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
 * Base type for API call responses
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Response
{
    /**
     * The json_decoded message data from ARI
     *
     * @var object
     */
    protected $response;

    /**
     * @param string $response The raw json response response data from ARI
     */
    public function __construct($response)
    {
        if (is_array($response)) { // For some reason, playback is an array, so this fixes that problem
            $this->response = json_decode(json_encode($response), false);
        } elseif (is_object($response)) {
            $this->response = $response;
        } else {
            $this->response = json_decode($response);
        }
    }

    /**
     * @param $propertyName
     * @return mixed
     */
    protected function getResponseValue($propertyName)
    {
        return property_exists($this->response, $propertyName) ? $this->response->{$propertyName} : null;
    }
}
