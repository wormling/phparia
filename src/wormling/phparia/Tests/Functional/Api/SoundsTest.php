<?php

namespace {

    use phparia\Tests\Functional\PhpariaTestCase;

    /**
     * @todo Asterisk doesn't seem to return an error for missing sounds
     */
    class SoundsTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetSounds()
        {
            $sounds = $this->client->sounds()->getSounds();
            $this->assertTrue(count($sounds) > 0);
            foreach ($sounds as $sound) {
                $this->assertInstanceOf('phparia\Resources\Sound', $sound);
            }
        }

        /**
         * @test
         */
        public function canGetSound()
        {
            $sound = $this->client->sounds()->getSound('beep');
            $this->assertTrue(strtolower($sound->getId()) === 'beep');
        }
    }
}