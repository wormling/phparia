<?php

namespace {

    use Devristo\Phpws\Client\WebSocket;
    use phparia\Api\Asterisk;
    use phparia\Api\Recordings;
    use phparia\Api\Sounds;
    use phparia\Client\AmiClient;
    use phparia\Client\AriClient;
    use phparia\Events\Event;
    use phparia\Events\StasisStart;
    use phparia\Resources\Application;
    use phparia\Resources\Bridge;
    use phparia\Resources\Channel;
    use phparia\Resources\DeviceState;
    use phparia\Resources\Endpoint;
    use phparia\Resources\Mailbox;
    use phparia\Resources\Playback;
    use phparia\Tests\Functional\PhpariaTestCase;
    use React\EventLoop\LoopInterface;
    use Zend\Log\LoggerInterface;

    class PhpariaTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canRunAndStop()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $success = true;
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

        /**
         * @test
         */
        public function onStasisEndEvent()
        {
            $this->markTestIncomplete('Timing issue with AMI client being ready on stasis start.');
            $success = false;

            $this->client->onStasisEnd(function () use (&$success) {
                $success = true;
                $this->client->stop();
            });
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                sleep(1);
                $this->client->getEventLoop()->tick();
                $event->getChannel()->hangup();
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
        public function canGetApplication()
        {
            $applications = $this->client->applications();

            foreach ($applications as $application) {
                $this->assertTrue($application instanceof Application);
            }
        }

        /**
         * @test
         */
        public function canGetAsterisk()
        {
            $asterisk = $this->client->asterisk();

            $this->assertTrue($asterisk instanceof Asterisk);
        }

        /**
         * @test
         */
        public function canGetBridges()
        {
            $bridges = $this->client->bridges();

            foreach ($bridges as $bridge) {
                $this->assertTrue($bridge instanceof Bridge);
            }
        }

        /**
         * @test
         */
        public function canGetChannels()
        {
            $channels = $this->client->channels();

            foreach ($channels as $channel) {
                $this->assertTrue($channel instanceof Channel);
            }
        }

        /**
         * @test
         */
        public function canGetDeviceStates()
        {
            $deviceStates = $this->client->deviceStates();

            foreach ($deviceStates as $deviceState) {
                $this->assertTrue($deviceState instanceof DeviceState);
            }
        }

        /**
         * @test
         */
        public function canGetEndPoints()
        {
            $endPoints = $this->client->endPoints();

            foreach ($endPoints as $endPoint) {
                $this->assertTrue($endPoint instanceof Endpoint);
            }
        }

        /**
         * @test
         */
        public function canGetEvents()
        {
            $events = $this->client->events();

            foreach ($events as $event) {
                $this->assertTrue($event instanceof Event);
            }
        }

        /**
         * @test
         */
        public function canGetMailboxes()
        {
            $mailboxes = $this->client->mailboxes();

            foreach ($mailboxes as $mailbox) {
                $this->assertTrue($mailbox instanceof Mailbox);
            }
        }

        /**
         * @test
         */
        public function canGetPlaybacks()
        {
            $playbacks = $this->client->playbacks();

            foreach ($playbacks as $playback) {
                $this->assertTrue($playback instanceof Playback);
            }
        }

        /**
         * @test
         */
        public function canGetRecordings()
        {
            $recordings = $this->client->recordings();

            $this->assertTrue($recordings instanceof Recordings);
        }

        /**
         * @test
         */
        public function canGetSounds()
        {
            $sounds = $this->client->sounds();

            $this->assertTrue($sounds instanceof Sounds);
        }
    }
}