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
use phparia\Resources\Bridge;

/**
 * Notification that a blind transfer has occurred.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class BridgeBlindTransfer extends Event
{
    /**
     * @var Bridge (optional) - The bridge being transferred
     */
    private $bridge;

    /**
     * @var Channel The channel performing the blind transfer
     */
    private $channel;

    /**
     * @var string The context transferred to
     */
    private $contex;

    /**
     * @var string The extension transferred to
     */
    private $exten;

    /**
     * @var boolean Whether the transfer was externally initiated or not
     */
    private $isExternal;

    /**
     * @var Channel (optional) - The channel that is replacing transferer when the transferee(s) can not be transferred directly
     */
    private $replaceChannel;

    /**
     * @var string The result of the transfer attempt
     */
    private $result;

    /**
     * @var Channel (optional) - The channel that is being transferred
     */
    private $transferee;

    /**
     * @return Bridge (optional) - The bridge being transferred
     */
    public function getBridge()
    {
        return $this->bridge;
    }

    /**
     * @return Channel The channel performing the blind transfer
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string The context transferred to
     */
    public function getContext()
    {
        return $this->contex;
    }

    /**
     * @return string The extension transferred to
     */
    public function getExten()
    {
        return $this->exten;
    }

    /**
     * @return boolean Whether the transfer was externally initiated or not
     */
    public function isExternal()
    {
        return $this->isExternal;
    }

    /**
     * @return Channel (optional) - The channel that is replacing transferer when the transferee(s) can not be transferred directly
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
     * @return Channel (optional) - The channel that is being transferred
     */
    public function getTransferee()
    {
        return $this->transferee;
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->bridge = property_exists($this->response, 'bridge') ? new Bridge($client,
            $this->response->bridge) : null;
        $this->channel = new Channel($client, $this->response->channel);
        $this->contex = $this->response->context;
        $this->exten = $this->response->exten;
        $this->isExternal = $this->response->is_external;
        $this->replaceChannel = property_exists($this->response, 'replace_channel') ? new Channel($client,
            $this->response->replace_channel) : null;
        $this->result = $this->response->result;
        $this->transferee = property_exists($this->response, 'transferee') ? new Channel($client,
            $this->response->transferee) : null;
    }

}
