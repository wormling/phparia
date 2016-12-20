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
 * Error event sent when required params are missing.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class MissingParams extends Response
{
    /**
     * @var string Indicates the type of this message.
     */
    private $type;

    /**
     * @var array A list of the missing parameters
     */
    private $params;

    /**
     * @return string Indicates the type of this message.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array A list of the missing parameters
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->type = $this->getResponseValue('type');
        $this->params = $this->getResponseValue('params');
    }
}
