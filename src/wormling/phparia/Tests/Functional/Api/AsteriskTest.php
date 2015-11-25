<?php

namespace {

    use phparia\Api\Asterisk;
    use phparia\Tests\Functional\PhpariaTestCase;

    class AsteriskTest extends PhpariaTestCase
    {
        /**
         * @test
         */
        public function canGetInfo()
        {
            $asteriskInfo = $this->client->asterisk()->getInfo();
            $this->assertInstanceOf('phparia\Resources\AsteriskInfo', $asteriskInfo);
        }

        /**
         * @test
         */
        public function canGetFilteredInfo()
        {
            $asteriskInfo = $this->client->asterisk()->getInfo(Asterisk::INFO_BUILD);
            $this->assertInstanceOf('phparia\Resources\AsteriskInfo', $asteriskInfo);
        }

        /**
         * @test
         */
        public function canSetVariable()
        {
            $this->client->asterisk()->setVariable('NAME', 'VALUE');

            return 'NAME';
        }

        /**
         * @test
         * @expectedException \phparia\Exception\InvalidParameterException
         */
        public function setVariableCanThrowInvalidParameterException()
        {
            $this->client->asterisk()->setVariable(null);
        }

        /**
         * @test
         * @depends canSetVariable
         * @param string $name
         */
        public function canGetVariable($name)
        {
            $variable = $this->client->asterisk()->getVariable($name);
            $this->assertEquals('VALUE', $variable->getValue());
        }

        /**
         * @test
         * @expectedException \phparia\Exception\InvalidParameterException
         */
        public function getVariableCanThrowInvalidParameterException()
        {
            $this->client->asterisk()->getVariable(null);
        }
    }
}