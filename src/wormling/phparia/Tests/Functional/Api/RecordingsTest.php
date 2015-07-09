<?php

namespace {

    use phparia\Events\StasisStart;
    use phparia\Tests\Functional\PhpariaTestCase;

    class RecordingsTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canCreateRecordings()
        {
            $success = false;
            $this->client->onStasisStart(function (StasisStart $event) use (&$success) {
                $event->getChannel()->answer();
                $this->client->bridges()->createBridge('BRIDGE_1_ID', null, 'BRIDGE_1_NAME');
                $this->client->bridges()->addChannel('BRIDGE_1_ID', $event->getChannel()->getId());
                $this->client->bridges()->startMusicOnHold('BRIDGE_1_ID', 'default');
                $this->client->bridges()->record('BRIDGE_1_ID', 'recording_1', 'wav', null, null, 'overwrite');
                $this->client->bridges()->record('BRIDGE_1_ID', 'recording_2', 'wav', null, null, 'overwrite');
                sleep(2);
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

            $recordings = $this->client->recordings()->getRecordings();
            $this->assertTrue(count($recordings) > 0);
            foreach ($recordings as $recording) {
                $this->assertInstanceOf('phparia\Resources\StoredRecording', $recording);
            }
        }

        /**
         * @test
         */
        public function canGetRecordings()
        {
            $recordings = $this->client->recordings()->getRecordings();
            $this->assertTrue(count($recordings) > 1);
        }

        /**
         * @test
         */
        public function canGetRecording()
        {
            $recording = $this->client->recordings()->getRecording('recording_1');
            $this->assertTrue(strtolower($recording->getName()) === 'recording_1');
        }

        /**
         * @test
         * @@expectedException \phparia\Exception\NotFoundException
         */
        public function canDeleteRecording()
        {
            $this->client->recordings()->deleteRecording('recording_1');
            $this->client->recordings()->getRecording('recording_1');
        }

        /**
         * @test
         */
        public function canCopyRecording()
        {
            $this->client->recordings()->copyRecording('recording_2', 'recording_3');
            $recording = $this->client->recordings()->getRecording('recording_3');
            $this->assertTrue(strtolower($recording->getName()) === 'recording_3');
            $this->client->recordings()->deleteRecording('recording_3');
        }
    }
}