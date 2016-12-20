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

namespace phparia\Resources;

use phparia\Client\AriClient;
use phparia\Events\Event;

/**
 * Represents the state of a device.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class DeviceState extends Resource
{
    /**
     * @var string Name of the device.
     */
    private $name;

    /**
     * @var string Device's state
     */
    private $state;

    /**
     * @return string Name of the device.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Device's state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param callable $callback
     */
    public function onDeviceStateChange(callable $callback)
    {
        $this->on(Event::DEVICE_STATE_CHANGE.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceDeviceStateChange(callable $callback)
    {
        $this->once(Event::DEVICE_STATE_CHANGE.'_'.$this->getName(), $callback);
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->name = $this->getResponseValue('name');
        $this->state = $this->getResponseValue('state');
    }

}
