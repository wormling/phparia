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

use phparia\Client\Client;

/**
 * Base type for API call responses
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Resource
{
    /**
     * Spot to store event callbacks so we can unregister them from the destructor
     * 
     * @var array
     */
    protected $callbacks = array();

    /**
     * @var Client 
     */
    protected $client;

    /**
     * The json_decoded message data from ARI
     * 
     * @var object
     */
    protected $response;

    /**
     * Replaces any duplicate once event handler
     * 
     * @param string $event
     * @param \phparia\Resources\callable $callback
     */
    protected function on($event, callable $callback)
    {
        $this->callbacks[$event] = $callback;
        $this->client->getStasisClient()->on($event, $callback);
    }

    /**
     * Replaces any duplicate on event handler
     * 
     * @param string $event
     * @param \phparia\Resources\callable $callback
     */
    protected function once($event, callable $callback)
    {
        $this->callbacks[$event] = $callback;
        $this->client->getStasisClient()->once($event, $callback);
    }

    /**
     * @param string $response The raw json response response data from ARI
     */
    public function __construct(Client $client, $response)
    {
        $this->client = $client;

        if (is_array($response)) { // For some reson, playback is an array, so this fixes that problem
            $this->response = $object = json_decode(json_encode($response), false);
        } elseif (is_object($response)) {
            $this->response = $response;
        } else {
            $this->response = json_decode($response);
        }
    }

    public function __destruct()
    {
        foreach ($this->callbacks as $event => $callback) {
            try {
                if ($this->client->getStasisClient()->listeners($event)) {
                    $this->client->getStasisClient()->removeAllListeners($event);
                }
            } catch (\Exception $ignore) {
                
            }
        }
    }

}
