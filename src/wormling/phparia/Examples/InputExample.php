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

use Symfony\Component\Yaml\Parser;

/**
 * @author Brian Smith <wormling@gmail.com>
 */
class InputExample
{
    /**
     * Example of listening for DTMF input from a caller.
     * 
     * @var \phparia\Client\Client 
     */
    public $client;

    public function __construct()
    {
        $configFile = __DIR__ . '/config.yml';
        $yaml = new Parser();
        $value = $yaml->parse(file_get_contents($configFile));

        $userName = $value['client']['userName'];
        $password = $value['client']['password'];
        $applicationName = $value['client']['applicationName'];
        $host = $value['client']['host'];
        $port = $value['client']['port'];

        // Connect to the ARI server
        $client = new \phparia\Client\Client($userName, $password, $applicationName, $host, $port);
        $this->client = $client;

        // Listen for the stasis start
        $client->getStasisClient()->on(\phparia\Events\Event::STASIS_START, function($event) {
            // Put the new channel in a bridge
            $channel = $event->getChannel();
            $bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing', 'bridgename');
            $this->client->bridges()->addChannel($bridge->getId(), $channel->getId(), null);
            
            // Listen for DTMF
            $channel->onChannelDtmfReceived(function($event) {
                $this->log("Got digit: {$event->getDigit()}");
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
