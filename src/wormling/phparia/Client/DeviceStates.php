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

use Pest_Conflict;
use Pest_NotFound;
use phparia\Exception\ConflictException;
use phparia\Exception\NotFoundException;
use phparia\Resources\DeviceState;

/**
 * DeviceStates API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class DeviceStates extends Base
{

    /**
     * List all ARI controlled device states.
     * 
     * @return DeviceState[]
     */
    public function deviceStates()
    {
        $uri = '/deviceStates';
        $response = $this->client->getEndpoint()->get($uri);

        $deviceStates = [];
        foreach ((array)$response as $deviceState) {
            $deviceStates[] = new DeviceState($this->client, $deviceState);
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
        $response = $this->client->getEndpoint()->get($uri);

        return new DeviceState($this->client, $response);
    }

    /**
     * Change the state of a device controlled by ARI. (Note - implicitly creates the device state).
     *
     * @param string $deviceName Name of the device
     * @param string $deviceState (required) Device state value
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function updateDeviceState($deviceName, $deviceState)
    {
        $uri = "/deviceStates/$deviceName";
        try {
            $this->client->getEndpoint()->put($uri, array(
                'deviceState' => $deviceState,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Destroy a device-state controlled by ARI.
     *
     * @param string $deviceName Name of the device
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function deleteDeviceState($deviceName)
    {
        $uri = "/deviceStates/$deviceName";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

}
