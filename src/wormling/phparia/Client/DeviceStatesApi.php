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

use phparia\Entity\DeviceState;

/**
 * DeviceStates API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class DeviceStatesApi
{

    /**
     * List all ARI controlled device states.
     * 
     * @return DeviceState[]
     */
    public function getDeviceStates()
    {
        $uri = '/deviceStates';
        $response = $this->client->getAriEndpoint()->get($uri);

        $deviceStates = [];
        foreach ($response as $deviceState) {
            $deviceStates[] = new DeviceState($deviceState);
        }

        return $deviceStates;
    }

    /**
     * Retrieve the current state of a device.
     * 
     * @param string $deviceName
     * @return DeviceState
     */
    public function getDeviceState($deviceName)
    {
        $uri = "/deviceStates/$deviceName";
        $response = $this->client->getAriEndpoint()->get($uri);

        return new DeviceState($response);
    }

    /**
     * Change the state of a device controlled by ARI. (Note - implicitly creates the device state).
     * 
     * @param string $deviceName Name of the device
     * @param string $deviceState (required) Device state value
     */
    public function updateDeviceState($deviceName, $deviceState)
    {
        $uri = "/deviceStates/$deviceName";
        $this->client->getAriEndpoint()->put($uri, array(
            'deviceState' => $deviceState,
        ));
    }

    /**
     * Destroy a device-state controlled by ARI.
     * 
     * @param string $deviceName Name of the device
     */
    public function deleteDeviceState($deviceName)
    {
        $uri = "/deviceStates/$deviceName";
        $this->client->getAriEndpoint()->delete($uri);
    }

}
