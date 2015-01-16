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

namespace phparia\Entity;

/**
 * Description of Playback
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Playback extends Response
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
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);

        $this->id = $this->response->id;
        $this->language = property_exists($this->response, 'language') ? $this->response->language : null;
        $this->mediaUri = $this->response->media_uri;
        $this->state = $this->response->state;
        $this->targetUri = $this->response->target_uri;
    }

}
