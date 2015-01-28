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
 * The merging of media from one or more channels.
 * Everyone on the bridge receives the same audio.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Bridge extends Response
{
    /**
     * @var string Bridging class
     */
    private $bridgeClass;
    
    /**
     * @var string Type of bridge technology
     */
    private $bridgeType;
    
    /**
     * @var array Ids of channels participating in this bridge
     */
    private $channels;
    
    /**
     * @var string  Entity that created the bridge
     */
    private $creator;
    
    /**
     * @var string Unique identifier for this bridge
     */
    private $id;
    
    /**
     * @var string Unique identifier for this bridge
     */
    private $name;
    
    /**
     * @var string Name of the current bridging technology 
     */
    private $technology;
    
    /**
     * @return string Bridging class
     */
    public function getBridgeClass()
    {
        return $this->bridgeClass;
    }

    /**
     * @return type Type of bridge technology
     */
    public function getBridgeType()
    {
        return $this->bridgeType;
    }

    /**
     * @return array Ids of channels participating in this bridge 
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @return string Entity that created the bridge
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return string Unique identifier for this bridge
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string Unique identifier for this bridge
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Name of the current bridging technology 
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
        
        $this->bridgeClass = $this->response->bridge_class;
        $this->bridgeType = $this->response->bridge_type;
        $this->channels = $this->response->channels;
        $this->creator = $this->response->creator;
        $this->id = $this->response->id;
        $this->name = $this->response->name;
        $this->technology->technology;
    }
}
