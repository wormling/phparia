<?php

/*
 * Copyright 2017 Brian Smith <wormling@gmail.com>.
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
 * A key/value pair that makes up part of a configuration object.
 *
 * @author Eric Smith <eric2733@gmail.com>
 */
class ConfigTuple extends Response
{
    /**
     * @var string A configuration object attribute.
     */
    private $attribute;

    /**
     * @var string The value for the attribute.
     */
    private $value;

    /**
     * @return string A configuration object attribute.
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return string The value for the attribute.
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

        $this->attribute = $this->getResponseValue('attribute');
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
