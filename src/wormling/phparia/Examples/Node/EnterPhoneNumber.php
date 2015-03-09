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

use phparia\Events\Event;
use phparia\Examples\Example;
use phparia\Node\Node;
use phparia\Node\NodeController;

// Make sure composer dependencies have been installed
require __DIR__ . '/../../../../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('xdebug.var_display_max_depth', 4);

class EnterPhoneNumber extends Example
{

    public function __construct()
    {
        parent::__construct();

        $this->run();
        $this->client->run();
    }

    public function run()
    {
        $this->client->getStasisClient()->on(\phparia\Events\Event::STASIS_START, function($event) {
            if (count($event->getArgs()) > 0 && $event->getArgs()[0] === 'dialed') {
                return; // Not an incoming call
            }

            // Toss the call in a bridge
            $bridge = $this->client->bridges()->createBridge('occ_bridge_' . uniqid(), 'mixing, dtmf_events, proxy_media', 'bridgy');
            $this->client->bridges()->addChannel($bridge->getId(), $event->getChannel()->getId(), null);

            $this->nodeController = new NodeController($this->client, $event->getChannel(), $bridge);


            $this->nodeController->jumpTo($this->getMainMenuName());
        });
    }

    /**
     * Get the module menu name
     *
     * @return string Node name
     */
    public function getMainMenuName()
    {
        $menuName = 'mainMenu_' . uniqid();

        $this->buildGenericOptionSelectNode($menuName, array(Node::DTMF_1, Node::DTMF_2, Node::DTMF_3, Node::DTMF_4))
                ->maxAttemptsForInput(3)
                ->saySound('beep')
        ;

        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->withInput(Node::DTMF_1)
                ->jumpAfterEval(function(Node $node) {
                    return $this->getPhoneMenuName();
                })
        ;

        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->withInput(Node::DTMF_2)
                ->jumpAfterEval(function(Node $node) {
                    return $this->getPhoneMenuName();
                })
        ;

        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->withInput(Node::DTMF_3)
                ->jumpAfterEval(function(Node $node) {
                    return $this->getPhoneMenuName();
                })
        ;

        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->withInput(Node::DTMF_4)
                ->jumpAfterEval(function(Node $node) {
                    return $this->getPhoneMenuName();
                })
        ;

        return $menuName;
    }

    public function getPhoneMenuName()
    {
        $menuName = 'phoneMenu_' . uniqid();

        // Dial menu (Always defined since it can be called from the email caller id menu
        $this->buildGenericInputNode($menuName)
                ->saySound('vm-enter-num-to-call')
                ->expectAtLeast(1)
                ->expectAtMost(12)
//                    ->loadValidatorsFrom($this->getValidatorPhoneNumber())
        ;

        // Set the phone number then jump to the confirm menu
        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->jumpAfterEval(function (Node $node) {
                    $phoneNumber = \preg_replace('/[^0-9]/', '', $node->getInput());
                    echo "Phone number: $phoneNumber\n";

                    return $this->getMainMenuName();
                })
        ;

        return $menuName;
    }

}

$enterPhoneNumber = new EnterPhoneNumber();
