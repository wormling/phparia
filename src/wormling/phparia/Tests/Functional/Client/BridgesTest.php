<?php

namespace {

    use phparia\Tests\Functional\PhpariaTestCase;

    class BridgesTest extends PhpariaTestCase
    {
        /**
         * @test
         * @dataProvider bridgesDataProvider
         * @param $bridgeId
         * @param $bridgeName
         */
        public function canCreateBridge($bridgeId, $bridgeName)
        {
            $bridge = $this->client->bridges()->createBridge($bridgeId, null, $bridgeName);
            $this->assertInstanceOf('phparia\Resources\Bridge', $bridge);
        }

        public function bridgesDataProvider()
        {
            return [
                ['id-1000', 'name-1000'],
                ['id-1001', 'name-1001'],
                ['id-1002', 'name-1002'],
                ['id-1003', 'name-1003'],
            ];
        }
    }
}