<?php

namespace {

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
    }
}