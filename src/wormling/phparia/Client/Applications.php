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

use phparia\Resources\Application;

/**
 * Applications API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Applications extends Base
{

    /**
     * List all applications.
     * 
     * @return Application[]
     */
    public function applications()
    {
        $uri = '/applications';
        $response = $this->client->getAriEndpoint()->get($uri);

        $applications = [];
        foreach ($response as $application) {
            $applications[] = new Application($this->client, $application);
        }

        return $applications;
    }

    /**
     * Get details of an application.
     * 
     * @param string $applicationName
     * @return Application
     */
    public function getApplication($applicationName)
    {
        $uri = "/applications/$applicationName";
        try {
            $response = $this->client->getAriEndpoint()->get($uri);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        }

        return new Application($this->client, $response);
    }

    /**
     * Subscribe an application to a event source. Returns the state of the application after the subscriptions have changed
     * 
     * @param string $applicationName Application's name
     * @param string $eventSource (required) URI for event source (channel:{channelId}, bridge:{bridgeId}, endpoint:{tech}[/{resource}], deviceState:{deviceName}  Allows comma separated values.
     * @return Application
     */
    public function subscribe($applicationName, $eventSource)
    {
        $uri = "/applications/$applicationName/subscription";
        try {
            $response = $this->client->getAriEndpoint()->post($uri, array(
                'eventSource' => $eventSource,
            ));
        } catch (Pest_BadRequest $e) { // Invalid parameters
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_InvalidRecord $e) { // Channel not in Stasis application
            throw new UnprocessableEntityException($e);
        }

        return new Application($this->client, $response);
    }

    /**
     * Unsubscribe an application from an event source. Returns the state of the application after the subscriptions have changed
     * 
     * @param string $applicationName Application's name
     * @param string $eventSource (required) URI for event source (channel:{channelId}, bridge:{bridgeId}, endpoint:{tech}[/{resource}], deviceState:{deviceName}  Allows comma separated values.
     * @return Application
     */
    public function unsubscribe($applicationName, $eventSource)
    {
        $uri = "/applications/$applicationName/subscription";
        try {
            $response = $this->client->getAriEndpoint()->delete($uri, array(
                'eventSource' => $eventSource,
            ));
        } catch (Pest_BadRequest $e) { // Invalid parameters
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Channel is not in a Stasis application; A recording with the same name already exists on the system and can not be overwritten because it is in progress or ifExists=fail
            throw new ConflictException($e);
        } catch (Pest_InvalidRecord $e) { // Channel not in Stasis application
            throw new UnprocessableEntityException($e);
        }

        return new Application($this->client, $response);
    }

}
