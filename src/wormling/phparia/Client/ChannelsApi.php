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

use phparia\Entity\Channel;
use phparia\Entity\LiveRecording;
use phparia\Entity\Playback;
use phparia\Entity\Variable;

/**
 * Channels API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelsApi extends Base
{

    /**
     * List all active channels in Asterisk.
     * 
     * @return Channel[]
     */
    public function getChannels()
    {
        $uri = '/channels';
        $response = $this->client->getAriEndpoint()->get($uri);

        $channels = [];
        foreach ($response as $channel) {
            $channels[] = new Channel($channel);
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
     * @param string $app The application that is subscribed to the originated channel, and passed to the Stasis application.
     * @param string $appArgs The application arguments to pass to the Stasis application.
     * @param string $callerId CallerID to use when dialing the endpoint or extension.
     * @param int $timeout (default 30) Timeout (in seconds) before giving up dialing, or -1 for no timeout.
     * @param string $channelId The unique id to assign the channel on creation.
     * @param string $otherChannelId The unique id to assign the second channel when using local channels.
     * @param array $variables The "variables" key in the body object holds variable key/value pairs to set on the channel on creation. Other keys in the body object are interpreted as query parameters. Ex. { "endpoint": "SIP/Alice", "variables": { "CALLERID(name)": "Alice" } }
     * @return Channel
     */
    public function createChannel($endpoint, $extension, $context, $priority, $app, $appArgs, $callerId, $timeout, $channelId, $otherChannelId, $variables)
    {
        $uri = '/channels';
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'endpoint' => $endpoint,
            'extension' => $extension,
            'context' => $context,
            'priority' => $priority,
            'app' => $app,
            'appArgs' => $appArgs,
            'callerId' => $callerId,
            'timeout' => $timeout,
            'channelId' => $channelId,
            'otherChannelId' => $otherChannelId,
            'variables' => $variables,
        ));

        return new Channel($response);
    }

    /**
     * Channel details.
     * 
     * @param string $channelId
     * @return Channel
     */
    public function getChannel($channelId)
    {
        $uri = "/channels/$channelId";
        try {
            $response = $this->client->getAriEndpoint()->get($uri);
        } catch (Pest_NotFound $e) { // Channel not found
            throw new NotFoundException($e);
        }

        return new Channel($response);
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
     */
    public function createChannelWithId($endpoint, $extension, $context, $priority, $app, $appArgs, $callerId, $timeout, $channelId, $otherChannelId, $variables)
    {
        $uri = "/channels/$channelId";
        $response = $this->client->getAriEndpoint()->post($uri, array(
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

        return new Channel($response);
    }

    /**
     * Delete (i.e. hangup) a channel.
     * 
     * @param string $channelId Channel's id
     */
    public function deleteChannel($channelId)
    {
        $uri = "/channels/$channelId";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Exit application; continue execution in the dialplan.
     * 
     * @param string $channelId Channel's id
     * @param string $context The context to continue to.
     * @param string $extension The extension to continue to.
     * @param int $priority The priority to continue to.
     */
    public function continueDialplan($channelId, $context, $extension, $priority)
    {
        $uri = "/channels/$channelId/continue";
        $this->client->getAriEndpoint()->post($uri, array(
            'context' => $context,
            'extension' => $extension,
            'priority' => $priority,
        ));
    }

    /**
     * Answer a channel.
     * 
     * @param string $channelId Channel's id
     */
    public function answer($channelId)
    {
        $uri = "/channels/$channelId/answer";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Indicate ringing to a channel.
     * 
     * @param string $channelId
     */
    public function startRinging($channelId)
    {
        $uri = "/channels/$channelId/ring";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Stop ringing indication on a channel if locally generated.
     * 
     * @param string $channelId
     */
    public function stopRinging($channelId)
    {
        $uri = "/channels/$channelId/ring";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Send provided DTMF to a given channel.
     * 
     * @param string $channelId
     * @param string $dtmf
     * @param int $before
     * @param int $between
     * @param int $duration
     * @param int $after
     */
    public function sendDtmf($channelId, $dtmf, $before, $between, $duration, $after)
    {
        $uri = "/channels/$channelId/dtmf";
        $this->client->getAriEndpoint()->post($uri, array(
            'dtmf' => $dtmf,
            'before' => $before,
            'between' => $between,
            'duration' => $duration,
            'after' => $after,
        ));
    }

    /**
     * Mute a channel.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to mute audio
     */
    public function mute($channelId, $direction)
    {
        $uri = "/channels/$channelId/mute";
        $this->client->getAriEndpoint()->post($uri, array(
            'direction' => $direction,
        ));
    }

    /**
     * Unmute a channel.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to unmute audio
     */
    public function unmute($channelId, $direction)
    {
        $uri = "/channels/$channelId/mute";
        $this->client->getAriEndpoint()->delete($uri, array(
            'direction' => $direction,
        ));
    }

    /**
     * Hold a channel.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to mute audio
     */
    public function hold($channelId, $direction)
    {
        $uri = "/channels/$channelId/hold";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Remove a channel from hold.
     * 
     * @param string $channelId Channel's id
     * @param string $direction (default both) Direction in which to unmute audio
     */
    public function unhold($channelId, $direction)
    {
        $uri = "/channels/$channelId/hold";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Play music on hold to a channel. Using media operations such as /play on a channel playing MOH in 
     * this manner will suspend MOH without resuming automatically. If continuing music on hold is 
     * desired, the stasis application must reinitiate music on hold.
     * 
     * @param string $channelId Channel's id
     * @param string $mohClass Music on hold class to use
     */
    public function startMusicOnHold($channelId, $mohClass)
    {
        $uri = "/channels/$channelId/moh";
        $this->client->getAriEndpoint()->post($uri, array(
            'mohClass' => $mohClass,
        ));
    }

    /**
     * Stop playing music on hold to a channel.
     * 
     * @param string $channelId Channel's id
     */
    public function stopMusicOnHold($channelId)
    {
        $uri = "/channels/$channelId/moh";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Play silence to a channel. Using media operations such as /play on a channel playing silence in this manner will suspend silence without resuming automatically.
     * 
     * @param string $channelId Channel's id
     */
    public function startSilence($channelId)
    {
        $uri = "/channels/$channelId/silence";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Stop playing silence to a channel.
     * 
     * @param string $channelId Channel's id
     */
    public function stopSilence($channelId)
    {
        $uri = "/channels/$channelId/silence";
        $this->client->getAriEndpoint()->delete($uri);
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
     */
    public function playMedia($channelId, $media, $lang, $offsetms, $skipms, $playbackId)
    {
        $uri = "/channels/$channelId/play";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'media' => $media,
            'lang' => $lang,
            'offsetms' => $offsetms,
            'skipms' => $skipms,
            'playbackId' => $playbackId,
        ));

        return new Playback($response);
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
     */
    public function playMediaById($channelId, $media, $lang, $offsetms, $skipms, $playbackId)
    {
        $uri = "/channels/$channelId/play/$playbackId";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'media' => $media,
            'lang' => $lang,
            'offsetms' => $offsetms,
            'skipms' => $skipms,
        ));

        return new Playback($response);
    }

    /**
     * Start a recording. Record audio from a channel. Note that this will not capture audio sent to the 
     * channel. The bridge itself has a record feature if that's what you want.
     * 
     * @param string $channelId
     * @param string $name
     * @param string $format
     * @param int $maxDurationSeconds
     * @param int $maxSilenceSeconds
     * @param string $ifExists
     * @param boolean $beep
     * @param string $terminateOn
     * @return LiveRecording
     */
    public function record($channelId, $name, $format, $maxDurationSeconds, $maxSilenceSeconds, $ifExists, $beep, $terminateOn)
    {
        $uri = "/channels/$channelId/record";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'name' => $name,
            'format' => $format,
            'maxDurationSeconds' => $maxDurationSeconds,
            'maxSilenceSeconds' => $maxSilenceSeconds,
            'ifExists' => $ifExists,
            'beep' => $beep,
            'terminateOn' => $terminateOn,
        ));

        return new LiveRecording($response);
    }

    /**
     * Get the value of a channel variable or function.
     * 
     * @param string $channelId
     * @param string $variable
     * @return Variable
     */
    public function getVariable($channelId, $variable)
    {
        $uri = "/channels/$channelId/variable";
        $response = $this->client->getAriEndpoint()->get($uri, array(
            'variable' => $variable,
        ));

        return new Variable($response);
    }

    /**
     * Set the value of a channel variable or function.
     * 
     * @param string $channelId
     * @param string $variable
     * @param string $value
     * @return Variable
     */
    public function setVariable($channelId, $variable, $value)
    {
        $uri = "/channels/$channelId/variable";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'variable' => $variable,
            'value' => $value,
        ));

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
     */
    public function startSnoop($channelId, $spy, $whisper, $app, $appArgs, $snoopId)
    {
        $uri = "/channels/$channelId/snoop";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'spy' => $spy,
            'whisper' => $whisper,
            'app' => $app,
            'appArgs' => $appArgs,
            'snoopId' => $snoopId,
        ));

        return new Channel($response);
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
     */
    public function startSnoopWithId($channelId, $spy, $whisper, $app, $appArgs, $snoopId)
    {
        $uri = "/channels/$channelId/snoop/$snoopId";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'spy' => $spy,
            'whisper' => $whisper,
            'app' => $app,
            'appArgs' => $appArgs,
        ));

        return new Channel($response);
    }

}
