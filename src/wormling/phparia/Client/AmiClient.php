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

use Clue\React\Ami\ActionSender;
use Clue\React\Ami\Client;
use Clue\React\Ami\Factory;
use Clue\React\Ami\Protocol\Event;
use Devristo\Phpws\Client\WebSocket;
use React\EventLoop\LoopInterface;
use Zend\Log\LoggerInterface;

/**
 *  AMI Client to get events not supported in ARI
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class AmiClient
{
    /**
     * @var Client
     */
    protected $amiClient = null;

    /**
     * @var WebSocket
     */
    protected $wsClient;

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ActionSender
     */
    protected $actionSender = null;

    public function __construct(WebSocket $wsClient, LoopInterface $eventLoop, LoggerInterface $logger)
    {
        $this->wsClient = $wsClient;
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Connect to AMI and start emitting events.
     *
     * @param string $address Example uaername:password@localhost:5038
     * @return \React\Promise\Promise
     */
    public function connect($address)
    {
        $factory = new Factory($this->eventLoop);

        return $factory->createClient($address)
            ->then(function (Client $client) {
                $this->amiClient = $client;
                $this->actionSender = new ActionSender($client);
                $this->actionSender->events(true);
                $client->on('close', function () {
                    $this->logger->debug('AMI connection closed');
                });
                $client->on('event', function (Event $event) {
                    $this->wsClient->emit($event->getName(), (array)$event);
                });
            }, function (\Exception $e) {
                $this->logger->err('Connection error: '.$e->getMessage());
            });
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->amiClient;
    }

    /**
     * @return ActionSender
     */
    public function getActionSender()
    {
        return $this->actionSender;
    }

}
