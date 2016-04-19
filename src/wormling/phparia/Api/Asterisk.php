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

namespace phparia\Api;

use GuzzleHttp\Exception\RequestException;
use phparia\Client\AriClientAware;
use phparia\Resources\AsteriskInfo;
use phparia\Resources\Variable;
use phparia\Exception\InvalidParameterException;

/**
 * Asterisk API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Asterisk extends AriClientAware
{
    const INFO_BUILD = 'build';
    const INFO_SYSTEM = 'system';
    const INFO_CONFIG = 'config';
    const INFO_STATUS = 'status';

    /**
     * Gets Asterisk system information.
     *
     * @param string $only Filter information returned.  Allows comma separated values.  Allowed values: build, system, config, status.
     * @return AsteriskInfo
     */
    public function getInfo($only = null)
    {
        if (empty($only)) {
            $uri = 'asterisk/info';
        } else {
            $uri = "asterisk/info?only=$only";
        }
        $response = $this->client->getEndpoint()->get($uri);

        return new AsteriskInfo(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Get the value of a global variable.
     *
     * @param string $variable (required) The variable to get
     * @return Variable
     * @throws InvalidParameterException
     */
    public function getVariable($variable)
    {
        $uri = "asterisk/variable?variable=$variable";

        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Variable(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Set the value of a global variable.
     *
     * @param string $variable (required) The variable to set
     * @param string $value The value to set the variable to
     * @throws InvalidParameterException
     */
    public function setVariable($variable, $value = null)
    {
        $uri = 'asterisk/variable';

        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'variable' => $variable,
                    'value' => $value
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }
}
