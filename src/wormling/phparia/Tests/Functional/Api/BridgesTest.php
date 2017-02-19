<?php

namespace {

    use phparia\Events\StasisStart;
    use phparia\Exception\NotFoundException;
    use phparia\Resources\Bridge;
    use phparia\Tests\Functional\PhpariaTestCase;

    /**
     * Class BridgesTest
     *
     * @todo Refactor channel/bridge creation
     */
    class BridgesTest extends PhpariaTestCase
    {
        /**
         * @test
         * @dataProvider bridgesDataProvider
         * @param $bridgeId
         * @param $bridgeName
         * @return Bridge
         */
        public function canCreateBridge($bridgeId, $bridgeName)
        {
            $bridge = $this->client->bridges()->createBridge($bridgeId, null, $bridgeName);
            $this->assertInstanceOf('phparia\Resources\Bridge', $bridge);

            return $bridge;
        }

        public function bridgesDataProvider()
        {
            return [
                ['BRIDGE_1_ID', 'BRIDGE_1_NAME'],
                ['BRIDGE_2_ID', 'BRIDGE_2_NAME'],
                ['BRIDGE_3_ID', 'BRIDGE_3_NAME'],
            ];
        }

        /**
         * @test
         */
        public function canGetBridges()
        {
            $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
            $this->client->bridges()->createBridge('BRIDGE_2_ID', null, 'BRIDGE_2_NAME');
            $this->client->bridges()->createBridge('BRIDGE_3_ID', null, 'BRIDGE_3_NAME');
            $bridges = $this->client->bridges()->getBridges();
            $this->assertGreaterThanOrEqual(3, count($bridges));
            foreach ($bridges as $bridge) {
                $this->assertInstanceOf('phparia\Resources\Bridge', $bridge);
            }
            $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
            $this->client->bridges()->deleteBridge('BRIDGE_2_ID');
            $this->client->bridges()->deleteBridge('BRIDGE_3_ID');
        }

        /**
         * @todo Enable this once bridge type/name can actually be updated
         */
        public function canUpdateBridge()
        {
            $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
            $updatedBridge = $this->client->bridges()->updateBridge('BRIDGE_1_ID', 'holding', 'BRIDGE_1_NEW_NAME');
            $bridge = $this->client->bridges()->getBridge('BRIDGE_1_ID');
            $this->assertEquals($updatedBridge, $bridge);
        }

        /**
         * @test
         */
        public function canGetBridge()
        {
            $createdBridge = $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
            $bridge = $this->client->bridges()->getBridge('BRIDGE_1_ID');
            $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
            $this->assertEquals($createdBridge, $bridge);
        }

        /**
         * @test
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canGetBridgeThrowNotFoundException()
        {
            $this->client->bridges()->getBridge('THIS_BRIDGE_ID_WILL_NOT_EXIST');
        }

        /**
         * @test
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canDeleteBridge()
        {
            $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
            $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
            $this->client->bridges()->getBridge('BRIDGE_1_ID');
        }

        /**
         * @test
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canDeleteBridgeThrowNotFoundException()
        {
            $this->client->bridges()->deleteBridge('THIS_BRIDGE_ID_WILL_NOT_EXIST');
        }

        /**
         * @test
         */
        public function canAddChannel()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $channelIds = $this->client->bridges()->getBridge('BRIDGE_1_ID')->getChannelIds();
                $found = false;
                foreach ($channelIds as $channel) {
                    if ($channel === $event->getChannel()->getId()) {
                        $found = true;
                    }
                }
                $this->assertTrue($found);
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
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
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canAddChannelThrowNotFoundExceptionFromMissingBridge()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->addChannel('THIS_BRIDGE_ID_WILL_NOT_EXIST', $event->getChannel()->getId());
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
         * @expectedException phparia\Exception\InvalidParameterException
         */
        public function canAddChannelThrowNotFoundExceptionFromMissingChannel()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                try {
                    $this->client->bridges()->addChannel('BRIDGE_1_ID', 'THIS_CHANNEL_WILL_NOT_EXIST');
                } catch (NotFoundException $e) {
                    $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
                    throw $e;
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
        public function canRemoveChannel()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->removeChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $channelIds = $this->client->bridges()->getBridge('BRIDGE_1_ID')->getChannelIds();
                $found = false;
                foreach ($channelIds as $channel) {
                    if ($channel === $event->getChannel()->getId()) {
                        $found = true;
                    }
                }
                $this->assertFalse($found);
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
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
         * @expectedException phparia\Exception\NotFoundException
         */
        public function canRemoveChannelThrowNotFoundExceptionFromMissingBridge()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->removeChannel('THIS_BRIDGE_ID_WILL_NOT_EXIST', $event->getChannel()->getId());
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
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
         * @expectedException phparia\Exception\InvalidParameterException
         */
        public function canRemoveChannelThrowNotFoundExceptionFromMissingChannel()
        {
            $this->client->onStasisStart(function (StasisStart $event) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->removeChannel('BRIDGE_1_ID', 'THIS_CHANNEL_WILL_NOT_EXIST');
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
                $this->client->stop();
            });
            $this->client->getAriClient()->onConnect(function () {
                $this->client->channels()->createChannel($this->dialString, null, null, null, null,
                    $this->client->getStasisApplicationName());
            });
            $this->client->run();
        }

        /**
         * @todo
         * @todo Doesn't technically prove that music on hold is playing
         */
        public function canStartMusicOnHold()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->startMusicOnHold('BRIDGE_1_ID', 'default');
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
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
         * @todo
         * @todo Doesn't technically prove that music on hold has stopped playing
         */
        public function canStopMusicOnHold()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->startMusicOnHold('BRIDGE_1_ID', 'default');
                $this->client->bridges()->stopMusicOnHold('BRIDGE_1_ID');
                $this->client->bridges()->deleteBridge('BRIDGE_1_ID');
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
    }
}