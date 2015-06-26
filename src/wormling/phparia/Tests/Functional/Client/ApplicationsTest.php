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
            $applications = $this->client->applications()->applications();
            foreach($applications as $application) {
                $this->assertInstanceOf('phparia\Resources\Application', $application);
            }
        }

        /**
         * @test
         */
        public function canGetApplication()
        {
            $application = $this->client->applications()->getApplication($this->client->getStasisApplicationName());
            $this->assertInstanceOf('phparia\Resources\Application', $application);
            $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canGetApplicationThrowNotFoundException()
        {
            $this->client->applications()->getApplication('THIS_APPLICATION_NAME_WILL_NOT_EXIST');
        }

        /**
         * @test
         * @return \phparia\Resources\Bridge
         */
        public function canSubscribe()
        {
            $bridge = $this->client->bridges()->createBridge('BRIDGE_ID', null, 'BRIDGE_NAME');
            $application = $this->client->applications()->subscribe($this->client->getStasisApplicationName(), "bridge:{$bridge->getId()}");
            $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());

            return $bridge;
        }

        /**
         * @test
         * @expectedException \phparia\Exception\InvalidParameterException
         */
        public function canSubscribeThrowInvalidParameterException()
        {
            $this->client->applications()->subscribe($this->client->getStasisApplicationName(), 'bad:format');
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canSubscribeThrowNotFoundException()
        {
            $this->client->applications()->subscribe('THIS_APPLICATION_NAME_WILL_NOT_EXIST', "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
        }

        /**
         * @test
         * @expectedException \phparia\Exception\UnprocessableEntityException
         */
        public function canSubscribeThrowUnprocessableEntityException()
        {
            $this->client->applications()->subscribe($this->client->getStasisApplicationName(), "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
        }

        /**
         * @test
         * @depends canSubscribe
         * @param \phparia\Resources\Bridge $bridge
         */
        public function canUnsubscribe(Bridge $bridge)
        {
            $application = $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), "bridge:{$bridge->getId()}");
            $this->assertEquals($this->client->getStasisApplicationName(), $application->getName());
        }

        /**
         * @test
         * @expectedException \phparia\Exception\InvalidParameterException
         */
        public function canUnsubscribeThrowInvalidParameterException()
        {
            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), 'bad:format');
        }

        /**
         * @test
         * @expectedException \phparia\Exception\NotFoundException
         */
        public function canUnsubscribeThrowNotFoundException()
        {
            $this->client->applications()->unsubscribe('THIS_APPLICATION_NAME_WILL_NOT_EXIST', "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
        }

        /**
         * @todo Enable this again once it actually works from asterisk
         * @depends canSubscribe
         * @param \phparia\Resources\Bridge $bridge
         * @expectedException \phparia\Exception\ConflictException
         */
        public function canUnsubscribeThrowConflictException(Bridge $bridge)
        {
            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), "bridge:{$bridge->getId()}");
            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), "bridge:{$bridge->getId()}");
        }

        /**
         * @test
         * @expectedException \phparia\Exception\UnprocessableEntityException
         */
        public function canUnsubscribeThrowUnprocessableEntityException()
        {
            $this->client->applications()->unsubscribe($this->client->getStasisApplicationName(), "bridge:THIS_BRIDGE_NAME_WILL_NOT_EXIST");
        }
    }
}