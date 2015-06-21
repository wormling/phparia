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
use phparia\Events\IdentifiableEventInterface;
use phparia\Events\Message;
use React\EventLoop\LoopInterface;
use Zend\Log\LoggerInterface;
use PestJSON;

/**
 * @author Brian Smith <wormling@gmail.com>
 */
class AriClient
{

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
     * @var string
     */
    protected $stasisApplicationName;

    /**
     * @var \PestJSON
     */
    protected $endpoint;

    public function __construct(LoopInterface $eventLoop, LoggerInterface $logger)
    {
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Connect to AMI and start emitting events.
     *
     * @param string $address Example ws://localhost:8088/ari/events?api_key=username:password&app=stasis_app_name
     */
    public function connect($address)
    {
        $components = parse_url($address);
        $scheme = $components['scheme'];
        $host = $components['host'];
        $port = $components['port'];
        $user = $components['user'];
        $pass = $components['pass'];
        $path = $components['path'];
        $query = $components['query'];
        $queryParts = [];
        parse_str($query, $queryParts);

        if (is_set($queryParts['app'])) {
            $this->stasisApplicationName = $queryParts['app'];
        } else {
            // throw exception
        }

        $this->endpoint = new PestJSON('http://'.$host.':'.$port.$path);
        $this->endpoint->setupAuth($user, $pass, 'basic');

        $this->wsClient = new WebSocket($address, $this->eventLoop, $this->logger);

        $this->wsClient->on("request", function ($headers) {
            $this->logger->notice("Request object created!");
        });

        $this->wsClient->on("handshake", function () {
            $this->logger->notice("Handshake received!");
        });

        $this->wsClient->on("message", function ($rawMessage) {
            $message = new Message($rawMessage->getData());

            $eventType = '\\phparia\\Events\\'.$message->getType();
            $event = new $eventType($this, $rawMessage->getData());

            // Emit the specific event (just to get it back to where is came from)
            if ($event instanceof IdentifiableEventInterface) {
                $this->logger->notice("Emitting ID event: {$event->getEventId()}");
                $this->wsClient->emit($event->getEventId(), array(
                    'event' => $event
                ));
            }

            // Emit the general event
            $this->logger->notice("Emitting    event: {$event->getType()}");
            $this->wsClient->emit($message->getType(), array(
                'event' => $event
            ));

            $this->logger->debug('Got message: '.$rawMessage->getData());
        });
    }

    /**
     * @return WebSocket
     */
    public function getWsClient()
    {
        return $this->wsClient;
    }

    /**
     * @return LoopInterface
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
     * @return string
     */
    public function getStasisApplicationName()
    {
        return $this->stasisApplicationName;
    }

    /**
     * @return PestJSON
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

}
