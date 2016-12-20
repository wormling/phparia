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
 * Channel changed location in the dialplan.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelDialplan extends Event implements IdentifiableEventInterface
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var string The application about to be executed.
     */
    private $dialplanApp;

    /**
     * @var string The data to be passed to the application.
     */
    private $dialplanAppData;

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string The application about to be executed.
     */
    public function getDialplanApp()
    {
        return $this->dialplanApp;
    }

    /**
     * @return string The data to be passed to the application.
     */
    public function getDialplanAppData()
    {
        return $this->dialplanAppData;
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
        $this->dialplanApp = $this->getResponseValue('dialplan_app');
        $this->dialplanAppData = $this->getResponseValue('dialplan_app_data');
    }
}
