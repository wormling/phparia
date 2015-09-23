<?php

namespace {
    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class EventsTest extends PhpariaTestCase
    {
        /**
         * @todo
         */
        public function canGetEvents()
        {

        }

        /**
         * @todo
         */
        public function canCreateUserEvent()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $event->getChannel()->onceChannelUserevent(function(){
                    $this->client->stop();
                });
                $this->client->events()->createUserEvent('USER_EVENT_NAME', $this->client->getStasisApplicationName(),
                    "channel:{$event->getChannel()->getId()}", array('USER_EVENT_VARIABLE' => 'USER_EVENT_VALUE'));
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());

            });
            $this->client->run();
        }
    }
}