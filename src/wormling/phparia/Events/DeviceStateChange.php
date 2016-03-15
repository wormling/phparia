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
use phparia\Resources\DeviceState;

/**
 * Notification that a device state has changed.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class DeviceStateChange extends Event
{
    /**
     * @var DeviceState
     */
    private $deviceState;

    /**
     * @return DeviceState
     */
    public function getDeviceState()
    {
        return $this->deviceState;
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->deviceState = $this->getResponseValue('device_state', '\phparia\Resources\DeviceState', $client);
    }
}
