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
use Devristo\Phpws\Messaging\WebSocketMessage;
use GuzzleHttp\Client;
use phparia\Api\Applications;
use phparia\Api\Asterisk;
use phparia\Api\Bridges;
use phparia\Api\Channels;
use phparia\Api\DeviceStates;
use phparia\Api\Endpoints;
use phparia\Api\Events;
use phparia\Api\Mailboxes;
use phparia\Api\Playbacks;
use phparia\Api\Recordings;
use phparia\Api\Sounds;
use phparia\Events\IdentifiableEventInterface;
use phparia\Events\Message;
use React\EventLoop\LoopInterface;
use Zend\Log\LoggerInterface;

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
     * @var Client
     */
    protected $endpoint;

    /**
     * @var Applications
     */
    protected $applications;

    /**
     * @var Asterisk
     */
    protected $asterisk;

    /**
     * @var Bridges
     */
    protected $bridges;

    /**
     * @var Channels
     */
    protected $channels;

    /**
     * @var DeviceStates
     */
    protected $deviceStates;

    /**
     * @var Endpoints
     */
    protected $endPoints;

    /**
     * @var Events
     */
    protected $events;

    /**
     * @var Mailboxes
     */
    protected $mailboxes;

    /**
     * @var Playbacks
     */
    protected $playbacks;

    /**
     * @var Recordings
     */
    protected $recordings;

    /**
     * @var Sounds
     */
    protected $sounds;

    public function __construct(LoopInterface $eventLoop, LoggerInterface $logger)
    {
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Connect to ARI.
     *
     * @param string $address Example ws://localhost:8088/ari/events?api_key=username:password&app=stasis_app_name
     * @param array $streamOptions Such as ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]];
     * @param array $httpOptions Such as ['verify' => false];
     */
    public function connect($address, array $streamOptions = [], $httpOptions = [])
    {
        $components = parse_url($address);
        $scheme = $components['scheme'];
        $host = $components['host'];
        $port = $components['port'];
        $path = $components['path'];
        $query = $components['query'];
        $queryParts = [];
        parse_str($query, $queryParts);

        $this->stasisApplicationName = $queryParts['app'];
        $apiKey = $queryParts['api_key'];
        list($username, $password) = explode(':', $apiKey);

        $config = [
            'base_uri' => ($scheme === 'wss' ? 'https://' : 'http://').$host.':'.$port.dirname($path).'/',
            'auth' => [$username, $password],
            'verify' => false
        ];
        $config = array_merge($config, $httpOptions);
        
        $this->endpoint = new Client($config);

        $this->wsClient = new WebSocket($address, $this->eventLoop, $this->logger, $streamOptions);

        $this->wsClient->on("message", function (WebSocketMessage $rawMessage) {
            $message = new Message($rawMessage->getData());

            $eventType = '\\phparia\\Events\\'.$message->getType();
            if (class_exists($eventType)) {
                $event = new $eventType($this, $rawMessage->getData());
            } else {
                $this->logger->warn("Event: '$eventType' not implemented");

                // @todo Create a generic event for any that are not implemented

                return;
            }

            // Emit the specific event (just to get it back to where it came from)
            if ($event instanceof IdentifiableEventInterface) {
                $this->logger->notice("Emitting ID event: {$event->getEventId()}");
                $this->wsClient->emit($event->getEventId(), array('event' => $event));
            }

            // Emit the general event
            $this->logger->notice("Emitting event: {$message->getType()}");
            $this->wsClient->emit($message->getType(), array('event' => $event));
        });
    }

    /**
     * Headers will be passed to the provided callback on request
     *
     * @param callable $callback
     */
    public function onRequest(callable $callback)
    {
        $this->wsClient->on("request", $callback);
    }

    /**
     * Handshake will be passed to the provide callback on handshake
     *
     * @param callable $callback
     */
    public function onHandshake(callable $callback)
    {
        $this->wsClient->on("handshake", $callback);
    }

    /**
     * @param callable|callable $callback
     */
    public function onConnect(callable $callback)
    {
        $this->wsClient->on("connect", $callback);
    }

    /**
     * @param callable|callable $callback
     */
    public function onClose(callable $callback)
    {
        $this->wsClient->on("close", $callback);
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
     * @return Client
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return Applications
     */
    public function applications()
    {
        if (!$this->applications instanceof Applications) {
            $this->applications = new Applications($this);
        }

        return $this->applications;
    }

    /**
     * @return Asterisk
     */
    public function asterisk()
    {
        if (!$this->asterisk instanceof Asterisk) {
            $this->asterisk = new Asterisk($this);
        }

        return $this->asterisk;
    }

    /**
     * @return Bridges
     */
    public function bridges()
    {
        if (!$this->bridges instanceof Bridges) {
            $this->bridges = new Bridges($this);
        }

        return $this->bridges;
    }

    /**
     * @return Channels
     */
    public function channels()
    {
        if (!$this->channels instanceof Channels) {
            $this->channels = new Channels($this);
        }

        return $this->channels;
    }

    /**
     * @return DeviceStates
     */
    public function deviceStates()
    {
        if (!$this->deviceStates instanceof DeviceStates) {
            $this->deviceStates = new DeviceStates($this);
        }

        return $this->deviceStates;
    }

    /**
     * @return Endpoints
     */
    public function endPoints()
    {
        if (!$this->endPoints instanceof Endpoints) {
            $this->endPoints = new Endpoints($this);
        }

        return $this->endPoints;
    }

    /**
     * @return Events
     */
    public function events()
    {
        if (!$this->events instanceof Events) {
            $this->events = new Events($this);
        }

        return $this->events;
    }

    /**
     * @return Mailboxes
     */
    public function mailboxes()
    {
        if (!$this->mailboxes instanceof Mailboxes) {
            $this->mailboxes = new Mailboxes($this);
        }

        return $this->mailboxes;
    }

    /**
     * @return Playbacks
     */
    public function playbacks()
    {
        if (!$this->playbacks instanceof Playbacks) {
            $this->playbacks = new Playbacks($this);
        }

        return $this->playbacks;
    }

    /**
     * @return Recordings
     */
    public function recordings()
    {
        if (!$this->recordings instanceof Recordings) {
            $this->recordings = new Recordings($this);
        }

        return $this->recordings;
    }

    /**
     * @return Sounds
     */
    public function sounds()
    {
        if (!$this->sounds instanceof Sounds) {
            $this->sounds = new Sounds($this);
        }

        return $this->sounds;
    }

}
