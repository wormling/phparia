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

/**
 * Base type ARI resources
 *
 * The event code is a wrapper to isolate the listeners for the particular resource
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Resource extends Response
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var AriClient
     */
    protected $client;

    /**
     * @param string $event
     * @param callable $listener
     */
    public function on($event, callable $listener)
    {
        $this->listeners[$event][] = $listener;
        $this->client->getWsClient()->on($event, $listener);
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function once($event, callable $listener)
    {
        $onceListener = function () use (&$onceListener, $event, $listener) {
            $this->removeListener($event, $onceListener);

            call_user_func_array($listener, func_get_args());
        };

        $this->on($event, $onceListener);
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function removeListener($event, callable $listener)
    {
        if (isset($this->listeners[$event])) {
            if (false !== $index = array_search($listener, $this->listeners[$event], true)) {
                unset($this->listeners[$event][$index]);
                $this->client->getWsClient()->removeListener($event, $listener);
            }
        }
    }

    /**
     * @param string $event
     */
    public function removeAllListeners($event = null)
    {
        if ($event !== null) {
            if (isset($this->listeners[$event])) {
                unset($this->listeners[$event]);
                $this->client->getWsClient()->removeAllListeners($event);
            }
        } else {
            foreach ($this->listeners as $event => $listeners) {
                $this->client->getWsClient()->removeAllListeners($event);
            }
            $this->listeners = [];
        }
    }

    /**
     * @param string $event
     * @return array
     */
    public function listeners($event)
    {
        return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
    }

    /**
     * @param AriClient $client
     * @param string $response The raw json response response data from ARI
     */
    public function __construct(AriClient $client, $response)
    {
        $this->client = $client;

        parent::__construct($response);
    }

    public function __destruct()
    {
        $this->removeAllListeners();
    }

}
