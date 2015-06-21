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
 * Dialing state has changed.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Dial extends Event
{
    /**
     * Valid dialstatus values
     */
    const DIALSTATUS_ANSWER = 'answer';
    const DIALSTATUS_BUSY = 'busy';
    const DIALSTATUS_NOANSWER = 'noanswer';
    const DIALSTATUS_CANCEL = 'cancel';
    const DIALSTATUS_CONGESTION = 'congestion';
    const DIALSTATUS_CHANUNAVAIL = 'chanunavail';
    const DIALSTATUS_DONTCALL = 'dontcall';
    const DIALSTATUS_TORTURE = 'torture';
    const DIALSTATUS_INVALIDARGS = 'invalidargs';

    /**
     * @var Channel (optional) - The channel on which the variable was set. If missing, the variable is a global variable.
     */
    private $caller;

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
     * @return Channel (optional) - The calling channel.
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @return string - Current status of the dialing attempt to the peer.
     */
    public function getDialstatus()
    {
        return $this->dialstatus;
    }

    /**
     * @return string (optional) - The dial string for calling the peer channel.
     */
    public function getDialstring()
    {
        return $this->dialstring;
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
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->caller = property_exists($this->response, 'channel') ? new Channel($client, $this->response->caller) : null;
        $this->dialstatus = $this->response->dialstatus;
        $this->dialstring = property_exists($this->response, 'dialstring') ? $this->response->dialstring : null;
        $this->forward = property_exists($this->response, 'forward') ? $this->response->forward : null;
        $this->forwarded = property_exists($this->response, 'forwarded') ? new Channel($client, $this->response->forwarded) : null;
        $this->peer = $this->response->peer;
    }

}
