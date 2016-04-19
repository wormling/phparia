<?php

namespace {

    use Clue\React\Ami\ActionSender;
    use Clue\React\Ami\Client;
    use phparia\Client\AmiClient;
    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class AmiClientTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetClient()
        {
            $this->markTestIncomplete('Timing issue with AMI client being ready on stasis start.');
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                sleep(1);
                if ($this->client->getAmiClient()->getClient() instanceof Client) {
                    $success = true;
                }
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
            $this->assertTrue($success);
        }

        /**
         * @test
         */
        public function canGetActionSender()
        {
            $this->markTestIncomplete('Timing issue with AMI client being ready on stasis start.');
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                sleep(1);
                if ($this->client->getAmiClient()->getActionSender() instanceof ActionSender) {
                    $success = true;
                }
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
            $this->assertTrue($success);
        }
    }
}