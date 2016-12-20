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
 * Effective user/group id
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class SetId extends Response
{
    /**
     * @var string Effective user id.
     */
    private $user;

    /**
     * @var string Effective group id.
     */
    private $group;

    /**
     * @return string Effective user id.
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string Effective group id.
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->user = $this->getResponseValue('user');
        $this->group = $this->getResponseValue('group');
    }

}
