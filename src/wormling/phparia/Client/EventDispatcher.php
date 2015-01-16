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

/**
 * Very basic event dispatcher for ARI
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class EventDispatcher
{
    protected $listeners;

    public function __construct()
    {
        $this->listeners = [];
    }

    /**
     * @param string $eventName
     * @param mixed $event
     */
    public function dispatch($eventName, $event)
    {
        foreach ($this->listeners as $listener) {
            if ($listener['eventName'] === $eventName) {
                call_user_func($listener['callback'], $event);
            }
        }
    }

    /**
     * @param string $eventName
     * @param \Closure $callback
     */
    public function addListener($eventName, \Closure $callback)
    {
        $this->listeners[] = array('eventName' => $eventName, 'callback' => $callback);
    }

    /**
     * @param string $eventName
     * @param \Closure $callback
     */
    public function removeListener($eventName, \Closure $callback)
    {
        foreach ($this->listeners as $key => $listener) {
            if ($listener['eventName'] === $eventName && $listener['callback'] === $callback) {
                unset($this->listeners[$key]);
            }
        }
    }

}
