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
 * Channel variable changed.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class ChannelVarset extends Event
{
    /**
     * @var Channel (optional) - The channel on which the variable was set. If missing, the variable is a global variable.
     */
    private $channel;

    /**
     * @var string The new value of the variable. 
     */
    private $value;

    /**
     * @var string The variable that changed. 
     */
    private $variable;

    /**
     * @return Channel (optional) - The channel on which the variable was set. If missing, the variable is a global variable.
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string The new value of the variable.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string The variable that changed.
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param string $jsonResponse
     */
    public function __construct($jsonResponse)
    {
        parent::__construct($jsonResponse);

        $this->channel = property_exists($this->response, 'channel') ? $this->response->channel : null;
        $this->value = property_exists($this->response, 'value') ? $this->response->value : null;
        $this->variable = property_exists($this->response, 'variable') ? $this->response->variable : null;
    }

}
