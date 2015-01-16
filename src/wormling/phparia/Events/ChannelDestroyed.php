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

use phparia\Client\Client;
use phparia\Resources\Channel;

/**
 * Notification that a channel has been destroyed.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelDestroyed extends Event implements IdentifiableEventInterface
{
    /**
     * @var int Integer representation of the cause of the hangup 
     */
    private $cause;

    /**
     * @var string Text representation of the cause of the hangup 
     */
    private $causeTxt;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @return int Integer representation of the cause of the hangup 
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return string Text representation of the cause of the hangup
     */
    public function getCauseTxt()
    {
        return $this->causeTxt;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function getEventId()
    {
        return "{$this->getType()}_{$this->getChannel()->getId()}";
    }

    /**
     * @param Client $client
     * @param string $response
     */
    public function __construct(Client $client, $response)
    {
        parent::__construct($client, $response);

        $this->cause = $this->response->cause;
        $this->causeTxt = $this->response->cause_txt;
        $this->channel = new Channel($client, $this->response->channel);
    }

}
