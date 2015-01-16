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

use phparia\Client\Client;
use phparia\Resources\Channel;
use phparia\Resources\Bridge;

/**
 * Notification that an attended transfer has occurred.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class BridgeAttendedTransfer extends Event
{
    /**
     * @var string (optional) - Application that has been transferred into
     */
    private $destinationApplication;

    /**
     * @var string (optional) - Bridge that survived the merge result
     */
    private $destinationBridge;

    /**
     * @var Channel (optional) - First leg of a link transfer result
     */
    private $destinationLinkFirstLeg;

    /**
     * @var Channel (optional) - Second leg of a link transfer result
     */
    private $destinationLinkSecondLeg;

    /**
     * @var Bridge (optional) - Bridge that survived the threeway result
     */
    private $destinationThreewayBridge;

    /**
     * @var Channel (optional) - Transferer channel that survived the threeway result
     */
    private $destinationThreewayChannel;

    /**
     * @var string How the transfer was accomplished 
     */
    private $destinationType;

    /**
     * @var boolean Whether the transfer was externally initiated or not
     */
    private $isExternal;

    /**
     * @var Channel (optional) - The channel that is replacing transferer_first_leg in the swap
     */
    private $replaceChannel;

    /**
     * @var string The result of the transfer attempt 
     */
    private $result;

    /**
     * @var Channel (optional) - The channel that is being transferred to
     */
    private $transferTarget;

    /**
     * @var Channel (optional) - The channel that is being transferred 
     */
    private $transferee;

    /**
     * @var Channel First leg of the transferer 
     */
    private $transfererFirstLeg;

    /**
     * @var Bridge (optional) - Bridge the transferer first leg is in 
     */
    private $transfererFirstLegBridge;

    /**
     * @var Channel Second leg of the transferer 
     */
    private $transfererSecondLeg;

    /**
     * @var Bridge (optional) - Bridge the transferer second leg is in 
     */
    private $transfererSecondLegBridge;

    /**
     * @return string (optional) - Application that has been transferred into
     */
    public function getDestinationApplication()
    {
        return $this->destinationApplication;
    }

    /**
     * @return string (optional) - Bridge that survived the merge result
     */
    public function getDestinationBridge()
    {
        return $this->destinationBridge;
    }

    /**
     * @return Channel (optional) - First leg of a link transfer result
     */
    public function getDestinationLinkFirstLeg()
    {
        return $this->destinationLinkFirstLeg;
    }

    /**
     * @return Channel (optional) - Second leg of a link transfer result
     */
    public function getDestinationLinkSecondLeg()
    {
        return $this->destinationLinkSecondLeg;
    }

    /**
     * @return Bridge (optional) - Bridge that survived the threeway result
     */
    public function getDestinationThreewayBridge()
    {
        return $this->destinationThreewayBridge;
    }

    /**
     * @return Channel (optional) - Transferer channel that survived the threeway result
     */
    public function getDestinationThreewayChannel()
    {
        return $this->destinationThreewayChannel;
    }

    /**
     * @return string How the transfer was accomplished
     */
    public function getDestinationType()
    {
        return $this->destinationType;
    }

    /**
     * @return boolean Whether the transfer was externally initiated or not
     */
    public function isExternal()
    {
        return $this->isExternal;
    }

    /**
     * @return Channel (optional) - The channel that is replacing transferer_first_leg in the swap
     */
    public function getReplaceChannel()
    {
        return $this->replaceChannel;
    }

    /**
     * @return string The result of the transfer attempt
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Channel (optional) - The channel that is being transferred to
     */
    public function getTransferTarget()
    {
        return $this->transferTarget;
    }

    /**
     * @return Channel (optional) - The channel that is being transferred
     */
    public function getTransferee()
    {
        return $this->transferee;
    }

    /**
     * @return Channel First leg of the transferer
     */
    public function getTransfererFirstLeg()
    {
        return $this->transfererFirstLeg;
    }

    /**
     * @return Bridge (optional) - Bridge the transferer first leg is in
     */
    public function getTransfererFirstLegBridge()
    {
        return $this->transfererFirstLegBridge;
    }

    /**
     * @return Channel Second leg of the transferer
     */
    public function getTransfererSecondLeg()
    {
        return $this->transfererSecondLeg;
    }

    /**
     * @return Bridge (optional) - Bridge the transferer second leg is in
     */
    public function getTransfererSecondLegBridge()
    {
        return $this->transfererSecondLegBridge;
    }

    /**
     * @param Client $client
     * @param string $response
     */
    public function __construct(Client $client, $response)
    {
        parent::__construct($client, $response);

        $this->destinationApplication = property_exists($this->response, 'destination_application') ? $this->response->destination_application : null;
        $this->destinationBridge = property_exists($this->response, 'destination_bridge') ? $this->response->destination_bridge : null;
        $this->destinationLinkFirstLeg = property_exists($this->response, 'destination_link_first_leg') ? new Channel($client, $this->response->destination_link_first_leg) : null;
        $this->destinationLinkSecondLeg = property_exists($this->response, 'destination_link_second_leg') ? new Channel($client, $this->response->destination_link_second_leg) : null;
        $this->destinationThreewayBridge = property_exists($this->response, 'destination_threeway_bridge') ? new Bridge($client, $this->response->destination_threeway_bridge) : null;
        $this->destinationThreewayChannel = property_exists($this->response, 'destination_threeway_channel') ? new Channel($client, $this->response->destination_threeway_channel) : null;
        $this->destinationType = property_exists($this->response, 'destination_type') ? $this->response->destination_type : null;
        $this->isExternal = $this->response->is_external;
        $this->replaceChannel = property_exists($this->response, 'replace_channel') ? new Channel($client, $this->response->replace_channel) : null;
        $this->result = $this->response->result;
        $this->transferTarget = property_exists($this->response, 'transfer_target') ? new Channel($client, $this->response->transfer_target) : null;
        $this->transferee = property_exists($this->response, 'transferee') ? new Channel($client, $this->response->transferee) : null;
        $this->transfererFirstLeg = new Channel($client, $this->response->transferer_first_leg);
        $this->transfererFirstLegBridge = property_exists($this->response, 'transferer_first_leg_bridge') ? new Bridge($client, $this->response->transferer_first_leg_bridge) : null;
        $this->transfererSecondLeg = new Channel($client, $this->response->transferer_second_leg);
        $this->transfererSecondLegBridge = property_exists($this->response, 'transferer_second_leg_bridge') ? new Bridge($client, $this->response->transferer_second_leg_bridge) : null;
    }

}
