<?php

/*
 * Copyright 2017 Brian Smith <wormling@gmail.com>.
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

use Clue\React\Ami\Protocol\Event;
use Devristo\Phpws\Messaging\WebSocketMessage;
use phparia\Client\Phparia;
use phparia\Events\Message;
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
class EventsExample
{
    /**
     * Example of creating a stasis app which also supports AMI events.
     *
     * @var Phparia
     */
    public $client;

    public function __construct()
    {
        $configFile = __DIR__.'/../config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $ariAddress = $value['examples']['client']['ari_address'];
        $amiAddress = $value['examples']['client']['ami_address'];

        $logger = new Log\Logger();
        $logWriter = new Log\Writer\Stream("php://output");
        $logger->addWriter($logWriter);
        //$filter = new Log\Filter\SuppressFilter(true);
        $filter = new Log\Filter\Priority(Log\Logger::NOTICE);
        $logWriter->addFilter($filter);

        // Connect to the ARI server
        $client = new Phparia($logger);
        $client->connect($ariAddress, $amiAddress);
        $this->client = $client;

        // ARI Events
        $client->getAriClient()->getWsClient()->on("message", function(WebSocketMessage $rawMessage) {
            $message = new Message($rawMessage->getData());
            $this->log($message->getType());
        });

        // AMI Eveents
        $client->getAmiClient()->getClient()->on('event', function (Event $event) {
            $this->log($event->getName());
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

new EventsExample();
