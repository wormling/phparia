<?php

namespace {

    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;
    use React\EventLoop\LoopInterface;
    use Zend\Log\LoggerInterface;

    class AriClientTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetEventLoop()
        {
            $eventLoop = $this->client->getAriClient()->getEventLoop();

            $this->assertTrue($eventLoop instanceof LoopInterface);
        }

        /**
         * @test
         */
        public function canGetLogger()
        {
            $wsClient = $this->client->getAriClient()->getLogger();

            $this->assertTrue($wsClient instanceof LoggerInterface);
        }

        /**
         * @test
         */
        public function canCallOnHandshake()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->stop();
            });
            $this->client->getAriClient()->onHandshake(function () use (&$success) {
                $success = true;
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
        public function canCallOnRequest()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->stop();
            });
            $this->client->getAriClient()->onRequest(function () use (&$success) {
                $success = true;
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
        public function canCallOnClose()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->stop();
            });
            $this->client->getAriClient()->onClose(function () use (&$success) {
                $success = true;
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