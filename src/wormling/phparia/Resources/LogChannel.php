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
 * Details of an Asterisk log channel
 *
 * @author Eric Smith <eric2733@gmail.com>
 */
class LogChannel extends Response
{
    /**
     * @var string The log channel path
     */
    private $channel;

    /**
     * @var string The various log levels
     */
    private $configuration;

    /**
     * @var string Whether or not a log type is enabled
     */
    private $status;

    /**
     * @var string Types of logs for the log channel
     */
    private $type;

    /**
     * @return string The log channel path
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string The various log levels
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string Whether or not a log type is enabled
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string Types of logs for the log channel
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->channel = $this->getResponseValue('channel');
        $this->configuration = $this->getResponseValue('configuration');
        $this->status = $this->getResponseValue('status');
        $this->type = $this->getResponseValue('type');
    }

}
