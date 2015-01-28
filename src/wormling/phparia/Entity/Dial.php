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
 * Dialing state has changed.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Dial extends Event
{
    /**
     * @var Channel (optional) - The channel on which the variable was set. If missing, the variable is a global variable.
     */
    private $channel;

    /**
     * @var string Current status of the dialing attempt to the peer.
     */
    private $dialstatus;

    /**
     * @var string (optional) - The dial string for calling the peer channel.
     */
    private $dialstring;
    
    /**
     * @var string (optional) - Forwarding target requested by the original dialed channel. 
     */
    private $forward;
    
    /**
     * @var Channel (optional) - Channel that the caller has been forwarded to.  
     */
    private $forwarded;
    
    /**
     * @var Channel The dialed channel.
     */
    private $peer;

    /**
     * @return Channel (optional) - The channel on which the variable was set. If missing, the variable is a global variable.
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string Current status of the dialing attempt to the peer.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string (optional) - The dial string for calling the peer channel.
     */
    public function getVariable()
    {
        return $this->variable;
    }
    
    /**
     * @return string (optional) - Forwarding target requested by the original dialed channel. 
     */
    public function getForward()
    {
        return $this->forward;
    }
    
    /**
     * @return Channel (optional) - Forwarding target requested by the original dialed channel. 
     */
    public function getForwarded()
    {
        return $this->forwarded;
    }
    
    /**
     * @return Channel The dialed channel.
     */
    public function getPeer()
    {
        return $this->peer;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);

        $this->channel = property_exists($this->response, 'channel') ? $this->response->channel : null;
        $this->dialstatus = $this->response->dialstatus;
        $this->dialstring = property_exists($this->response, 'dialstring') ? $this->response->dialstring : null;
        $this->forward = property_exists($this->response, 'forward') ? $this->response->forward : null;
        $this->forwarded = property_exists($this->response, 'forwarded') ? $this->response->forwarded : null;
        $this->peer = $this->response->peer;
    }

}
