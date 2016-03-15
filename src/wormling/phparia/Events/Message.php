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

use phparia\Resources\Response;

/**
 * Base type for errors and events
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Message extends Response implements MessageInterface
{
    /**
     * @var string Indicates the type of this message.
     */
    private $type;

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $response The raw json response message data from ARI
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->type = $this->getResponseValue('type');
    }
}
