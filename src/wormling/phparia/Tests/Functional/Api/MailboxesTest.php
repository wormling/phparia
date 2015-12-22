<?php

namespace {

    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class MailboxesTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canUpdateMailbox()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->mailboxes()->updateMailbox('TEST_MAILBOX', 0, 0);
                $this->client->mailboxes()->updateMailbox('TEST_MAILBOX2', 0, 0);
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

        /**
         * @test
         */
        public function canGetMailboxes()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $mailboxes = $this->client->mailboxes()->getMailboxes();
                foreach ($mailboxes as $mailbox) {
                    $this->assertInstanceOf('phparia\Resources\Mailbox', $mailbox);
                }
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

        /**
         * @test
         */
        public function canGetMailbox()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $mailbox = $this->client->mailboxes()->getMailbox('TEST_MAILBOX');
                $this->assertInstanceOf('phparia\Resources\Mailbox', $mailbox);
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canDeleteMailbox()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->mailboxes()->deleteMailbox('TEST_MAILBOX');
                $this->client->mailboxes()->deleteMailbox('TEST_MAILBOX2');
                $this->client->mailboxes()->getMailbox('TEST_MAILBOX');
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canDeleteMailboxThrowNotFoundException()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->mailboxes()->deleteMailbox('TEST_MAILBOX');
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }
    }
}