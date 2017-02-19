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

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use phparia\Client\AriClientAware;
use phparia\Exception\ConflictException;
use phparia\Exception\ForbiddenException;
use phparia\Exception\NotFoundException;
use phparia\Resources\AsteriskInfo;
use phparia\Resources\ConfigTuple;
use phparia\Resources\LogChannel;
use phparia\Resources\Module;
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
     * Retrieve a dynamic configuration object.
     *
     * @param string $configClass The configuration class containing dynamic configuration objects.
     * @param string $objectType The type of configuration object to retrieve.
     * @param string $id The unique identifier of the object to retrieve.
     * @return ConfigTuple[]
     * @throws NotFoundException
     */
    public function getObject($configClass, $objectType, $id)
    {
        $uri = "asterisk/config/dynamic/$configClass/$objectType/$id";

        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        $configTuples = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $configTuple) {
            $configTuples[] = new ConfigTuple($configTuple);
        }

        return $configTuples;
    }

    /**
     * Create or update a dynamic configuration object.
     *
     * @param string $configClass The configuration class containing dynamic configuration objects.
     * @param string $objectType The type of configuration object to retrieve.
     * @param string $id The unique identifier of the object to retrieve.
     * @param ConfigTuple[] $fields The body object should have a value that is a list of ConfigTuples, which provide the fields to update. Ex. [ { "attribute": "directmedia", "value": "false" } ]
     * @return ConfigTuple[]
     * @throws BadResponseException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function updateObject($configClass, $objectType, $id, $fields)
    {
        $uri = "asterisk/config/dynamic/$configClass/$objectType/$id";

        try {
            $response = $this->client->getEndpoint()->put($uri, [
                'form_params' => [
                    'fields' => array_map('strval', $fields)
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        $configTuples = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $configTuple) {
            $configTuples[] = new ConfigTuple($configTuple);
        }

        return $configTuples;
    }

    /**
     * Delete a dynamic configuration object.
     *
     * @param string $configClass The configuration class containing dynamic configuration objects.
     * @param string $objectType The type of configuration object to retrieve.
     * @param string $id The unique identifier of the object to retrieve.
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function deleteObject($configClass, $objectType, $id)
    {
        $uri = "asterisk/config/dynamic/$configClass/$objectType/$id";

        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

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
     * List Asterisk modules.
     *
     * @return Module[]
     */
    public function listModules()
    {
        $uri = 'asterisk/modules';

        $response = $this->client->getEndpoint()->get($uri);

        $modules = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $module) {
            $modules[] = new Module($module);
        }

        return $modules;
    }

    /**
     * Get Asterisk module information.
     *
     * @param string $moduleName Module's name
     * @return Module
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function getModule($moduleName)
    {
        $uri = "asterisk/module/$moduleName";

        $response = $this->client->getEndpoint()->get($uri);

        return new Module(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Load an Asterisk module.
     *
     * @param string $moduleName Module's name
     * @return Module
     * @throws ConflictException
     */
    public function loadModule($moduleName)
    {
        $uri = "asterisk/module/$moduleName";

        $this->client->getEndpoint()->post($uri);
    }

    /**
     * Unload an Asterisk module.
     *
     * @param string $moduleName Module's name
     * @return Module
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function unloadModule($moduleName)
    {
        $uri = "asterisk/module/$moduleName";

        $this->client->getEndpoint()->delete($uri);
    }

    /**
     * Reload an Asterisk module.
     *
     * @param string $moduleName Module's name
     * @return Module
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function reloadModule($moduleName)
    {
        $uri = "asterisk/module/$moduleName";

        $this->client->getEndpoint()->put($uri);
    }

    /**
     * Gets Asterisk log channel information.
     *
     * @return LogChannel[]
     */
    public function listLogChannels()
    {
        $uri = 'asterisk/logging';

        $response = $this->client->getEndpoint()->get($uri);

        $logChannels = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $logChannel) {
            $logChannels[] = new Module($logChannel);
        }

        return $logChannels;
    }

    /**
     * Adds a log channel.
     *
     * @param string $logChannelName
     * @param string $configuration
     * @throws InvalidParameterException
     * @throws ConflictException
     */
    public function addLog($logChannelName, $configuration)
    {
        $uri = "asterisk/logging/$logChannelName";

        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'configuration' => $configuration
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Deletes a log channel.
     *
     * @param string $logChannelName
     * @throws NotFoundException
     */
    public function deleteLog($logChannelName)
    {
        $uri = "asterisk/logging/$logChannelName";

        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Rotates a log channel.
     *
     * @param string $logChannelName
     * @throws NotFoundException
     */
    public function rotateLog($logChannelName)
    {
        $uri = "asterisk/logging/$logChannelName";

        try {
            $this->client->getEndpoint()->put($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Get the value of a global variable.
     *
     * @param string $variable (required) The variable to get
     * @return Variable
     * @throws InvalidParameterException
     */
    public function getGlobalVar($variable)
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
     * Get the value of a global variable.
     *
     * @param string $variable (required) The variable to get
     * @return Variable
     * @throws InvalidParameterException
     * @deprecated
     */
    public function getVariable($variable)
    {
        return $this->getGlobalVar($variable);
    }

    /**
     * Set the value of a global variable.
     *
     * @param string $variable (required) The variable to set
     * @param string $value The value to set the variable to
     * @throws InvalidParameterException
     */
    public function setGlobalVar($variable, $value = null)
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

    /**
     * Set the value of a global variable.
     *
     * @param string $variable (required) The variable to set
     * @param string $value The value to set the variable to
     * @throws InvalidParameterException
     * @deprecated
     */
    public function setVariable($variable, $value)
    {
        $this->setGlobalVar($variable, $value);
    }
}
