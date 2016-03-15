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

use phparia\Client\AriClient;

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
     * @return mixed Custom Userevent data
     */
    public function getUserevent()
    {
        return $this->userevent;
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->bridge = $this->getResponseValue('bridge', '\phparia\Resources\Bridge', $client);
        $this->channel = $this->getResponseValue('channel', '\phparia\Resources\Channel', $client);
        $this->endpoint = $this->getResponseValue('endpoint', '\phparia\Resources\Endpoint', $client);
        $this->eventname = $this->getResponseValue('eventname');
        $this->userevent = $this->getResponseValue('userevent');
    }
}
