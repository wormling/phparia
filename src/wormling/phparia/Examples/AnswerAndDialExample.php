<?php

/*
 * Copyright 2014 Brian Smith <wormling@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace phparia\Examples;

use phparia\Client\Phparia;
use phparia\Events\StasisEnd;
use phparia\Events\StasisStart;
use phparia\Resources\Bridge;
use phparia\Resources\Channel;
use Symfony\Component\Yaml\Yaml;

// Make sure composer dependencies have been installed
require __DIR__.'/../../../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('xdebug.var_display_max_depth', 4);

/**
 * Example of dialing.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class AnswerAndDialExample
{
    /**
     * @var Phparia
     */
    public $client;

    /**
     * @var Bridge
     */
    private $bridge = null;

    /**
     * @var Channel
     */
    private $dialedChannel = null;

    public function __construct()
    {
        $configFile = __DIR__.'/config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $ariAddress = $value['client']['ari_address'];
        $dialString = $value['dial_example']['dial_string'];

        $logger = new \Zend\Log\Logger();
        $logWriter = new \Zend\Log\Writer\Stream("php://output");
        $logger->addWriter($logWriter);
        //$filter = new \Zend\Log\Filter\SuppressFilter(true);
        $filter = new \Zend\Log\Filter\Priority(\Zend\Log\Logger::NOTICE);
        $logWriter->addFilter($filter);

        // Connect to the ARI server
        $this->client = new Phparia($logger);
        $this->client->connect($ariAddress);

        // Hangup this channel if the caller hangs up
        $this->client->onStasisEnd(function (StasisEnd $event) {
            $this->dialedChannel->hangup();
        });

        // Listen for the stasis start
        $this->client->onStasisStart(function (StasisStart $event) use ($dialString) {
            if (count($event->getArgs()) > 0 && $event->getArgs()[0] === 'dialed') {
                $this->log('Detected outgoing call');
                $this->bridge->addChannel($event->getChannel()->getId());

                return; // Not an incoming call
            }

            // Put the new channel in a bridge
            $channel = $event->getChannel();
            $this->bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing',
                'dial_example_bridge');
            $this->bridge->addChannel($channel->getId());
            try {
                $this->dialedChannel = $this->client->channels()->createChannel($dialString, null, null, null,
                    $this->client->getStasisApplicationName(), 'dialed', '8005551212');
            } catch (\phparia\Exception\ServerException $e) {
                $this->log($e->getMessage());
            }
        });

        $this->client->run();
    }

    /**
     * @param string $msg
     */
    protected function log($msg)
    {
        $logger = $this->client->getLogger();
        $logger->notice($msg);
    }

}

$answerAndDialExample = new AnswerAndDialExample();
