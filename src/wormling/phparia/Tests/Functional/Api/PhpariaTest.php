<?php

namespace {

    use Devristo\Phpws\Client\WebSocket;
    use phparia\Client\AmiClient;
    use phparia\Client\AriClient;
    use phparia\Tests\Functional\PhpariaTestCase;
    use React\EventLoop\LoopInterface;
    use Zend\Log\LoggerInterface;

    class PhpariaTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetEventLoop()
        {
            $eventLoop = $this->client->getEventLoop();

            $this->assertTrue($eventLoop instanceof LoopInterface);
        }

        /**
         * @test
         */
        public function canGetWsClient()
        {
            $wsClient = $this->client->getWsClient();

            $this->assertTrue($wsClient instanceof WebSocket);
        }

        /**
         * @test
         */
        public function canGetLogger()
        {
            $wsClient = $this->client->getLogger();

            $this->assertTrue($wsClient instanceof LoggerInterface);
        }

        /**
         * @test
         */
        public function canGetAriClient()
        {
            $ariClient = $this->client->getAriClient();

            $this->assertTrue($ariClient instanceof AriClient);
        }

        /**
         * @test
         */
        public function canGetAmiClient()
        {
            $amiClient = $this->client->getAmiClient();

            $this->assertTrue($amiClient instanceof AmiClient);
        }
    }
}