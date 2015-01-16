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

namespace phparia\Events;

use phparia\Client\Client;
use phparia\Resources\Bridge;
use phparia\Resources\Channel;
use phparia\Resources\Endpoint;

/**
 * User-generated event with additional user-defined fields in the object.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelUserevent extends Event
{
    /**
     * @var Bridge (optional) - A bridge that is signaled with the user event. 
     */
    private $bridge;

    /**
     * @var Channel (optional) - A channel that is signaled with the user event.
     */
    private $channel;

    /**
     * @var Endpoint (optional) - A endpoint that is signaled with the user event.
     */
    private $endpoint;

    /**
     * @var string The name of the user event. 
     */
    private $eventname;

    /**
     * @var object Custom Userevent data
     */
    private $userevent;

    /**
     * @return Bridge (optional) - A bridge that is signaled with the user event.
     */
    public function getBridge()
    {
        return $this->bridge;
    }

    /**
     * @return Channel (optional) - A channel that is signaled with the user event.
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return Endpoint (optional) - A endpoint that is signaled with the user event.
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return string The name of the user event.
     */
    public function getEventname()
    {
        return $this->eventname;
    }

    /**
     * @return onject Custom Userevent data
     */
    public function getUserevent()
    {
        return $this->userevent;
    }

    /**
     * @param Client $client
     * @param string $response
     */
    public function __construct(Client $client, $response)
    {
        parent::__construct($client, $response);

        $this->bridge = property_exists($this->response, 'bridge') ? new Bridge($client, $this->response->bridge) : null;
        $this->channel = property_exists($this->response, 'channel') ? new Channel($client, $this->response->channel) : null;
        $this->endpoint = property_exists($this->response, 'endpoint') ? new Endpoint($client, $this->response->endpoint) : null;
        $this->eventname = $this->response->eventname;
        $this->userevent = $this->response->userevent;
    }

}
