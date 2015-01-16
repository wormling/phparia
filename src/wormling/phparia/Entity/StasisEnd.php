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

namespace phparia\Entity;

/**
 * Notification that a channel has left a Stasis application.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class StasisEnd extends Event
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);

        $this->channel = $this->response->channel;
    }

}
