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
use phparia\Events\ChannelDtmfReceived;
use phparia\Events\StasisStart;
use Symfony\Component\Yaml\Yaml;
use Zend\Log;

// Make sure composer dependencies have been installed
require __DIR__.'/../../../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('xdebug.var_display_max_depth', 4);

/**
 * @author Brian Smith <wormling@gmail.com>
 */
class HangupExample
{
    /**
     * Example of listening for DTMF input from a caller and hanging up when '#' is pressed.
     *
     * @var Phparia
     */
    public $client;

    public function __construct()
    {
        $configFile = __DIR__.'/../config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $ariAddress = $value['examples']['client']['ari_address'];

        $logger = new Log\Logger();
        $logWriter = new Log\Writer\Stream("php://output");
        $logger->addWriter($logWriter);
        //$filter = new \Zend\Log\Filter\SuppressFilter(true);
        $filter = new Log\Filter\Priority(Log\Logger::NOTICE);
        $logWriter->addFilter($filter);

        // Connect to the ARI server
        $client = new Phparia($logger);
        $client->connect($ariAddress);
        $this->client = $client;

        // Listen for the stasis start
        $client->onStasisStart(function (StasisStart $event) {
            // Put the new channel in a bridge
            $channel = $event->getChannel();
            $bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing', 'bridgename');
            $this->client->bridges()->addChannel($bridge->getId(), $channel->getId());

            // Listen for DTMF and hangup when '#' is pressed
            $channel->onChannelDtmfReceived(function (ChannelDtmfReceived $event) use ($channel) {
                $this->log("Got digit: {$event->getDigit()}");
                if ($event->getDigit() === '#') {
                    $channel->hangup();
                }
            });
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

new HangupExample();
