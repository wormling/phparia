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
 * Channel changed Caller ID.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelCallerId extends Event
{
    /**
     * @var int The integer representation of the Caller Presentation value.
     */
    private $callerPresentation;
    
    /**
     * @var string The text representation of the Caller Presentation value.
     */
    private $callerPresentationTxt;
    
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @return int The integer representation of the Caller Presentation value.
     */
    public function getCallerPresentation()
    {
        return $this->callerPresentation;
    }
    
    /**
     * @return string The text representation of the Caller Presentation value.
     */
    public function getCallerPresentationTxt()
    {
        return $this->callerPresentationTxt;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);

        $this->callerPresentation = $this->response->caller_presentation;
        $this->callerPresentationTxt = $this->response->caller_presentation_txt;
        $this->channel = $this->response->channel;
    }

}
