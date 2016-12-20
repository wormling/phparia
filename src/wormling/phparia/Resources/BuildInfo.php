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
 * Info about how Asterisk was built
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class BuildInfo extends Response
{
    /**
     * @var string Username that build Asterisk
     */
    private $user;

    /**
     * @var string Compile time options, or empty string if default.
     */
    private $options;

    /**
     * @var string Machine architecture (x86_64, i686, ppc, etc.)
     */
    private $machine;

    /**
     * @var string OS Asterisk was built on.
     */
    private $os;

    /**
     * @var string Kernel version Asterisk was built on.
     */
    private $kernel;

    /**
     * @var string Date and time when Asterisk was built.
     */
    private $date;

    /**
     * @return string Username that build Asterisk
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string Compile time options, or empty string if default.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string Machine architecture (x86_64, i686, ppc, etc.)
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * @return string OS Asterisk was built on.
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * @return string Kernel version Asterisk was built on.
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return string Date and time when Asterisk was built.
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->user = $this->getResponseValue('user');
        $this->options = $this->getResponseValue('options');
        $this->machine = $this->getResponseValue('machine');
        $this->os = $this->getResponseValue('os');
        $this->kernel = $this->getResponseValue('kernel');
        $this->date = $this->getResponseValue('date');
    }
}
