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
 * An external device that may offer/accept calls to/from Asterisk.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Endpoint extends Response
{
    /**
     * @var array Id's of channels associated with this endpoint 
     */
    private $channelIds;

    /**
     * @var string Identifier of the endpoint, specific to the given technology. 
     */
    private $resource;

    /**
     * @var string (optional) - Endpoint's state 
     */
    private $state;

    /**
     * @var string Technology of the endpoint 
     */
    private $technology;

    /**
     * @return array Id's of channels associated with this endpoint 
     */
    public function getChannelIds()
    {
        return $this->channelIds;
    }

    /**
     * @return string Identifier of the endpoint, specific to the given technology. 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string (optional) - Endpoint's state 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string Technology of the endpoint 
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);
        
        $this->channelIds = $this->response->channel_ids;
        $this->resource = $this->response->resource;
        $this->state = property_exists($this->response, 'state') ? $this->response->state : null;
        $this->technology = $this->response->technology;
    }

}
