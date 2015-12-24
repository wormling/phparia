<?php

namespace {

    use phparia\Api\DeviceStates;
    use phparia\Events\DeviceStateChange;
    use phparia\Events\StasisStart;
    use phparia\Exception\NotFoundException;
    use phparia\Tests\Functional\PhpariaTestCase;

    class DeviceStatesTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canUpdateDeviceState()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $this->client->deviceStates()->updateDeviceState('Stasis:TEST_DEVICE', DeviceStates::DEVICE_STATE_INUSE);
                $this->client->deviceStates()->getDeviceState('Stasis:TEST_DEVICE');
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
         * @expectedException phparia\Exception\ConflictException
         */
        public function canUpdateDeviceStateThrowNotFoundException()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $this->client->deviceStates()->updateDeviceState('Bad:TEST_DEVICE', DeviceStates::DEVICE_STATE_INUSE);
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
        public function canGetDeviceStates()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $deviceStates = $this->client->deviceStates()->getDeviceStates();
                $this->assertTrue(count($deviceStates) > 0);
                foreach ($deviceStates as $deviceState) {
                    $this->assertInstanceOf('phparia\Resources\DeviceState', $deviceState);
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
        public function canGetDeviceState()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $deviceState = $this->client->deviceStates()->getDeviceState('Stasis:TEST_DEVICE');
                $this->assertInstanceOf('phparia\Resources\DeviceState', $deviceState);
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
        public function canDeleteDeviceState()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $this->client->deviceStates()->deleteDeviceState('Stasis:TEST_DEVICE');
                $deviceState = $this->client->deviceStates()->getDeviceState('Stasis:TEST_DEVICE');
                $this->assertNotEquals(DeviceStates::DEVICE_STATE_INUSE, $deviceState->getState(), 'Device state delete failed');
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
         * @expectedException phparia\Exception\ConflictException
         */
        public function canDeleteDeviceStateThrowNotFoundException()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();

                $this->client->deviceStates()->deleteDeviceState('Bad:TEST_DEVICE_DOES_NOT_EXIST');
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