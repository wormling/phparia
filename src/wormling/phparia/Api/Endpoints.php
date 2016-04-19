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
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Resources\Endpoint;

/**
 * Endpoints API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Endpoints extends AriClientAware
{
    const AST_ENDPOINT_UNKNOWN = 'unknown';
    const AST_ENDPOINT_OFFLINE = 'offline';
    const AST_ENDPOINT_ONLINE = 'online';

    /**
     * List all endpoints.
     *
     * @return Endpoint
     */
    public function getEndpoints()
    {
        $uri = 'endpoints';
        $response = $this->client->getEndpoint()->get($uri);

        $endpoints = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $endpoint) {
            $endpoints[] = new Endpoint($this->client, $endpoint);
        }

        return $endpoints;
    }

    /**
     * Send a message to some technology URI or endpoint.
     *
     * @param string $to (required) The endpoint resource or technology specific URI to send the message to. Valid resources are sip, pjsip, and xmpp.
     * @param string $from (required) The endpoint resource or technology specific identity to send this message from. Valid resources are sip, pjsip, and xmpp.
     * @param string $body The body of the message
     * @param array $variables
     * @throws NotFoundException
     */
    public function sendMessage($to, $from, $body, $variables = array())
    {
        $uri = 'endpoints/sendMessage';
        try {
            $this->client->getEndpoint()->put($uri, [
                'form_params' => [
                    'to' => $to,
                    'from' => $from,
                    'body' => $body,
                    'variables' => array_map('strval', $variables),
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * List available endoints for a given endpoint technology.
     *
     * @param string $tech Technology of the endpoints (sip,iax2,...)
     * @return \phparia\Resources\Endpoint[]
     * @throws NotFoundException
     */
    public function getEndpointsByTech($tech)
    {
        $uri = "endpoints/$tech";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        $endpoints = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $endpoint) {
            $endpoints[] = new Endpoint($this->client, $endpoint);
        }

        return $endpoints;
    }

    /**
     * Details for an endpoint.
     *
     * @param string $tech Technology of the endpoint
     * @param string $resource ID of the endpoint
     * @return Endpoint
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function getEndpointByTechAndResource($tech, $resource)
    {
        $uri = "endpoints/$tech/$resource";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Endpoint($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Send a message to some endpoint in a technology.
     *
     * @param string $tech
     * @param $resource
     * @param string $from (required) The endpoint resource or technology specific identity to send this message from. Valid resources are sip, pjsip, and xmpp.
     * @param string $body The body of the message
     * @param array $variables
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @internal param $string @resource
     */
    public function sendMessageToEndpointAndTechAndResource($tech, $resource, $from, $body, $variables = array())
    {
        $uri = "endpoints/$tech/$resource/sendMessage";
        try {
            $this->client->getEndpoint()->put($uri, [
                'form_params' => [
                    'from' => $from,
                    'body' => $body,
                    'variables' => array_map('strval', $variables),
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }
}
