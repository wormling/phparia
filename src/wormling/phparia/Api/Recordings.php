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
use phparia\Exception\ConflictException;
use phparia\Exception\NotFoundException;
use phparia\Resources\LiveRecording;
use phparia\Resources\StoredRecording;

/**
 * Recordings API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Recordings extends AriClientAware
{
    /**
     * List recordings that are complete.
     *
     * @return StoredRecording[]
     */
    public function getRecordings()
    {
        $uri = 'recordings/stored';
        $response = $this->client->getEndpoint()->get($uri);

        $recordings = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $recording) {
            $recordings[] = new StoredRecording($recording);
        }

        return $recordings;
    }

    /**
     * Get a stored recording's details.
     *
     * @param string $recordingName The name of the recording
     * @return StoredRecording
     * @throws NotFoundException
     */
    public function getRecording($recordingName)
    {
        $uri = "recordings/stored/$recordingName";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new StoredRecording(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Delete a stored recording.
     *
     * @param string $recordingName The name of the recording
     * @return StoredRecording
     * @throws NotFoundException
     */
    public function deleteRecording($recordingName)
    {
        $uri = "recordings/stored/$recordingName";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Copy a stored recording.
     *
     * @param string $recordingName The name of the recording to copy
     * @param string $destinationRecordingName (required) The destination name of the recording
     * @return StoredRecording
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function copyRecording($recordingName, $destinationRecordingName)
    {
        $uri = "recordings/stored/$recordingName/copy";
        try {
            $response = $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'destinationRecordingName' => $destinationRecordingName,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new StoredRecording(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Get live recording
     *
     * @param string $recordingName The name of the recording
     * @return LiveRecording
     * @throws NotFoundException
     */
    public function getLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new LiveRecording($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Stop a live recording and discard it.
     *
     * @param string $recordingName The name of the recording
     * @throws NotFoundException
     */
    public function deleteLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Stop a live recording and discard it.
     *
     * @param string $recordingName The name of the recording
     * @throws NotFoundException
     */
    public function cancelLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Stop a live recording and store it.
     *
     * @param string $recordingName The name of the recording
     * @throws NotFoundException
     */
    public function stopLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName/stop";
        try {
            $this->client->getEndpoint()->post($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Pause a live recording. Pausing a recording suspends silence detection, which will be restarted
     * when the recording is unpaused. Paused time is not included in the accounting for
     * maxDurationSeconds.
     *
     * @param string $recordingName The name of the recording
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function pauseLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName/pause";
        try {
            $this->client->getEndpoint()->post($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Unause a live recording.
     *
     * @param string $recordingName The name of the recording
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function unpauseLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName/pause";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Mute a live recording. Muting a recording suspends silence detection, which will be restarted when the recording is unmuted.
     *
     * @param string $recordingName The name of the recording
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function muteLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName/mute";
        try {
            $this->client->getEndpoint()->post($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Unmute a live recording.
     *
     * @param string $recordingName The name of the recording
     * @throws ConflictException
     * @throws NotFoundException
     */
    public function unmuteLiveRecording($recordingName)
    {
        $uri = "recordings/live/$recordingName/mute";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }
}
