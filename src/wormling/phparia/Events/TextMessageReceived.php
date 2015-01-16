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
use phparia\Resources\Endpoint;
use phparia\Resources\TextMessage;

/**
 * A text message was received from an endpoint.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class TextMessageReceived extends Event
{
    /**
     * @var Endpoint  (optional)
     */
    private $endpoint;

    /**
     * @var TextMessage
     */
    private $message;

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return TextMessage
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param Client $client
     * @param string $response
     */
    public function __construct(Client $client, $response)
    {
        parent::__construct($client, $response);

        $this->endpoint = property_exists($this->response, 'endpoint') ? new Endpoint($client, $this->response->endpoint) : null;
        $this->message = new TextMessage($client, $this->response->message);
    }

}
