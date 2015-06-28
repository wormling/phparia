<?php

namespace {
    use phparia\Tests\Functional\PhpariaTestCase;

    class DeviceStatesTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetDeviceStates()
        {
            $deviceStates = $this->client->deviceStates()->getDeviceStates();
            //$this->assertTrue(count($deviceStates) > 0);
            foreach ($deviceStates as $deviceState) {
                $this->assertInstanceOf('phparia\Resources\DeviceState', $deviceState);
            }
        }
    }
}