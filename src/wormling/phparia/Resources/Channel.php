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

use DateTime;
use phparia\Client\AriClient;
use phparia\Events\Event;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\UnprocessableEntityException;

/**
 * A specific communication connection between Asterisk and an Endpoint.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Channel extends Resource
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
     * @param callable $callback
     */
    public function onStasisEnd(callable $callback)
    {
        $this->on(Event::STASIS_END.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceStasisEnd(callable $callback)
    {
        $this->once(Event::STASIS_END.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onStasisStart(callable $callback)
    {
        $this->on(Event::STASIS_START.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceStasisStart(callable $callback)
    {
        $this->once(Event::STASIS_START.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelCallerId(callable $callback)
    {
        $this->on(Event::CHANNEL_CALLER_ID.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelCallerId(callable $callback)
    {
        $this->once(Event::CHANNEL_CALLER_ID.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelCreated(callable $callback)
    {
        $this->on(Event::CHANNEL_CREATED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelCreated(callable $callback)
    {
        $this->once(Event::CHANNEL_CREATED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelDestroyed(callable $callback)
    {
        $this->on(Event::CHANNEL_DESTROYED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelDestroyed(callable $callback)
    {
        $this->once(Event::CHANNEL_DESTROYED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function removeChannelDestroyedListener(callable $callback)
    {
        $this->removeListener(Event::CHANNEL_DESTROYED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelDtmfReceived(callable $callback)
    {
        $this->on(Event::CHANNEL_DTMF_RECEIVED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelDtmfReceived(callable $callback)
    {
        $this->once(Event::CHANNEL_DTMF_RECEIVED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelEnteredBridge(callable $callback)
    {
        $this->on(Event::CHANNEL_ENTERED_BRIDGE.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelEnteredBridge(callable $callback)
    {
        $this->once(Event::CHANNEL_ENTERED_BRIDGE.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelHangupRequest(callable $callback)
    {
        $this->on(Event::CHANNEL_HANGUP_REQUEST.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelHangupRequest(callable $callback)
    {
        $this->once(Event::CHANNEL_HANGUP_REQUEST.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelLeftBridge(callable $callback)
    {
        $this->on(Event::CHANNEL_LEFT_BRIDGE.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelLeftBridge(callable $callback)
    {
        $this->once(Event::CHANNEL_LEFT_BRIDGE.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelStateChange(callable $callback)
    {
        $this->on(Event::CHANNEL_STATE_CHANGED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelStateChange(callable $callback)
    {
        $this->once(Event::CHANNEL_STATE_CHANGED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelHold(callable $callback)
    {
        $this->on(Event::CHANNEL_HOLD.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelHold(callable $callback)
    {
        $this->once(Event::CHANNEL_HOLD.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelUnhold(callable $callback)
    {
        $this->on(Event::CHANNEL_UNHOLD.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChanneUnhold(callable $callback)
    {
        $this->once(Event::CHANNEL_UNHOLD.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelTalkingFinished(callable $callback)
    {
        $this->on(Event::CHANNEL_TALKING_FINISHED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelTalkingFinished(callable $callback)
    {
        $this->once(Event::CHANNEL_TALKING_FINISHED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelTalkingStarted(callable $callback)
    {
        $this->on(Event::CHANNEL_TALKING_STARTED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelTalkingStarted(callable $callback)
    {
        $this->once(Event::CHANNEL_TALKING_STARTED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelUserevent(callable $callback)
    {
        $this->on(Event::CHANNEL_USEREVENT.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelUserevent(callable $callback)
    {
        $this->once(Event::CHANNEL_USEREVENT.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onChannelVarset(callable $callback)
    {
        $this->on(Event::CHANNEL_VARSET.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceChannelVarset(callable $callback)
    {
        $this->once(Event::CHANNEL_VARSET.'_'.$this->getId(), $callback);
    }

    /**
     * Delete (i.e. hangup) a channel.
     *
     * @throws NotFoundException
     */
    public function deleteChannel()
    {
        $this->client->channels()->deleteChannel($this->id);
    }

    /**
     * Hangup the channel if it still exists.
     */
    public function hangup()
    {
        $this->client->channels()->hangup($this->id);
    }

    /**
     * Exit application; continue execution in the dialplan.
     *
     * @param string $context The context to continue to.
     * @param string $extension The extension to continue to.
     * @param int $priority The priority to continue to.
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function continueDialplan($context, $extension, $priority)
    {
        $this->client->channels()->continueDialplan($this->id, $context, $extension, $priority);
    }

    /**
     * Answer a channel.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function answer()
    {
        $this->client->channels()->answer($this->id);
    }

    /**
     * Indicate ringing to a channel.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startRinging()
    {
        $this->client->channels()->startRinging($this->id);
    }

    /**
     * Stop ringing indication on a channel if locally generated.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopRinging()
    {
        $this->client->channels()->stopRinging($this->id);
    }

    /**
     * Send provided DTMF to a given channel.
     *
     * @param string $dtmf DTMF To send.
     * @param int $before Amount of time to wait before DTMF digits (specified in milliseconds) start.
     * @param int $between Amount of time in between DTMF digits (specified in milliseconds).  Default: 100
     * @param int $duration Length of each DTMF digit (specified in milliseconds).  Default: 100
     * @param int $after Amount of time to wait after DTMF digits (specified in milliseconds) end.
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function sendDtmf($dtmf, $before = null, $between = null, $duration = null, $after = null)
    {
        $this->client->channels()->sendDtmf($this->id, $dtmf, $before, $between, $duration, $after);
    }

    /**
     * Mute a channel.
     *
     * @param string $direction (default both) Direction in which to mute audio
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function mute($direction)
    {
        $this->client->channels()->mute($this->id, $direction);
    }

    /**
     * Unmute a channel.
     *
     * @param string $direction (default both) Direction in which to unmute audio
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function unmute($direction)
    {
        $this->client->channels()->unmute($this->id, $direction);
    }

    /**
     * Hold a channel.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function hold()
    {
        $this->client->channels()->hold($this->id);
    }

    /**
     * Remove a channel from hold.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function unhold()
    {
        $this->client->channels()->unhold($this->id);
    }

    /**
     * Play music on hold to a channel. Using media operations such as /play on a channel playing MOH in
     * this manner will suspend MOH without resuming automatically. If continuing music on hold is
     * desired, the stasis application must reinitiate music on hold.
     *
     * @param string $mohClass Music on hold class to use
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startMusicOnHold($mohClass)
    {
        $this->client->channels()->startMusicOnHold($this->id, $mohClass);
    }

    /**
     * Stop playing music on hold to a channel.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopMusicOnHold()
    {
        $this->client->channels()->stopMusicOnHold($this->id);
    }

    /**
     * Play silence to a channel. Using media operations such as /play on a channel playing silence in this manner will suspend silence without resuming automatically.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startSilence()
    {
        $this->client->channels()->startSilence($this->id);
    }

    /**
     * Stop playing silence to a channel.
     *
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopSilence()
    {
        $this->client->channels()->stopSilence($this->id);
    }

    /**
     * Start playback of media. The media URI may be any of a number of URI's. Currently sound:,
     * recording:, number:, digits:, characters:, and tone: URI's are supported. This operation creates a
     * playback resource that can be used to control the playback of media (pause, rewind, fast forward,
     * etc.)
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
        return $this->client->channels()->playMedia($this->id, $media, $lang, $offsetms, $skipms, $playbackId);
    }

    /**
     * Start playback of media and specify the playbackId. The media URI may be any of a number of URI's.
     * Currently sound: and recording: URI's are supported. This operation creates a playback resource
     * that can be used to control the playback of media (pause, rewind, fast forward, etc.)
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
        return $this->client->channels()->playMediaWithId($this->getId(), $media, $lang, $offsetms, $skipms,
            $playbackId);
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
        return $this->client->channels()->record($this->id, $name, $format, $maxDurationSeconds, $maxSilenceSeconds,
            $ifExists, $beep, $terminateOn);
    }

    /**
     * Get the value of a channel variable or function.
     *
     * @param string $variable
     * @param null|string $default The value to return if the variable does not exist
     * @return string|Variable
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function getVariable($variable, $default = null)
    {
        return $this->client->channels()->getVariable($this->id, $variable, $default);
    }

    /**
     * Set the value of a channel variable or function.
     *
     * @param string $variable
     * @param string $value
     * @return Variable
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function setVariable($variable, $value)
    {
        return $this->client->channels()->setVariable($this->id, $variable, $value);
    }

    /**
     * Start snooping. Snoop (spy/whisper) on a specific channel.
     *
     * @param string $spy (default none) Direction of audio to spy on
     * @param string $whisper (default none) Direction of audio to whisper into
     * @param string $app (required) Application the snooping channel is placed into
     * @param string $appArgs The application arguments to pass to the Stasis application
     * @param string $snoopId Unique ID to assign to snooping channel
     * @return Channel
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function startSnoop($spy, $whisper, $app, $appArgs, $snoopId)
    {
        return $this->client->channels()->startSnoop($this->id, $spy, $whisper, $app, $appArgs, $snoopId);
    }

    /**
     * Start snooping. Snoop (spy/whisper) on a specific channel.
     *
     * @param string $spy (default none) Direction of audio to spy on
     * @param string $whisper (default none) Direction of audio to whisper into
     * @param string $app (required) Application the snooping channel is placed into
     * @param string $appArgs The application arguments to pass to the Stasis application
     * @param string $snoopId Unique ID to assign to snooping channel
     * @return Channel
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function startSnoopWithId($spy, $whisper, $app, $appArgs, $snoopId)
    {
        return $this->client->channels()->startSnoopWithId($this->id, $spy, $whisper, $app, $appArgs, $snoopId);
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->accountCode = $this->getResponseValue('accountcode');
        $this->caller = $this->getResponseValue('caller', '\phparia\Resources\CallerId');
        $this->connected = $this->getResponseValue('connected', '\phparia\Resources\CallerId');
        $this->creationTime = $this->getResponseValue('creationtime', '\DateTime');
        $this->dialplan = $this->getResponseValue('dialplan', '\phparia\Resources\DialplanCep');
        $this->id = $this->getResponseValue('id');
        $this->name = $this->getResponseValue('name');
        $this->state = $this->getResponseValue('state');
    }

}
