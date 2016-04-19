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

use GuzzleHttp\Exception\RequestException;
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
        $uri = 'applications';
        $response = $this->client->getEndpoint()->get($uri);

        $applications = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $application) {
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
        $uri = "applications/$applicationName";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Application($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Subscribe an application to a event source. Returns the state of the application after the subscriptions have changed
     *
     * @param string $applicationName Application's name
     * @param string $eventSource (required) URI for event source (channel:{channelId}, bridge:{bridgeId}, endpoint:{tech}[/{resource}], deviceState:{deviceName}.  Allows comma separated values.
     * @return Application
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws UnprocessableEntityException Event source does not exist.
     */
    public function subscribe($applicationName, $eventSource)
    {
        $uri = "applications/$applicationName/subscription";
        try {
            $response = $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'eventSource' => $eventSource,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Application($this->client, \GuzzleHttp\json_decode($response->getBody()));
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
        $uri = "applications/$applicationName/subscription?eventSource=".\GuzzleHttp\json_encode($eventSource);
        try {
            $response = $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Application($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }
}
