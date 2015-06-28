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

namespace phparia\Client;

use Pest_BadRequest;
use Pest_Conflict;
use Pest_InvalidRecord;
use Pest_NotFound;
use Pest_ServerError;
use phparia\Exception\UnprocessableEntityException;
use phparia\Resources\Channel;
use phparia\Resources\LiveRecording;
use phparia\Resources\Playback;
use phparia\Resources\Variable;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\ServerException;

/**
 * Channels API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Channels extends Base
{

    use MediaTrait;

    /**
     * List all active channels in Asterisk.
     * 
     * @return Channel[]
     */
    public function getChannels()
    {
        $uri = '/channels';
        $response = $this->client->getEndpoint()->get($uri);

        $channels = [];
        foreach ((array)$response as $channel) {
            $channels[] = new Channel($this->client, $channel);
        }

        return $channels;
    }

    /**
     * Create a new channel (originate). The new channel is created immediately and a snapshot of it 
     * returned. If a Stasis application is provided it will be automatically subscribed to the originated 
     * channel for further events and updates.
     * 
     * @param string $endpoint (required) Endpoint to call.
     * @param string $extension The extension to dial after the endpoint answers
     * @param string $context The context to dial after the endpoint answers. If omitted, uses 'default'
     * @param int $priority The priority to dial after the endpoint answers. If omitted, uses 1
     * @param string $label Asterisk 13+ The label to dial after the endpoint answers. Will supersede 'priority' if provided. Mutually exclusive with 'app'.
     * @param string $app The application that is subscribed to the originated channel. When the channel is answered, it will be passed to this Stasis application. Mutually exclusive with 'context', 'extension', 'priority', and 'label'.
     * @param string $appArgs The application arguments to pass to the Stasis application.
     * @param string $callerId CallerID to use when dialing the endpoint or extension.
     * @param int $timeout (default 30) Timeout (in seconds) before giving up dialing, or -1 for no timeout.
     * @param string $channelId The unique id to assign the channel on creation.
     * @param string $otherChannelId The unique id to assign the second channel when using local channels.
     * @param array $variables The "variables" key in the body object holds variable key/value pairs to set on the channel on creation. Other keys in the body object are interpreted as query parameters. Ex. { "endpoint": "SIP/Alice", "variables": { "CALLERID(name)": "Alice" } }
     * @return Channel
     * @throws InvalidParameterException
     * @throws ServerException
     */
    public function createChannel($endpoint, $extension = null, $context = null, $priority = null, $label = null, $app = null, $appArgs = null, $callerId = null, $timeout = null, $channelId = null, $otherChannelId = null, $variables = array())
    {
        $uri = '/channels';
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'endpoint' => $endpoint,
                'extension' => $extension,
                'context' => $context,
                'priority' => $priority,
                'label' => $label,
                'app' => $app,
                'appArgs' => $appArgs,
                'callerId' => $callerId,
                'timeout' => $timeout,
                'channelId' => $channelId,
                'otherChannelId' => $otherChannelId,
                'variables' => $variables,
            ));
        } catch (Pest_BadRequest $e) { // Invalid parameters for originating a channel.
            throw new InvalidParameterException($e);
        } catch (Pest_ServerError $e) {
            throw new ServerException($e); // Couldn't the channel.
        }

        return new Channel($this->client, $response);
    }

    /**
     * Channel details.
     * 
     * @param string $channelId
     * @return Channel
     * @throws NotFoundException
     */
    public function getChannel($channelId)
    {
        $uri = "/channels/$channelId";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        }

        return new Channel($this->client, $response);
    }

    /**
     * Create a new channel (originate). The new channel is created immediately and a snapshot of it 
     * returned. If a Stasis application is provided it will be automatically subscribed to the originated 
     * channel for further events and updates.
     * 
     * @param string $endpoint (required) Endpoint to call.
     * @param string $extension The extension to dial after the endpoint answers
     * @param string $context The context to dial after the endpoint answers. If omitted, uses 'default'
     * @param int $priority The priority to dial after the endpoint answers. If omitted, uses 1
     * @param string $app The application that is subscribed to the originated channel, and passed to the Stasis application.
     * @param string $appArgs The application arguments to pass to the Stasis application.
     * @param string $callerId CallerID to use when dialing the endpoint or extension.
     * @param int $timeout (default 30) Timeout (in seconds) before giving up dialing, or -1 for no timeout.
     * @param string $channelId The unique id to assign the channel on creation.
     * @param string $otherChannelId The unique id to assign the second channel when using local channels.
     * @param array $variables The "variables" key in the body object holds variable key/value pairs to set on the channel on creation. Other keys in the body object are interpreted as query parameters. Ex. { "endpoint": "SIP/Alice", "variables": { "CALLERID(name)": "Alice" } }
     * @return Channel
     * @throws InvalidParameterException
     */
    public function createChannelWithId($endpoint, $extension = null, $context = null, $priority = null, $app = null, $appArgs = null, $callerId = null, $timeout = null, $channelId = null, $otherChannelId = null, $variables = array())
    {
        $uri = "/channels/$channelId";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'endpoint' => $endpoint,
                'extension' => $extension,
                'context' => $context,
                'priority' => $priority,
                'app' => $app,
                'appArgs' => $appArgs,
                'callerId' => $callerId,
                'timeout' => $timeout,
                'otherChannelId' => $otherChannelId,
                'variables' => $variables,
            ));
        } catch (Pest_BadRequest $e) { // Invalid parameters for originating a channel.
            throw new InvalidParameterException($e);
        }

        return new Channel($this->client, $response);
    }

    /**
     * Delete (i.e. hangup) a channel.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     */
    public function deleteChannel($channelId)
    {
        $uri = "/channels/$channelId";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        }
    }

    /**
     * Hangup a channel if it still exists.
     * 
     * @param string $channelId Channel's id
     */
    public function hangup($channelId)
    {
        try {
            $this->deleteChannel($channelId);
        } catch (\Exception $ignore) {
            // Don't throw exception if the channel doesn't exist
        }
    }

    /**
     * Exit application; continue execution in the dialplan.
     * 
     * @param string $channelId Channel's id
     * @param string $context The context to continue to.
     * @param string $extension The extension to continue to.
     * @param int $priority The priority to continue to.
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function continueDialplan($channelId, $context, $extension, $priority)
    {
        $uri = "/channels/$channelId/continue";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'context' => $context,
                'extension' => $extension,
                'priority' => $priority,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Answer a channel.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function answer($channelId)
    {
        $uri = "/channels/$channelId/answer";
        try {
            $this->client->getEndpoint()->post($uri, array());
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Indicate ringing to a channel.
     * 
     * @param string $channelId
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startRinging($channelId)
    {
        $uri = "/channels/$channelId/ring";
        try {
            $this->client->getEndpoint()->post($uri, array());
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Stop ringing indication on a channel if locally generated.
     * 
     * @param string $channelId
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopRinging($channelId)
    {
        $uri = "/channels/$channelId/ring";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Send provided DTMF to a given channel.
     * 
     * @param string $channelId
     * @param string $dtmf DTMF To send.
     * @param int $before Amount of time to wait before DTMF digits (specified in milliseconds) start.
     * @param int $between Amount of time in between DTMF digits (specified in milliseconds).  Default: 100
     * @param int $duration Length of each DTMF digit (specified in milliseconds).  Default: 100
     * @param int $after Amount of time to wait after DTMF digits (specified in milliseconds) end.
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function sendDtmf($channelId, $dtmf, $before, $between, $duration, $after)
    {
        $uri = "/channels/$channelId/dtmf";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'dtmf' => $dtmf,
                'before' => $before,
                'between' => $between,
                'duration' => $duration,
                'after' => $after,
            ));
        } catch (Pest_BadRequest $e) {
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Mute a channel.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to mute audio.  Allowed values: both, in, out
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function mute($channelId, $direction)
    {
        $uri = "/channels/$channelId/mute";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'direction' => $direction,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Unmute a channel.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to unmute audio
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function unmute($channelId, $direction)
    {
        $uri = "/channels/$channelId/mute?direction=" . $this->client->getEndpoint()->jsonEncode($direction);
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Hold a channel.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function hold($channelId)
    {
        $uri = "/channels/$channelId/hold";
        try {
            $this->client->getEndpoint()->post($uri, array());
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Remove a channel from hold.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function unhold($channelId)
    {
        $uri = "/channels/$channelId/hold";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Play music on hold to a channel. Using media operations such as /play on a channel playing MOH in 
     * this manner will suspend MOH without resuming automatically. If continuing music on hold is 
     * desired, the stasis application must reinitiate music on hold.
     * 
     * @param string $channelId Channel's id
     * @param string $mohClass Music on hold class to use
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startMusicOnHold($channelId, $mohClass)
    {
        $uri = "/channels/$channelId/moh";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'mohClass' => $mohClass,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Stop playing music on hold to a channel.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopMusicOnHold($channelId)
    {
        $uri = "/channels/$channelId/moh";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Play silence to a channel. Using media operations such as /play on a channel playing silence in this manner will suspend silence without resuming automatically.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startSilence($channelId)
    {
        $uri = "/channels/$channelId/silence";
        try {
            $this->client->getEndpoint()->post($uri, array());
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Stop playing silence to a channel.
     * 
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopSilence($channelId)
    {
        $uri = "/channels/$channelId/silence";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

    /**
     * Start playback of media. The media URI may be any of a number of URI's. Currently sound:, 
     * recording:, number:, digits:, characters:, and tone: URI's are supported. This operation creates a 
     * playback resource that can be used to control the playback of media (pause, rewind, fast forward, 
     * etc.)
     * 
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     * 
     * @param string $channelId Channel's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMedia($channelId, $media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        $uri = "/channels/$channelId/play";

        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'media' => $media,
                'lang' => $lang,
                'offsetms' => $offsetms,
                'skipms' => $skipms,
                'playbackId' => $playbackId,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }

        return new Playback($this->client, $response);
    }

    /**
     * Start playback of media and specify the playbackId. The media URI may be any of a number of URI's. 
     * Currently sound: and recording: URI's are supported. This operation creates a playback resource 
     * that can be used to control the playback of media (pause, rewind, fast forward, etc.)
     * 
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     * 
     * @param string $channelId Channel's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMediaWithId($channelId, $media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        $uri = "/channels/$channelId/play/$playbackId";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'media' => $media,
                'lang' => $lang,
                'offsetms' => $offsetms,
                'skipms' => $skipms,
            ));
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }

        return new Playback($this->client, $response);
    }

    /**
     * Start a recording. Record audio from a channel. Note that this will not capture audio sent to the 
     * channel. The bridge itself has a record feature if that's what you want.
     * 
     * @param string $channelId
     * @param string $name (required) Recording's filename
     * @param string $format (required) Format to encode audio in
     * @param int $maxDurationSeconds Maximum duration of the recording, in seconds. 0 for no limit
     * @param int $maxSilenceSeconds Maximum duration of silence, in seconds. 0 for no limit
     * @param string $ifExists = fail - Action to take if a recording with the same name already exists.
     * @param boolean $beep  string = fail - Action to take if a recording with the same name already exists.
     * @param string $terminateOn none - DTMF input to terminate recording
     * @return LiveRecording
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function record($channelId, $name, $format, $maxDurationSeconds = null, $maxSilenceSeconds = null, $ifExists = null, $beep = null, $terminateOn = null)
    {
        $uri = "/channels/$channelId/record";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'name' => $name,
                'format' => $format,
                'maxDurationSeconds' => $maxDurationSeconds,
                'maxSilenceSeconds' => $maxSilenceSeconds,
                'ifExists' => $ifExists,
                'beep' => $beep,
                'terminateOn' => $terminateOn,
            ));
        } catch (Pest_BadRequest $e) { // Invalid parameters
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Channel is not in a Stasis application; A recording with the same name already exists on the system and can not be overwritten because it is in progress or ifExists=fail
            throw new ConflictException($e);
        } catch (Pest_InvalidRecord $e) { // Channel not in Stasis application
            throw new UnprocessableEntityException($e);
        }

        return new LiveRecording($this->client, $response);
    }

    /**
     * Get the value of a channel variable or function.
     *
     * @param string $channelId
     * @param string $variable
     * @param null|string $default The value to return if the variable does not exist
     * @return string|Variable
     * @throws ConflictException
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function getVariable($channelId, $variable, $default = null)
    {
        $uri = "/channels/$channelId/variable";
        try {
            $response = $this->client->getEndpoint()->get($uri, array(
                'variable' => $variable,
            ));
        } catch (Pest_BadRequest $e) { // Missing variable parameter.
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Variable not found
            if ($default !== null) {
                return $default;
            }
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Channel not in a Stasis application
            throw new ConflictException($e);
        }

        return new Variable($response);
    }

    /**
     * Set the value of a channel variable or function.
     * 
     * @param string $channelId
     * @param string $variable
     * @param string $value
     * @return Variable
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function setVariable($channelId, $variable, $value)
    {
        $uri = "/channels/$channelId/variable";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'variable' => $variable,
                'value' => $value,
            ));
        } catch (Pest_BadRequest $e) { // Missing variable parameter.
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Channel not in a Stasis application
            throw new ConflictException($e);
        }

        return new Variable($response);
    }

    /**
     * Start snooping. Snoop (spy/whisper) on a specific channel.
     * 
     * @param string $channelId Channel's id
     * @param string $spy (default none) Direction of audio to spy on
     * @param string $whisper (default none) Direction of audio to whisper into
     * @param string $app (required) Application the snooping channel is placed into
     * @param string $appArgs The application arguments to pass to the Stasis application
     * @param string $snoopId Unique ID to assign to snooping channel
     * @return Channel
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function startSnoop($channelId, $spy, $whisper, $app, $appArgs, $snoopId)
    {
        $uri = "/channels/$channelId/snoop";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'spy' => $spy,
                'whisper' => $whisper,
                'app' => $app,
                'appArgs' => $appArgs,
                'snoopId' => $snoopId,
            ));
        } catch (Pest_BadRequest $e) { // Missing parameters
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        }

        return new Channel($this->client, $response);
    }

    /**
     * Start snooping. Snoop (spy/whisper) on a specific channel.
     * 
     * @param string $channelId Channel's id
     * @param string $spy (default none) Direction of audio to spy on
     * @param string $whisper (default none) Direction of audio to whisper into
     * @param string $app (required) Application the snooping channel is placed into
     * @param string $appArgs The application arguments to pass to the Stasis application
     * @param string $snoopId Unique ID to assign to snooping channel
     * @return Channel
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function startSnoopWithId($channelId, $spy, $whisper, $app, $appArgs, $snoopId)
    {
        $uri = "/channels/$channelId/snoop/$snoopId";
        try {
            $response = $this->client->getEndpoint()->post($uri, array(
                'spy' => $spy,
                'whisper' => $whisper,
                'app' => $app,
                'appArgs' => $appArgs,
            ));
        } catch (Pest_BadRequest $e) { // Missing parameters
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        }

        return new Channel($this->client, $response);
    }

}
