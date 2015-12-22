<?php

namespace {

    use phparia\Api\PlaybackList;
    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class PlaybackListTest extends PhpariaTestCase
    {
        /**
         * @test
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canStop()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $playbackList = new PlaybackList($this->client);
                $playbackList->append($event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null,
                    'TEST_PLAYBACK_LIST1'));
                $playbackList->append($event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null,
                    'TEST_PLAYBACK_LIST2'));
                $playbackList->append($event->getChannel()->playMediaWithId('sound:demo-abouttotry', null, null, null,
                    'TEST_PLAYBACK_LIST3'));
                sleep(1);
                $playbackList->stop();
                sleep(1);
                $this->client->playbacks()->getPlayback('TEST_PLAYBACK_LIST1');
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
        public function appendCanRemovePlaybackAfterFinished()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $playbackList = new PlaybackList($this->client);
                $playbackList->append($playback = $event->getChannel()->playMedia('sound:silence/1'));
                sleep(2);
                $this->client->getEventLoop()->tick();
                $this->assertFalse((bool)array_search($playback, $playbackList->getArrayCopy()));
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
        public function offsetSetCanRemovePlaybackAfterFinished()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $playbackList = new PlaybackList($this->client);
                $playbackList->offsetSet(0, $playback = $event->getChannel()->playMedia('sound:silence/1'));
                sleep(2);
                $this->client->getEventLoop()->tick();
                $this->assertFalse((bool)array_search($playback, $playbackList->getArrayCopy()));
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
         * @expectedException \InvalidArgumentException
         */
        public function appendCanThrowInvalidArgumentException()
        {
            $playbackList = new PlaybackList($this->client);
            $playbackList->append('invalid argument');
        }

        /**
         * @test
         * @expectedException \InvalidArgumentException
         */
        public function offsetSetCanThrowInvalidArgumentException()
        {
            $playbackList = new PlaybackList($this->client);
            $playbackList->offsetSet(0, 'invalid argument');
        }
    }
}