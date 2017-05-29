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

use DateTime;
use phparia\Client\AriClient;

/**
 * Base type for asynchronous events from Asterisk.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Event extends Message implements EventInterface
{
    // ARI events
    const APPLICATION_REPLACED = 'ApplicationReplaced';
    const BRIDGE_ATTENDED_TRANSFER = 'BridgeAttendedTransfer';
    const BRIDGE_BLIND_TRANSFER = 'BridgeBlindTransfer';
    const BRIDGE_CREATED = 'BridgeCreated';
    const BRIDGE_DESTROYED = 'BridgeDestroyed';
    const BRIDGE_MERGED = 'BridgeMerged';
    const BRIDGE_VIDEO_SOURCE_CHANGED = 'BridgeVideoSourceChanged';
    const CHANNEL_CALLER_ID = 'ChannelCallerId';
    const CHANNEL_CREATED = 'ChannelCreated';
    const CHANNEL_CONNECTED_LINE = 'ChannelConnectedLine';
    const CHANNEL_DESTROYED = 'ChannelDestroyed';
    const CHANNEL_DTMF_RECEIVED = 'ChannelDtmfReceived';
    const CHANNEL_ENTERED_BRIDGE = 'ChannelEnteredBridge';
    const CHANNEL_HANGUP_REQUEST = 'ChannelHangupRequest';
    const CHANNEL_LEFT_BRIDGE = 'ChannelLeftBridge';
    const CHANNEL_STATE_CHANGE = 'ChannelStateChange';
    const CHANNEL_HOLD = 'ChannelHold';
    const CHANNEL_UNHOLD = 'ChannelUnhold';
    const CHANNEL_TALKING_FINISHED = 'ChannelTalkingFinished';
    const CHANNEL_TALKING_STARTED = 'ChannelTalkingStarted';
    const CHANNEL_USEREVENT = 'ChannelUserevent';
    const CHANNEL_VARSET = 'ChannelVarset';
    const DEVICE_STATE_CHANGE = 'DeviceStateChange';
    const DIAL = 'Dial';
    const DIALED = 'Dialed';
    const ENDPOINT_STATE_CHANGE = 'EndpointStateChange';
    const PEER_STATUS_CHANGE = 'PeerStatusChange';
    const PLAYBACK_CONTINUING = 'PlaybackContinuing';
    const PLAYBACK_FINISHED = 'PlaybackFinished';
    const PLAYBACK_STARTED = 'PlaybackStarted';
    const RECORDING_FAILED = 'RecordingFailed';
    const RECORDING_FINISHED = 'RecordingFinished';
    const RECORDING_STARTED = 'RecordingStarted';
    const STASIS_END = 'StasisEnd';
    const STASIS_START = 'StasisStart';
    const TEXT_MESSAGE_RECEIVED = 'TextMessageReceived';

    /**
     * @var AriClient
     */
    protected $client;

    /**
     * @var string Name of the application receiving the event.
     */
    private $application;

    /**
     * @var DateTime (optional) - Time at which this event was created.
     */
    private $timestamp;

    /**
     * @return string Name of the application receiving the event.
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return \DateTime (optional) - Time at which this event was created.
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        $this->client = $client;

        parent::__construct($response);

        $this->application = $this->getResponseValue('application');
        $this->timestamp = $this->getResponseValue('timestamp', '\DateTime');
    }
}
