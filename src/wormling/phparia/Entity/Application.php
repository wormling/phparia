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
 * Details of a Stasis application
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Application extends Response
{
    /**
     * @var array Id's for bridges subscribed to. 
     */
    private $bridgeIds;

    /**
     * @var array Id's for channels subscribed to.
     */
    private $channelIds;

    /**
     * @var array Names of the devices subscribed to.
     */
    private $deviceNames;

    /**
     * @var array {tech}/{resource} for endpoints subscribed to. 
     */
    private $endpointIds;

    /**
     * @var string Name of this application 
     */
    private $name;

    /**
     * @return array Id's for bridges subscribed to. 
     */
    public function getBridgeIds()
    {
        return $this->bridgeIds;
    }

    /**
     * @return array Id's for channels subscribed to.
     */
    public function getChannelIds()
    {
        return $this->channelIds;
    }

    /**
     * @return array Names of the devices subscribed to.
     */
    public function getDeviceNames()
    {
        return $this->deviceNames;
    }

    /**
     * @return array {tech}/{resource} for endpoints subscribed to. 
     */
    public function getEndpointIds()
    {
        return $this->endpointIds;
    }

    /**
     * @return string string Name of this application 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);
        
        $this->bridgeIds = $this->response->bridge_ids;
        $this->channelIds = $this->response->channel_ids;
        $this->deviceNames = $this->response->device_names;
        $this->endpointIds = $this->response->endpoint_ids;
        $this->name = $this->response->name;
    }

}
