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

use GuzzleHttp\Exception\RequestException;
use phparia\Client\AriClientAware;
use phparia\Exception\NotFoundException;
use phparia\Resources\Mailbox;

/**
 * Mailboxes API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Mailboxes extends AriClientAware
{
    /**
     * List all mailboxes.
     *
     * @return Mailbox[]
     */
    public function getMailboxes()
    {
        $uri = 'mailboxes';
        $response = $this->client->getEndpoint()->get($uri);

        $mailboxes = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $mailbox) {
            $mailboxes[] = new Mailbox($mailbox);
        }

        return $mailboxes;
    }

    /**
     * Retrieve the current state of a mailbox.
     *
     * @param string $mailboxName Name of the mailbox
     * @return Mailbox
     * @throws NotFoundException
     */
    public function getMailbox($mailboxName)
    {
        $uri = "mailboxes/$mailboxName";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Mailbox(\GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Change the state of a mailbox. (Note - implicitly creates the mailbox).
     *
     * @param string $mailboxName Name of the mailbox
     * @param int $oldMessages (required) Count of old messages in the mailbox
     * @param int $newMessages (required) Count of new messages in the mailbox
     * @throws NotFoundException
     */
    public function updateMailbox($mailboxName, $oldMessages, $newMessages)
    {
        $uri = "mailboxes/$mailboxName";
        try {
            $this->client->getEndpoint()->put($uri, [
                'form_params' => [
                    'newMessages' => $newMessages,
                    'oldMessages' => $oldMessages,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Destroy a mailbox.
     *
     * @param string $mailboxName Name of the mailbox
     * @throws NotFoundException
     */
    public function deleteMailbox($mailboxName)
    {
        $uri = "mailboxes/$mailboxName";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }
}
