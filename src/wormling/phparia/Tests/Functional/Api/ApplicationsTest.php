<?php

namespace {

    use phparia\Resources\Bridge;
    use phparia\Tests\Functional\PhpariaTestCase;

    class ApplicationsTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetApplications()
        {
            $this->client->getAriClient()->onConnect(function () {
                $applications = $this->client->applications()->getApplications();
                foreach ($applications as $application) {
                    $this->assertInstanceOf('phparia\Resources\Application', $application);
                }
                $this->client->stop();
            });
            $this->client->run();
        }

        /**
         * @test
         */
        public function canGetApplication()
        {
            $this->client->getAriClient()->onConnect(function () {
                $application = $this->client->applications()->getApplication($this->client->getStasisApplicationName());
                $this->assertInstanceOf('phparia\Resources\Application', $application);
                $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());
                $this->client->stop();
            });
            $this->client->run();
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canGetApplicationThrowNotFoundException()
        {
            $this->client->getAriClient()->onConnect(function () {
                $this->client->applications()->getApplication('THIS_APPLICATION_NAME_WILL_NOT_EXIST');
                $this->client->stop();
            });
            $this->client->run();
        }

        /**
         * @test
         */
        public function canSubscribe()
        {
            $this->client->getAriClient()->onConnect(function () {
                $bridge = $this->client->bridges()->createBridge('BRIDGE_ID', null, 'BRIDGE_NAME');
                $application = $this->client->applications()->subscribe($this->client->getStasisApplicationName(),
                    "bridge:{$bridge->getId()}");
                $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());
                $this->client->stop();
            });
            $this->client->run();
        }

        /**
         * @test
         * @expectedException \phparia\Exception\InvalidParameterException
         */
        public function canSubscribeThrowInvalidParameterException()
        {
            $this->client->getAriClient()->onConnect(function () {
                try {
                    $this->client->applications()->subscribe($this->client->getStasisApplicationName(), 'bad:format');
                } catch (\phparia\Exception\InvalidParameterException $e) {
                    $this->client->stop();
                    throw($e);
                }
            });
            $this->client->run();
        }
//
//        /**
//         * @test
//         * @expectedException \phparia\Exception\NotFoundException
//         */
//        public function canSubscribeThrowNotFoundException()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                try {
//                    $this->client->applications()->subscribe('THIS_APPLICATION_NAME_WILL_NOT_EXIST',
//                        "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
//                } catch (\phparia\Exception\NotFoundException $e) {
//                    $this->client->stop();
//                    throw($e);
//                }
//            });
//            $this->client->run();
//        }
//
//        /**
//         * @test
//         * @expectedException \phparia\Exception\UnprocessableEntityException
//         */
//        public function canSubscribeThrowUnprocessableEntityException()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                try {
//                    $this->client->applications()->subscribe($this->client->getStasisApplicationName(),
//                        "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
//                } catch (\phparia\Exception\UnprocessableEntityException $e) {
//                    $this->client->stop();
//                    throw($e);
//                }
//            });
//            $this->client->run();
//        }
//
//        /**
//         * @test
//         */
//        public function canUnsubscribe()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                $bridge = $this->client->bridges()->createBridge('BRIDGE_ID', null, 'BRIDGE_NAME');
//                $this->client->applications()->subscribe($this->client->getStasisApplicationName(),
//                    "bridge:{$bridge->getId()}");
//                $application = $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(),
//                    "bridge:{$bridge->getId()}");
//                $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());
//                $this->client->stop();
//            });
//            $this->client->run();
//        }
//
//        /**
//         * @test
//         * @expectedException \phparia\Exception\InvalidParameterException
//         */
//        public function canUnsubscribeThrowInvalidParameterException()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                try {
//                    $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), 'bad:format');
//                } catch (\phparia\Exception\InvalidParameterException $e) {
//                    $this->client->stop();
//                    throw($e);
//                }
//            });
//            $this->client->run();
//        }
//
//        /**
//         * @test
//         * @expectedException \phparia\Exception\NotFoundException
//         */
//        public function canUnsubscribeThrowNotFoundException()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                try {
//                    $this->client->applications()->unsubscribe('THIS_APPLICATION_NAME_WILL_NOT_EXIST',
//                        "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
//                } catch (\phparia\Exception\NotFoundException $e) {
//                    $this->client->stop();
//                    throw($e);
//                }
//            });
//            $this->client->run();
//        }
//
//        /**
//         * @todo Enable this again once it actually works from asterisk
//         * @depends canSubscribe
//         * @param \phparia\Resources\Bridge $bridge
//         * @expectedException \phparia\Exception\ConflictException
//         */
//        public function canUnsubscribeThrowConflictException(Bridge $bridge)
//        {
//            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(),
//                "bridge:{$bridge->getId()}");
//            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(),
//                "bridge:{$bridge->getId()}");
//        }
//
//        /**
//         * @test
//         * @expectedException \phparia\Exception\UnprocessableEntityException
//         */
//        public function canUnsubscribeThrowUnprocessableEntityException()
//        {
//            $this->client->getAriClient()->onConnect(function () {
//                try {
//                    $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(),
//                        "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
//                } catch (\phparia\Exception\UnprocessableEntityException $e) {
//                    $this->client->stop();
//                    throw($e);
//                }
//            });
//            $this->client->run();
//        }
    }
}