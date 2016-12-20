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
 * Represents the state of a mailbox.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Mailbox extends Response
{
    /**
     * @var string Name of the mailbox.
     */
    private $name;

    /**
     * @var int Count of new messages in the mailbox.
     */
    private $newMessages;

    /**
     * @var int Count of old messages in the mailbox.
     */
    private $oldMessages;

    /**
     * @return string Name of the mailbox.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int Count of new messages in the mailbox.
     */
    public function getNewMessages()
    {
        return $this->newMessages;
    }

    /**
     * @return int Count of old messages in the mailbox.
     */
    public function getOldMessages()
    {
        return $this->oldMessages;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->name = $this->getResponseValue('name');
        $this->newMessages = $this->getResponseValue('new_messages');
        $this->oldMessages = $this->getResponseValue('old_messages');
    }

}
