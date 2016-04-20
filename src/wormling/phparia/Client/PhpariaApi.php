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
use React\EventLoop;

/**
 * Class PhpariaApi
 *
 * Just a helper class for getting api endpoints.
 *
 * @package phparia\Client
 */
class PhpariaApi
{
    /**
     * PhpariaApi constructor.
     * @param AriClient $ariClient
     */
    public function __construct(AriClient $ariClient)
    {
        $this->ariClient = $ariClient;
    }

    /**
     * @var AriClient
     */
    protected $ariClient;

    /**
     * @return AriClient
     */
    public function getAriClient()
    {
        return $this->ariClient;
    }

    /**
     * @return Applications
     */
    public function applications()
    {
        return $this->getAriClient()->applications();
    }

    /**
     * @return Asterisk
     */
    public function asterisk()
    {
        return $this->getAriClient()->asterisk();
    }

    /**
     * @return Bridges
     */
    public function bridges()
    {
        return $this->getAriClient()->bridges();
    }

    /**
     * @return Channels
     */
    public function channels()
    {
        return $this->getAriClient()->channels();
    }

    /**
     * @return DeviceStates
     */
    public function deviceStates()
    {
        return $this->getAriClient()->deviceStates();
    }

    /**
     * @return Endpoints
     */
    public function endPoints()
    {
        return $this->getAriClient()->endPoints();
    }

    /**
     * @return Events
     */
    public function events()
    {
        return $this->getAriClient()->events();
    }

    /**
     * @return Mailboxes
     */
    public function mailboxes()
    {
        return $this->getAriClient()->mailboxes();
    }

    /**
     * @return Playbacks
     */
    public function playbacks()
    {
        return $this->getAriClient()->playbacks();
    }

    /**
     * @return Recordings
     */
    public function recordings()
    {
        return $this->getAriClient()->recordings();
    }

    /**
     * @return Sounds
     */
    public function sounds()
    {
        return $this->getAriClient()->sounds();
    }
}