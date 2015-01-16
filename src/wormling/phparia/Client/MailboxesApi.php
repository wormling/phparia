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

namespace phparia\Client;

use phparia\Entity\Mailbox;

/**
 * Mailboxes API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class MailboxesApi
{

    /**
     * List all mailboxes.
     * 
     * @return Mailbox[]
     */
    public function getMailboxes()
    {
        $uri = '/mailboxes';
        $response = $this->client->getAriEndpoint()->get($uri);

        $mailboxes = [];
        foreach ($response as $mailbox) {
            $mailboxes[] = new Mailbox($mailbox);
        }

        return $mailboxes;
    }

    /**
     * Retrieve the current state of a mailbox.
     * 
     * @param string $mailboxName Name of the mailbox
     * @return Mailbox
     */
    public function getMailbox($mailboxName)
    {
        $uri = "/mailboxes/$mailboxName";
        $response = $this->client->getAriEndpoint()->get($uri);

        return new Mailbox($response);
    }

    /**
     * Change the state of a mailbox. (Note - implicitly creates the mailbox).
     * 
     * @param string $mailboxName Name of the mailbox
     * @param int $oldMessages (required) Count of old messages in the mailbox
     * @param int $newMessages (required) Count of new messages in the mailbox
     */
    public function updateMailbox($mailboxName, $oldMessages, $newMessages)
    {
        $uri = "/mailboxes/$mailboxName";
        $this->client->getAriEndpoint()->put($uri, array(
            'newMessages' => $newMessages,
            'oldMessages' => $oldMessages,
        ));
    }
    
    /**
     * Destroy a mailbox.
     * 
     * @param string $mailboxName Name of the mailbox
     */
    public function deleteMailbox($mailboxName)
    {
        $uri = "/mailboxes/$mailboxName";
        $this->client->getAriEndpoint()->delete($uri);
    }

}
