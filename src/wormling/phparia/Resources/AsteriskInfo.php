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
 * Asterisk system information
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class AsteriskInfo extends Response
{
    /**
     * @var BuildInfo Info about how Asterisk was built
     */
    private $buildInfo;

    /**
     * @var ConfigInfo Info about Asterisk configuration
     */
    private $configInfo;

    /**
     * @var StatusInfo Info about Asterisk status
     */
    private $statusInfo;

    /**
     * @var SystemInfo Info about Asterisk
     */
    private $systemInfo;

    public function getBuildInfo()
    {
        return $this->buildInfo;
    }

    public function getConfigInfo()
    {
        return $this->configInfo;
    }

    public function getStatusInfo()
    {
        return $this->statusInfo;
    }

    public function getSystemInfo()
    {
        return $this->systemInfo;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->buildInfo = $this->getResponseValue('build', 'phparia\Resources\BuildInfo');
        $this->configInfo = $this->getResponseValue('config', 'phparia\Resources\ConfigInfo');
        $this->statusInfo = $this->getResponseValue('statusInfo', 'phparia\Resources\StatusInfo');
        $this->systemInfo = $this->getResponseValue('systemInfo', 'phparia\Resources\SystemInfo');
    }

}
