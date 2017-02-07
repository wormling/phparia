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
 * Description of Playback
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Playback extends Resource
{
    /**
     * @var string ID for this playback operation
     */
    private $id;

    /**
     * @var string (optional) - For media types that support multiple languages, the language requested for playback.
     */
    private $language;

    /**
     * @var string URI for the media to play back.
     */
    private $mediaUri;

    /**
     * @var string Current state of the playback operation.
     */
    private $state;

    /**
     * @var string URI for the channel or bridge to play the media on
     */
    private $targetUri;

    /**
     * @return string ID for this playback operation
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string (optional) - For media types that support multiple languages, the language requested for playback.
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string URI for the media to play back.
     */
    public function getMediaUri()
    {
        return $this->mediaUri;
    }

    /**
     * @return string Current state of the playback operation.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string URI for the channel or bridge to play the media on
     */
    public function getTargetUri()
    {
        return $this->targetUri;
    }

    /**
     * @param callable $callback
     */
    public function onPlaybackStarted(callable $callback)
    {
        $this->on(Event::PLAYBACK_STARTED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function oncePlaybackStarted(callable $callback)
    {
        $this->once(Event::PLAYBACK_STARTED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onPlaybackContinuing(callable $callback)
    {
        $this->on(Event::PLAYBACK_CONTINUING.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function oncePlaybackContinuing(callable $callback)
    {
        $this->once(Event::PLAYBACK_CONTINUING.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function onPlaybackFinished(callable $callback)
    {
        $this->on(Event::PLAYBACK_FINISHED.'_'.$this->getId(), $callback);
    }

    /**
     * @param callable $callback
     */
    public function oncePlaybackFinished(callable $callback)
    {
        $this->once(Event::PLAYBACK_FINISHED.'_'.$this->getId(), $callback);
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->id = $this->getResponseValue('id');
        $this->language = $this->getResponseValue('language');
        $this->mediaUri = $this->getResponseValue('media_uri');
        $this->state = $this->getResponseValue('state');
        $this->targetUri = $this->getResponseValue('target_uri');
    }

}
