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
 * Dialplan location (context/extension/priority)
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class DialplanCep extends Response
{
    /**
     * @var string Context in the dialplan
     */
    private $context;

    /**
     * @var string Extension in the dialplan
     */
    private $exten;

    /**
     * @var int Priority in the dialplan
     */
    private $priority;

    /**
     * @return string Context in the dialplan
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string Extension in the dialplan
     */
    public function getExten()
    {
        return $this->exten;
    }

    /**
     * @return int Priority in the dialplan
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->context = $this->getResponseValue('context');
        $this->exten = $this->getResponseValue('exten');
        $this->priority = $this->getResponseValue('priority');
    }

}
