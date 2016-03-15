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

use phparia\Client\AriClient;
use phparia\Resources\Channel;

/**
 * Notification that a channel has entered a Stasis application.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class StasisStart extends Event implements IdentifiableEventInterface
{
    /**
     * @var array Arguments to the application
     */
    private $args;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var Channel (optional)
     */
    private $replaceChannel;

    /**
     * @return array Arguments to the application
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return Channel (optional)
     */
    public function getReplaceChannel()
    {
        return $this->replaceChannel;
    }

    public function getEventId()
    {
        return "{$this->getType()}_{$this->getChannel()->getId()}";
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->args = $this->getResponseValue('args');
        $this->channel = $this->getResponseValue('channel', '\phparia\Resources\Channel', $client);
        $this->replaceChannel = $this->getResponseValue('replace_channel', '\phparia\Resources\Channel', $client);
    }
}
