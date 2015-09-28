<?php

namespace {

    use phparia\Events\ChannelUserevent;
    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class EventsTest extends PhpariaTestCase
    {
        /**
         * @test
         * @todo Test this properly once it's fully supported
         */
        public function canGetEvents()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                try {
                    $this->client->events()->getEvents($this->client->getStasisApplicationName());
                } catch(\Pest_ClientError $ignore) {

                }
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

//        public function canCreateUserEvent()
//        {
//            $this->client->onStasisStart(function (StasisStart $event) {
//                $event->getChannel()->answer();
//                $bridge = $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
//                $bridge->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
//                $event->getChannel()->onceChannelUserevent(function(ChannelUserevent $channelUserevent){
//                    $this->client->stop();
//                });
//                $this->client->events()->createUserEvent('USER_EVENT_NAME', $this->client->getStasisApplicationName(),
//                    "channel:{$event->getChannel()->getId()}", array('USER_EVENT_VARIABLE' => 'USER_EVENT_VALUE'));
//            });
//            $this->client->getAriClient()->onConnect(function () {
//                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
//                    $this->client->getStasisApplicationName());
//            });
//            $this->client->run();
//        }
    }
}