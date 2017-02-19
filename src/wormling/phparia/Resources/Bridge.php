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

namespace phparia\Resources;

use phparia\Client\AriClient;
use phparia\Events\Event;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\UnprocessableEntityException;

/**
 * The merging of media from one or more channels.
 * Everyone on the bridge receives the same audio.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Bridge extends Resource
{
    const TYPE_MIXING = 'mixing';
    const TYPE_HOLDING = 'holding';
    const TYPE_DTMF_EVENTS = 'dtmf_events';
    const TYPE_PROXY_MEDIA = 'proxy_media';
    
    /**
     * @var string Bridging class
     */
    private $bridgeClass;

    /**
     * @var string Type of bridge technology (mixing, holding, dtmf_events, proxy_media)
     */
    private $bridgeType;

    /**
     * @var array Ids of channels participating in this bridge
     */
    private $channelIds;

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
     * @return string Type of bridge technology (mixing, holding, dtmf_events, proxy_media)
     */
    public function getBridgeType()
    {
        return $this->bridgeType;
    }

    /**
     * @return array Ids of channels participating in this bridge
     */
    public function getChannelIds()
    {
        return $this->channelIds;
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
     * @param callable $callback
     */
    public function onBridgeCreated(callable $callback)
    {
        $this->on(Event::BRIDGE_CREATED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceBridgeCreated(callable $callback)
    {
        $this->once(Event::BRIDGE_CREATED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onBridgeDestroyed(callable $callback)
    {
        $this->on(Event::BRIDGE_DESTROYED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceBridgeDestroyed(callable $callback)
    {
        $this->once(Event::BRIDGE_DESTROYED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onBridgeVideoSourceChanged(callable $callback)
    {
        $this->on(Event::BRIDGE_VIDEO_SOURCE_CHANGED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceBridgeVideoSourceChanged(callable $callback)
    {
        $this->once(Event::BRIDGE_VIDEO_SOURCE_CHANGED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onPeerStatusChange(callable $callback)
    {
        $this->on(Event::PEER_STATUS_CHANGE.'_'/* @todo Something unique to the peer */, $callback);
    }

    /**
     * @param callable $callback
     */
    public function oncePeerStatusChange(callable $callback)
    {
        $this->once(Event::PEER_STATUS_CHANGE.'_'/* @todo Something unique to the peer */, $callback);
    }

    /**
     * @param callable $callback
     */
    public function onBridgeBlindTransfer(callable $callback)
    {
        $this->on(Event::BRIDGE_BLIND_TRANSFER.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceBridgeBlindTransfer(callable $callback)
    {
        $this->once(Event::BRIDGE_BLIND_TRANSFER.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onBridgeAttendedTransfer(callable $callback)
    {
        $this->on(Event::BRIDGE_ATTENDED_TRANSFER.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceBridgeAttendedTransfer(callable $callback)
    {
        $this->once(Event::BRIDGE_ATTENDED_TRANSFER.'_'.$this->getId(), $callback);
    }

    /**
     * Shut down a bridge. If any channels are in this bridge, they will be removed and resume whatever they were doing beforehand.
     *
     * @throws NotFoundException
     */
    public function deleteBridge()
    {
        $this->client->bridges()->deleteBridge($this->id);
    }

    /**
     * Add a channel to a bridge.
     *
     * @param string $channel (required) Ids of channels to add to bridge.  Allows comma separated values.
     * @param string $role Channel's role in the bridge
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function addChannel($channel, $role = null)
    {
        $this->client->bridges()->addChannel($this->id, $channel, $role);
    }

    /**
     * Remove a channel from a bridge.
     *
     * @param string $channel (required) Ids of channels to remove from bridge.  Allows comma separated values.
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function removeChannel($channel)
    {
        $this->client->bridges()->removeChannel($this->id, $channel);
    }

    /**
     * Play music on hold to a bridge or change the MOH class that is playing.
     *
     * @param string $mohClass Music on hold class to use
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startMusicOnHold($mohClass)
    {
        $this->client->bridges()->startMusicOnHold($this->id, $mohClass);
    }

    /**
     * Stop playing music on hold to a bridge. This will only stop music on hold being played via POST bridges/{bridgeId}/moh.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopMusicOnHold()
    {
        $this->client->bridges()->stopMusicOnHold($this->id);
    }

    /**
     * Start playback of media on a bridge. The media URI may be any of a number of URI's. Currently
     * sound:, recording:, number:, digits:, characters:, and tone: URI's are supported. This operation
     * creates a playback resource that can be used to control the playback of media (pause, rewind,
     * fast forward, etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMedia($media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        return $this->client->bridges()->playMedia($this->id, $media, $lang, $offsetms, $skipms, $playbackId);
    }

    /**
     * Start playback of media on a bridge. The media URI may be any of a number of URI's. Currently
     * sound:, recording:, number:, digits:, characters:, and tone: URI's are supported. This operation
     * creates a playback resource that can be used to control the playback of media (pause, rewind,
     * fast forward, etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMediaWithId($media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        return $this->client->bridges()->playMediaWithId($this->id, $media, $lang, $offsetms, $skipms, $playbackId);
    }

    /**
     * Start a recording. Record audio from a channel. Note that this will not capture audio sent to the
     * channel. The bridge itself has a record feature if that's what you want.
     *
     * @param string $name (required) Recording's filename
     * @param string $format (required) Format to encode audio in
     * @param int $maxDurationSeconds Maximum duration of the recording, in seconds. 0 for no limit.  Allowed range: Min: 0; Max: None
     * @param int $maxSilenceSeconds Maximum duration of silence, in seconds. 0 for no limit.  Allowed range: Min: 0; Max: None
     * @param string $ifExists = Action to take if a recording with the same name already exists. default: fail, Allowed values: fail, overwrite, append
     * @param boolean $beep Play beep when recording begins
     * @param string $terminateOn DTMF input to terminate recording.  Default: none, Allowed values: none, any, *, #
     * @return LiveRecording
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function record(
        $name,
        $format,
        $maxDurationSeconds = null,
        $maxSilenceSeconds = null,
        $ifExists = null,
        $beep = null,
        $terminateOn = null
    ) {
        return $this->client->bridges()->record($this->id, $name, $format, $maxDurationSeconds, $maxSilenceSeconds,
            $ifExists, $beep, $terminateOn);
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->bridgeClass = $this->getResponseValue('bridge_class');
        $this->bridgeType = $this->getResponseValue('bridge_type');
        $this->channels = $this->getResponseValue('channels');
        $this->creator = $this->getResponseValue('creator');
        $this->id = $this->getResponseValue('id');
        $this->name = $this->getResponseValue('name');
        $this->technology = $this->getResponseValue('technology');
    }

}
