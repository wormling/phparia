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

namespace phparia\Examples\Node;

class CancelDigitsPlayback extends \phparia\Examples\Example
{

    public function __construct()
    {
        parent::__construct();

        $this->run();
    }

    public function run()
    {
        $this->client->getStasisClient()->on(\phparia\Events\Event::STASIS_START, function($event) use ($client, &$channel) {
            if (count($event->getArgs()) > 0 && $event->getArgs()[0] === 'dialed') {
                return; // Not an incoming call
            }

            $logger = $client->getLogger();

            // Toss the call in a bridge
            $bridge = $client->bridges()->createBridge('occ_bridge_' . uniqid(), 'mixing, dtmf_events, proxy_media', 'bridgy');
            $client->bridges()->addChannel($bridge->getId(), $event->getChannel()->getId(), null);

            $nodeController = new \phparia\Node\NodeController($client, $event->getChannel(), $bridge);

            $nodeController->register('mainMenu', $event->getChannel())
                    ->sayNumber(10)
                    ->sayDigits('1234567890')
                    ->maxAttemptsForInput(1)
                    ->expectExactly(1)
            ;

            $nodeController->registerResult('mainMenu')
                    ->onMaxAttemptsReached()
                    ->execute(function (\phparia\Node\Node $node) use ($logger) {
                        $logger->err("Max attempts reached");
                    })
                    ->hangup(0)
            ;

            $nodeController->registerResult('mainMenu')
                    ->onComplete()
                    ->execute(function (\phparia\Node\Node $node) use ($logger) {
                        $logger->err("Complete");
                    })
                    ->hangup(0)
            ;

            $nodeController->registerResult('mainMenu')
                    ->onCancel()
                    ->execute(function (\phparia\Node\Node $node) use ($logger) {
                        $logger->err("Cancel");
                    })
                    ->hangup(0)
            ;

            $nodeController->jumpTo('mainMenu');
        });

        $client->run();
    }

}
