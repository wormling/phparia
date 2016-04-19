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

use GuzzleHttp\Exception\RequestException;
use phparia\Client\AriClientAware;
use phparia\Exception\UnprocessableEntityException;
use phparia\Resources\LiveRecording;
use phparia\Resources\Playback;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;

/**
 * Base class for playing media on channels and bridges
 *
 * @author Brian Smith <wormling@gmail.com>
 */
abstract class MediaBase extends AriClientAware
{
    /**
     * @return string 'channels' or 'bridges'
     */
    abstract public function getType();

    /**
     * Play music on hold to a channel. Using media operations such as /play on a channel playing MOH in
     * this manner will suspend MOH without resuming automatically. If continuing music on hold is
     * desired, the stasis application must reinitiate music on hold.
     *
     * @param string $id Bridge/Channel's id
     * @param string $mohClass Music on hold class to use
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startMusicOnHold($id, $mohClass)
    {
        $uri = "{$this->getType()}/$id/moh";
        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'mohClass' => $mohClass,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Stop playing music on hold to a channel.
     *
     * @param string $id Bridge/Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopMusicOnHold($id)
    {
        $uri = "{$this->getType()}/$id/moh";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
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
     * @param string $id Bridge/Channel's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMedia($id, $media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        $uri = "{$this->getType()}/$id/play";

        try {
            $response = $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'media' => $media,
                    'lang' => $lang,
                    'offsetms' => $offsetms,
                    'skipms' => $skipms,
                    'playbackId' => $playbackId,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Playback($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Start playback of media and specify the playbackId. The media URI may be any of a number of URI's.
     * Currently sound: and recording: URI's are supported. This operation creates a playback resource
     * that can be used to control the playback of media (pause, rewind, fast forward, etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $id Channel's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMediaWithId(
        $id,
        $media,
        $lang = null,
        $offsetms = null,
        $skipms = null,
        $playbackId = null
    ) {
        $uri = "{$this->getType()}/$id/play/$playbackId";
        try {
            $response = $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'media' => $media,
                    'lang' => $lang,
                    'offsetms' => $offsetms,
                    'skipms' => $skipms,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Playback($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Start a recording. Record audio from a channel. Note that this will not capture audio sent to the
     * channel. The bridge itself has a record feature if that's what you want.
     *
     * @param string $id Channel/Bridge ID
     * @param string $name (required) Recording's filename
     * @param string $format (required) Format to encode audio in
     * @param int $maxDurationSeconds Maximum duration of the recording, in seconds. 0 for no limit
     * @param int $maxSilenceSeconds Maximum duration of silence, in seconds. 0 for no limit
     * @param string $ifExists = fail - Action to take if a recording with the same name already exists.
     * @param boolean $beep string = fail - Action to take if a recording with the same name already exists.
     * @param string $terminateOn none - DTMF input to terminate recording
     * @return LiveRecording
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function record(
        $id,
        $name,
        $format,
        $maxDurationSeconds = null,
        $maxSilenceSeconds = null,
        $ifExists = null,
        $beep = null,
        $terminateOn = null
    ) {
        $uri = "{$this->getType()}/$id/record";
        try {
            $response = $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'name' => $name,
                    'format' => $format,
                    'maxDurationSeconds' => $maxDurationSeconds,
                    'maxSilenceSeconds' => $maxSilenceSeconds,
                    'ifExists' => $ifExists,
                    'beep' => $beep,
                    'terminateOn' => $terminateOn,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new LiveRecording($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }
}
