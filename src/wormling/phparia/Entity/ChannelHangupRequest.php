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
 * A hangup was requested on the channel.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelHangupRequest extends \phparia\Entity\Event
{
    /**
     * @var int (optional) - Integer representation of the cause of the hangup. 
     */
    private $cause;
    
    /**
     * @var \phparia\Entity\Channel The channel on which the hangup was requested. 
     */
    private $channel;
    
    /**
     * @var boolean (optional) - Whether the hangup request was a soft hangup request. 
     */
    private $soft;
    
    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);
        
        $this->cause = property_exists($this->response, 'cause') ? $this->response->cause : null;
        $this->channel = new Channel($this->response->channel);
        $this->soft = property_exists($this->response, 'soft') ? $this->response->sort : null;
    }
}
