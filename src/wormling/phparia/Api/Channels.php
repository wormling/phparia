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

namespace phparia\Api;

use Pest_BadRequest;
use Pest_Conflict;
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
class Channels extends MediaBase
{
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
    public function createChannel(
        $endpoint,
        $extension = null,
        $context = null,
        $priority = null,
        $label = null,
        $app = null,
        $appArgs = null,
        $callerId = null,
        $timeout = null,
        $channelId = null,
        $otherChannelId = null,
        $variables = array()
    ) {
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
     * @param string $label Asterisk 13+ The label to dial after the endpoint answers. Will supersede 'priority' if provided. Mutually exclusive with 'app'.
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
    public function createChannelWithId(
        $endpoint,
        $extension = null,
        $context = null,
        $priority = null,
        $label = null,
        $app = null,
        $appArgs = null,
        $callerId = null,
        $timeout = null,
        $channelId = null,
        $otherChannelId = null,
        $variables = array()
    ) {
        $uri = "/channels/$channelId";
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
    public function sendDtmf($channelId, $dtmf, $before = null, $between = null, $duration = null, $after = null)
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
        $uri = "/channels/$channelId/mute?direction=".$this->client->getEndpoint()->jsonEncode($direction);
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

    /**
     * @return string
     */
    public function getType()
    {
        return 'channels';
    }
}
