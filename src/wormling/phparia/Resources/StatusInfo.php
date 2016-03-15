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

use DateTime;

/**
 * Info about Asterisk status
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class StatusInfo extends Response
{
    /**
     * @var DateTime Time when Asterisk was last reloaded.
     */
    private $lastReloadTime;

    /**
     * @var DateTime Time when Asterisk was started.
     */
    private $startupTime;

    /**
     * @return DateTime Time when Asterisk was last reloaded.
     */
    public function getLastReloadTime()
    {
        return $this->lastReloadTime;
    }

    /**
     * @return DateTime Time when Asterisk was started.
     */
    public function getStartupTime()
    {
        return $this->startupTime;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->lastReloadTime = $this->getResponseValue('last_reload_time');
        $this->startupTime = $this->getResponseValue('startup_time');
    }
}
