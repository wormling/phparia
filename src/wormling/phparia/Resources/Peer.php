<?php

/*
 * Copyright 2017 Brian Smith <wormling@gmail.com>.
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

namespace phparia\Resources;

use phparia\Client\AriClient;
use phparia\Events\Event;

/**
 * Detailed information about a remote peer that communicates with Asterisk.
 *
 * @author Eric Smith <eric2733@gmail.com>
 */
class Peer extends Resource
{
    /**
     * @var string (optional) - The IP address of the peer.
     */
    private $address;

    /**
     * @var string (optional) - An optional reason associated with the change in peer_status.
     */
    private $cause;

    /**
     * @var string The current state of the peer. Note that the values of the status are dependent on the underlying peer technology.
     */
    private $peerStatus;

    /**
     * @var string (optional) - The port of the peer.
     */
    private $port;

    /**
     * @var integer (optional) - The last known time the peer was contacted.
     */
    private $time;

    /**
     * @return string (optional) - The IP address of the peer.
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string (optional) - An optional reason associated with the change in peer_status.
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return string The current state of the peer. Note that the values of the status are dependent on the underlying peer technology.
     */
    public function getPeerStatus()
    {
        return $this->peerStatus;
    }

    /**
     * @return string (optional) - The port of the peer.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return integer (optional) - The last known time the peer was contacted.
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->address = $this->getResponseValue('address');
        $this->cause = $this->getResponseValue('cause');
        $this->peerStatus = $this->getResponseValue('peer_status');
        $this->port = $this->getResponseValue('port');
        $this->time = $this->getResponseValue('time');
    }

}
