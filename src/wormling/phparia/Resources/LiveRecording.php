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

/**
 * A recording that is in progress
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class LiveRecording extends Resource
{
    /**
     * @var string (optional) - Cause for recording failure if failed
     */
    private $cause;

    /**
     * @var int (optional) - Duration in seconds of the recording
     */
    private $duration;

    /**
     * @var string Recording format (wav, gsm, etc.)
     */
    private $format;

    /**
     * @var string Base name for the recording
     */
    private $name;

    /**
     * @var int (optional) - Duration of silence, in seconds, detected in the recording. This is only available if the recording was initiated with a non-zero maxSilenceSeconds.
     */
    private $silenceDuration;

    /**
     * @var string
     */
    private $state;

    /**
     * @var int (optional) - Duration of talking, in seconds, detected in the recording. This is only available if the recording was initiated with a non-zero maxSilenceSeconds.
     */
    private $talkingDuration;

    /**
     * @var string URI for the channel or bridge being recorded
     */
    private $targetUri;

    /**
     * @return string (optional) - Cause for recording failure if failed
     */
    public function getCause()
    {
        return $this->cause;
    }

    /**
     * @return int (optional) - Duration in seconds of the recording
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return string Recording format (wav, gsm, etc.)
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string Base name for the recording
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int (optional) - Duration of silence, in seconds, detected in the recording. This is only available if the recording was initiated with a non-zero maxSilenceSeconds.
     */
    public function getSilenceDuration()
    {
        return $this->silenceDuration;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int (optional) - Duration of talking, in seconds, detected in the recording. This is only available if the recording was initiated with a non-zero maxSilenceSeconds.
     */
    public function getTalkingDuration()
    {
        return $this->talkingDuration;
    }

    /**
     * @return string URI for the channel or bridge being recorded
     */
    public function getTargetUri()
    {
        return $this->targetUri;
    }

    /**
     * @param callable $callback
     */
    public function onRecordingFailed(callable $callback)
    {
        $this->on(Event::RECORDING_FAILED.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceRecordingFailed(callable $callback)
    {
        $this->once(Event::RECORDING_FAILED.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onRecordingFinished(callable $callback)
    {
        $this->on(Event::RECORDING_FINISHED.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceRecordingFinished(callable $callback)
    {
        $this->once(Event::RECORDING_FINISHED.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onRecordingStarted(callable $callback)
    {
        $this->on(Event::RECORDING_STARTED.'_'.$this->getName(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onceRecordingStarted(callable $callback)
    {
        $this->once(Event::RECORDING_STARTED.'_'.$this->getName(), $callback);
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->cause = $this->getResponseValue('cause');
        $this->duration = $this->getResponseValue('duration');
        $this->format = $this->getResponseValue('format');
        $this->name = $this->getResponseValue('name');
        $this->silenceDuration = $this->getResponseValue('silence_duration');
        $this->state = $this->getResponseValue('state');
        $this->talkingDuration = $this->getResponseValue('talking_duration');
        $this->targetUri = $this->getResponseValue('target_uri');
    }

}
