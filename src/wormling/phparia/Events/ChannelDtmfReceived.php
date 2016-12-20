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
 * DTMF received on a channel.
 * This event is sent when the DTMF ends. There is no notification about the start of DTMF
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelDtmfReceived extends Event implements IdentifiableEventInterface
{
    /**
     * @var Channel The channel on which DTMF was received
     */
    private $channel;

    /**
     * @var string DTMF digit received (0-9, A-E, # or *)
     */
    private $digit;

    /**
     * @var int Number of milliseconds DTMF was received
     */
    private $durationMs;

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string DTMF digit received (0-9, A-E, # or *)
     */
    public function getDigit()
    {
        return $this->digit;
    }

    /**
     * @return int Number of milliseconds DTMF was received
     */
    public function getDurationMs()
    {
        return $this->durationMs;
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

        $this->channel = $this->getResponseValue('channel', '\phparia\Resources\Channel', $client);
        $this->digit = $this->getResponseValue('digit');
        $this->durationMs = $this->getResponseValue('duration_ms');
    }

}
