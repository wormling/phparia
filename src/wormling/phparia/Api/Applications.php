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

namespace phparia\Api;

use Pest_BadRequest;
use Pest_Conflict;
use Pest_InvalidRecord;
use Pest_NotFound;
use phparia\Client\AriClientAware;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\UnprocessableEntityException;
use phparia\Resources\Application;

/**
 * Applications API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Applications extends AriClientAware
{

    /**
     * List all getApplications.
     *
     * @return Application[]
     */
    public function getApplications()
    {
        $uri = '/applications';
        $response = $this->client->getEndpoint()->get($uri);

        $applications = [];
        foreach ((array)$response as $application) {
            $applications[] = new Application($this->client, $application);
        }

        return $applications;
    }

    /**
     * Get details of an application.
     *
     * @param $applicationName
     * @return Application
     * @throws NotFoundException
     */
    public function getApplication($applicationName)
    {
        $uri = "/applications/$applicationName";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (Pest_NotFound $e) { // Application does not exist.
            throw new NotFoundException($e);
        }

        return new Application($this->client, $response);
    }

    /**
     * Subscribe an application to a event source. Returns the state of the application after the subscriptions have changed
     *
     * @param string $applicationName Application's name
     * @param string $eventSource (required) URI for event source (channel:{channelId}, bridge:{bridgeId}, endpoint:{tech}[/{resource}], deviceState:{deviceName}.  Allows comma separated values.
     * @return Application
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws UnprocessableEntityException
     */
    public function subscribe($applicationName, $eventSource)
    {
        $uri = "/applications/$applicationName/subscription";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'eventSource' => $eventSource,
            ));
        } catch (Pest_BadRequest $e) { // Missing parameter.
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Application does not exist.
            throw new NotFoundException($e);
        } catch (Pest_InvalidRecord $e) { // Event source does not exist.
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
     * @throws ConflictException
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws UnprocessableEntityException
     */
    public function unsubscribe($applicationName, $eventSource)
    {
        $uri = "/applications/$applicationName/subscription?eventSource=".$this->client->getEndpoint()->jsonEncode($eventSource);
        try {
            $response = $this->client->getEndpoint()->delete($uri);
        } catch (Pest_BadRequest $e) { // Missing parameter; event source scheme not recognized.
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Application does not exist.
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Application not subscribed to event source.
            throw new ConflictException($e);
        } catch (Pest_InvalidRecord $e) { // Event source does not exist.
            throw new UnprocessableEntityException($e);
        }

        return new Application($this->client, $response);
    }

}
