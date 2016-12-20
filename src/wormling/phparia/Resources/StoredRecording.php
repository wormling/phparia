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
 * A past recording that may be played back.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class StoredRecording extends Response
{
    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->format = $this->getResponseValue('format');
        $this->name = $this->getResponseValue('name');
    }

}
