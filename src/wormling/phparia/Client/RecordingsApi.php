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

use phparia\Entity\LiveRecording;
use phparia\Entity\StoredRecording;

/**
 * Recordings API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class RecordingsApi extends Base
{

    /**
     * List recordings that are complete.
     * 
     * @return StoredRecording[]
     */
    public function getRecordings()
    {
        $uri = '/recordings/stored';
        $response = $this->client->getAriEndpoint()->get($uri);

        $recordings = [];
        foreach ($response as $recording) {
            $recordings[] = new StoredRecording($recording);
        }

        return $recordings;
    }

    /**
     * Get a stored recording's details.
     * 
     * @param string $recordingName The name of the recording
     * @return StoredRecording
     */
    public function getRecording($recordingName)
    {
        $uri = "/recordings/stored/$recordingName";
        $response = $this->client->getAriEndpoint()->get($uri);

        return new StoredRecording($response);
    }

    /**
     * Delete a stored recording.
     * 
     * @param string $recordingName The name of the recording
     * @return StoredRecording
     */
    public function deleteRecording($recordingName)
    {
        $uri = "/recordings/stored/$recordingName";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Copy a stored recording.
     * 
     * @param string $recordingName The name of the recording to copy
     * @param string $destinationRecordingName (required) The destination name of the recording
     * @return StoredRecording
     */
    public function copyRecording($recordingName, $destinationRecordingName)
    {
        $uri = "/recordings/stored/$recordingName/copy";
        $response = $this->client->getAriEndpoint()->post($uri, array(
            'destinationRecordingName' => $destinationRecordingName,
        ));

        return new StoredRecording($response);
    }

    /**
     * Get live recording
     * 
     * @param $recordingName The name of the recording
     * @return LiveRecording
     */
    public function getLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName";
        $response = $this->client->getAriEndpoint()->get($uri);

        return new LiveRecording($response);
    }

    /**
     * Stop a live recording and discard it.
     * 
     * @param string $recordingName The name of the recording
     */
    public function deleteLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Stop a live recording and store it.
     * 
     * @param string $recordingName The name of the recording
     */
    public function stopLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName/stop";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Pause a live recording. Pausing a recording suspends silence detection, which will be restarted 
     * when the recording is unpaused. Paused time is not included in the accounting for 
     * maxDurationSeconds.
     * 
     * @param string $recordingName The name of the recording
     */
    public function pauseLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName/pause";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Unause a live recording.
     * 
     * @param string $recordingName The name of the recording
     */
    public function unpauseLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName/pause";
        $this->client->getAriEndpoint()->delete($uri);
    }

    /**
     * Mute a live recording. Muting a recording suspends silence detection, which will be restarted when the recording is unmuted.
     * 
     * @param string $recordingName The name of the recording
     */
    public function muteLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName/mute";
        $this->client->getAriEndpoint()->post($uri);
    }

    /**
     * Unmute a live recording.
     * 
     * @param string $recordingName The name of the recording
     */
    public function unmuteLiveRecording($recordingName)
    {
        $uri = "/recordings/live/$recordingName/mute";
        $this->client->getAriEndpoint()->delete($uri);
    }

}
