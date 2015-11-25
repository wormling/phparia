<?php

namespace {

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
        }   }
}