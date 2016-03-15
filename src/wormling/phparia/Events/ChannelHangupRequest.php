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
 * A hangup was requested on the channel.
 *
 * @author Brian Smith <wormling@gmail.com>
 * @todo Add cause constants
 */
class ChannelHangupRequest extends Event implements IdentifiableEventInterface
{
    /**
     * @var int (optional) - Integer representation of the cause of the hangup.
     */
    private $cause;

    /**
     * @var \phparia\Resources\Channel The channel on which the hangup was requested.
     */
    private $channel;

    /**
     * @var boolean (optional) - Whether the hangup request was a soft hangup request.
     */
    private $soft;

    /**
     * @return int
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return boolean
     */
    public function getSoft()
    {
        return $this->soft;
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

        $this->cause = $this->getResponseValue('cause');
        $this->channel = $this->getResponseValue('channel', '\phparia\Resources\Channel', $client);
        $this->soft = $this->getResponseValue('soft');
    }
}
