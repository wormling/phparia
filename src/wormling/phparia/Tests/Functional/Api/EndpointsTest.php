<?php

namespace {

    use phparia\Tests\Functional\PhpariaTestCase;

    class EndpointsTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetEndpoints()
        {
            $endpoints = $this->client->endPoints()->getEndpoints();
            $this->assertGreaterThanOrEqual(1, count($endpoints));
            foreach ($endpoints as $endpoint) {
                $this->assertInstanceOf('phparia\Resources\Endpoint', $endpoint);
            }
        }
    }
}