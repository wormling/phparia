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

use Symfony\Component\Yaml\Yaml;

// Make sure composer dependencies have been installed
require __DIR__ . '/../../../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('xdebug.var_display_max_depth', 4);

/**
 * @author Brian Smith <wormling@gmail.com>
 */
class DialExample
{
    /**
     * Example of dialing.
     *
     * @todo Update to 2.X
     * @var \phparia\Client\Client 
     */
    public $client;

    public function __construct()
    {
        $configFile = __DIR__ . '/config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $userName = $value['client']['userName'];
        $password = $value['client']['password'];
        $applicationName = $value['client']['applicationName'];
        $host = $value['client']['host'];
        $port = $value['client']['port'];
        $id = uniqid();
        $bridgeId = uniqid();

        // Connect to the ARI server
        $client = new \phparia\Client\Client($userName, $password, $applicationName, $host, $port);
        $this->client = $client;
        $this->client->channels()->hangup($id);
        // Hangup this channel if the caller hangs up
        $this->client->getStasisClient()->once(\phparia\Events\Event::STASIS_END, function($event) use ($id) {
            $this->client->channels()->hangup($id);
        });

        // Listen for the stasis start
        $client->getStasisClient()->on(\phparia\Events\Event::STASIS_START, function($event) use ($bridgeId, $id) {
            if (count($event->getArgs()) > 0 && $event->getArgs()[0] === 'dialed') {
                $this->log('Detected outgoing call');
                $this->client->bridges()->addChannel($bridgeId, $id);

                return; // Not an incoming call
            }

            // Put the new channel in a bridge
            $channel = $event->getChannel();
            $bridge = $this->client->bridges()->createBridge($bridgeId, 'dtmf_events, mixing', 'bridgename');
            $this->client->bridges()->addChannel($bridge->getId(), $channel->getId());
            try {
                $dialedChannel = $this->client->channels()->createChannel('SIP/8184560270@vitelity-out', null, null, null, $this->client->getStasisApplication(), 'dialed', '8005551212', null, $id);
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

$inputExample = new DialExample();
