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
 * Description of Variable
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Variable extends Response
{
    /**
     * @var string The value of the variable requested
     */
    private $value;

    /**
     * @return string The value of the variable requested
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->value = $this->getResponseValue('value');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->value);
    }

}
