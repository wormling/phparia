<?php
/*
 * Copyright 2015 Brian Smith <wormling@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace phparia\Client;


use Devristo\Phpws\Client\WebSocket;
use GuzzleHttp\Promise\FulfilledPromise;
use phparia\Events\Event;
use React\EventLoop;
use React\Promise\Deferred;
use Zend\Log\LoggerInterface;

class Phparia extends PhpariaApi
{
    /**
     * @var WebSocket
     */
    protected $wsClient;

    /**
     * @var EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AmiClient
     */
    protected $amiClient;

    /**
     * @var string
     */
    protected $stasisApplicationName;

    /**
     * @var callable
     */
    protected $onStop;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->eventLoop = EventLoop\Factory::create();
        $ariClient = new AriClient($this->eventLoop, $this->logger);
        $this->onStop = function() {
            return new FulfilledPromise(null);
        };

        parent::__construct($ariClient);
    }

    /**
     * Connect to ARI and optionally AMI
     *
     * @param string $ariAddress
     * @param string|null $amiAddress
     * @param array $streamOptions Example: ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]];
     * @param array $clientOptions Example: ['verify' => false];
     */
    public function connect($ariAddress, $amiAddress = null, $streamOptions = [], $clientOptions = [])
    {
        $this->ariClient->connect($ariAddress, $streamOptions, $clientOptions);
        $this->wsClient = $this->ariClient->getWsClient();
        $this->stasisApplicationName = $this->ariClient->getStasisApplicationName();

        if ($amiAddress !== null) {
            $this->amiClient = new AmiClient($this->ariClient->getWsClient(), $this->eventLoop, $this->logger);
            $this->amiClient
                ->connect($amiAddress)
                ->done();
        }
    }

    /**
     * Connect and start the event loop
     */
    public function run()
    {
        $this->wsClient->open();
        $this->eventLoop->run();
    }

    /**
     * Disconnect and stop the event loop
     * @return \React\Promise\Promise|\React\Promise\PromiseInterface
     */
    public function stop()
    {
        $deferred = new Deferred();

        $onStop = $this->onStop;
        $onStop()
            ->then(function () use (&$deferred) {
                $this->ariClient->onClose(function () use (&$deferred) {
                    $this->eventLoop->stop();
                    $deferred->resolve();
                });
                $this->wsClient->close();
            });

        return $deferred->promise();
    }

    /**
     * @param callable|callback $callback Must return a promise
     */
    public function onStop($callback)
    {
        $this->onStop = $callback;
    }

    /**
     * @param callable|callable $callback
     */
    public function onStasisStart(callable $callback)
    {
        $this->wsClient->on(Event::STASIS_START, $callback);
    }

    /**
     * @param callable|callable $callback
     */
    public function onStasisEnd(callable $callback)
    {
        $this->wsClient->on(Event::STASIS_END, $callback);
    }

    /**
     * @return WebSocket
     */
    public function getWsClient()
    {
        return $this->wsClient;
    }

    /**
     * @return EventLoop\LoopInterface
     */
    public function getEventLoop()
    {
        return $this->eventLoop;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return AmiClient
     */
    public function getAmiClient()
    {
        return $this->amiClient;
    }

    /**
     * @return string
     */
    public function getStasisApplicationName()
    {
        return $this->stasisApplicationName;
    }
}