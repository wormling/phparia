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
 * Details of an Asterisk module
 *
 * @author Eric Smith <eric2733@gmail.com>
 */
class Module extends Response
{
    /**
     * @var string The description of this module
     */
    private $description;

    /**
     * @var string The name of this module
     */
    private $name;

    /**
     * @var string The running status of this module
     */
    private $status;

    /**
     * @var string The support state of this module
     */
    private $supportLevel;

    /**
     * @var integer The number of times this module is being used
     */
    private $useCount;

    /**
     * @return string The description of this module
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string The name of this module
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string The running status of this module
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string The support state of this module
     */
    public function getType()
    {
        return $this->supportLevel;
    }

    /**
     * @return integer The number of times this module is being used
     */
    public function getUseCount()
    {
        return $this->useCount;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->description = $this->getResponseValue('description');
        $this->name = $this->getResponseValue('name');
        $this->status = $this->getResponseValue('status');
        $this->supportLevel = $this->getResponseValue('support_level');
        $this->useCount = $this->getResponseValue('use_count');
    }

}
