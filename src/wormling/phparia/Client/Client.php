<?php

/*
 * Copyright 2014 Brian Smith <wormling@gmail.com>.
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

/**
 * phparia client
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Client
{
    /**
     * @var \Devristo\Phpws\Client\WebSocket 
     */
    protected $stasisClient;

    /**
     * @var \PestJSON 
     */
    protected $ariEndpoint;

    /**
     * @var \React\EventLoop 
     */
    protected $stasisLoop;

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

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
     * @param string $ariUsername
     * @param string $ariPassword
     * @param string $stasisApplication
     * @param string $ariServer
     * @param int $ariPort
     * @param string $ariEndpoint
     */
    public function __construct($ariUsername = null, $ariPassword = null, $stasisApplication = null, $ariServer = '127.0.0.1', $ariPort = 8080, $ariEndpoint = '/ari')
    {
        $this->connect($ariUsername, $ariPassword, $stasisApplication, $ariServer, $ariPort, $ariEndpoint);
    }

    /**
     * @param string $ariUsername
     * @param string $ariPassword
     * @param string $stasisApplication
     * @param string $ariServer
     * @param string $ariPort
     * @param string $ariEndpoint
     */
    private function connect($ariUsername, $ariPassword, $stasisApplication, $ariServer = '127.0.0.1', $ariPort = '8088', $ariEndpoint = '')
    {
        $this->ariEndpoint = new \PestJSON('http://' . $ariServer . ':' . $ariPort . $ariEndpoint);
        $this->ariEndpoint->setupAuth($ariUsername, $ariPassword, 'basic');
        $this->stasisLoop = \React\EventLoop\Factory::create();
        $this->logger = new \Zend\Log\Logger();
        $this->logWriter = new \Zend\Log\Writer\Stream("php://output");
        $this->logger->addWriter($this->logWriter);
        $filter = new \Zend\Log\Filter\Priority(\Zend\Log\Logger::NOTICE);
        $this->logWriter->addFilter($filter);

        $this->stasisClient = new \Devristo\Phpws\Client\WebSocket('ws://' . $ariServer . ':' . $ariPort . '/ari/events?api_key=' . $ariUsername . ':' . $ariPassword . '&app=' . $stasisApplication, $this->stasisLoop, $this->logger);

        $this->stasisClient->on("request", function($headers) {
            $this->logger->notice("Request object created!");
        });

        $this->stasisClient->on("handshake", function() {
            $this->logger->notice("Handshake received!");
        });

        $this->stasisClient->on("message", function($rawMessage) {
            $message = new \phparia\Events\Message($rawMessage->getData());

            $eventType = '\\phparia\\Events\\' . $message->getType();
            $event = new $eventType($this, $rawMessage->getData());

            // Emit the specific event (just to get it back to where is came from)
            if ($event instanceof \phparia\Events\IdentifiableEventInterface) {
                $this->logger->notice("Emitting ID event: {$event->getEventId()}");
                $this->stasisClient->emit($event->getEventId(), array(
                    'event' => $event
                ));
            }

            // Emit the general event
            $this->logger->notice("Emitting    event: {$event->getType()}");
            $this->stasisClient->emit($message->getType(), array(
                'event' => $event
            ));

            $this->logger->debug("Got message: " . $rawMessage->getData());
        });
    }

    /**
     * Connect and start the stasis loop
     */
    public function run()
    {
        $this->stasisClient->open();

//        try {
        $this->stasisLoop->run();
//        } catch (\Exception $e) {
//            $this->logger->err("{$e->getTraceAsString()}");
//        }
    }

    /**
     * @return \Devristo\Phpws\Client\WebSocket 
     */
    public function getStasisClient()
    {
        return $this->stasisClient;
    }

    /**
     * @return \React\EventLoop 
     */
    public function getStasisLoop()
    {
        return $this->stasisLoop;
    }

    /**
     * @return \PestJSON
     */
    public function getAriEndpoint()
    {
        return $this->ariEndpoint;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        return $this->logger;
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
