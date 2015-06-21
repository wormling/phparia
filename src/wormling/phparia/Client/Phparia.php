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
use phparia\Events\Event;
use React\EventLoop\LoopInterface;
use Zend\Log\LoggerInterface;

class Phparia
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
     * @var AriClient
     */
    protected $ariClient;

    /**
     * @var AmiClient
     */
    protected $amiClient;

    /**
     * @var string
     */
    protected $stasisApplicationName;

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

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Connect to ARI and optionally AMI
     *
     * @param string $ariAddress
     * @param string|null $amiAddress
     */
    public function connect($ariAddress, $amiAddress = null)
    {
        $this->eventLoop = \React\EventLoop\Factory::create();
        $this->ariClient = new AriClient($this->eventLoop, $this->logger);
        $this->ariClient->connect($ariAddress);
        $this->wsClient = $this->ariClient->getWsClient();
        $this->stasisApplicationName = $this->ariClient->getStasisApplicationName();

        if ($amiAddress !== null) {
            $this->amiClient = new AmiClient($this->ariClient->getWsClient(), $this->eventLoop, $this->logger);
            $this->amiClient->connect($amiAddress);
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
        $this->wsClient->getStasisClient()->on(Event::STASIS_END, $callback);
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
     * @return AriClient
     */
    public function getAriClient()
    {
        return $this->ariClient;
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