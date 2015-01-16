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

/**
 * A media file that may be played back.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Sound extends Response
{
    /**
     * @var FormatLangPair[] The formats and languages in which this sound is available.
     */
    private $formats;

    /**
     * @var string Sound's identifier. 
     */
    private $id;

    /**
     * @var string (optional) - Text description of the sound, usually the words spoken. 
     */
    private $text;

    /**
     * @return string The formats and languages in which this sound is available.
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @return string Sound's identifier. 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string (optional) - Text description of the sound, usually the words spoken. 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);
        
        foreach ($this->response->formats as $key => $format) {
            $this->formats[$key] = new FormatLangPair($format);
        }
        $this->id = $this->response->id;
        $this->text = property_exists($this->response, 'text') ? $this->response->text : null;
    }

}
