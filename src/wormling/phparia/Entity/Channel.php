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

use DateTime;

/**
 * A specific communication connection between Asterisk and an Endpoint.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Channel extends Response
{
    /**
     * @var string 
     */
    private $accountCode;

    /**
     * @var CallerId
     */
    private $caller;

    /**
     * @var CallerId 
     */
    private $connected;

    /**
     * @var DateTime 
     */
    private $creationTime;

    /**
     * @var DialplanCep 
     */
    private $dialplan;

    /**
     * @var string Unique identifier of the channel.  This is the same as the Uniqueid field in AMI.
     */
    private $id;

    /**
     * @var string Name of the channel (i.e. SIP/foo-0000a7e3) 
     */
    private $name;

    /**
     * @var string 
     */
    private $state;

    /**
     * @return string
     */
    public function getAccountCode()
    {
        return $this->accountCode;
    }

    /**
     * @return CallerId Caller identification
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @return CallerId Connected caller identification
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return DialplanCep Dialplan location (context/extension/priority)
     */
    public function getDialplan()
    {
        return $this->dialplan;
    }

    /**
     * @return string Unique identifier of the channel.  This is the same as the Uniqueid field in AMI.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string Name of the channel (i.e. SIP/foo-0000a7e3) 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);
        
        $this->accountCode = property_exists($this->response, 'account_code') ? $this->response->account_code : null;
        $this->caller = $this->response->caller;
        $this->connected = $this->response->connected;
        $this->creationTime = $this->response->creationtime;
        $this->dialplan = $this->response->dialplan;
        $this->id = $this->response->id;
        $this->name = $this->response->name;
        $this->state = $this->response->state;
    }

}
