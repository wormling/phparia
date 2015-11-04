<?php

namespace {

    use phparia\Api\Playbacks;
    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class PlaybacksTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetPlayback()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null, 'TEST_PLAYBACK1');
                sleep(1);
                $playback = $this->client->playbacks()->getPlayback('TEST_PLAYBACK1');
                if ('TEST_PLAYBACK1' === $playback->getId()) {
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
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canStopPlayback()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null, 'TEST_PLAYBACK2');
                sleep(1);
                $playback = $this->client->playbacks()->getPlayback('TEST_PLAYBACK2');
                if ('TEST_PLAYBACK2' === $playback->getId()) {
                    $this->client->playbacks()->stopPlayback('TEST_PLAYBACK2');
                    $this->client->playbacks()->getPlayback('TEST_PLAYBACK2');
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
        public function canControlPlayback()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null, 'TEST_PLAYBACK3');
                sleep(1);
                $playback = $this->client->playbacks()->getPlayback('TEST_PLAYBACK3');
                if ('TEST_PLAYBACK3' === $playback->getId()) {
                    $this->client->playbacks()->controlPlayback('TEST_PLAYBACK3', Playbacks::OPERATION_PAUSE);
                    $playback = $this->client->playbacks()->getPlayback('TEST_PLAYBACK3');
                    if ($playback->getState() === Playbacks::OPERATION_PAUSE) {
                        $success = true;
                    }
                }
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