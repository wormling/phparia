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

use phparia\Node\Node;
use Symfony\Component\Yaml\Parser;

/**
 * Base class for examples
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Example
{
    /**
     * @var \phparia\Client\Client 
     */
    public $client;
    
    /**
     * @var \phparia\Node\NodeController 
     */
    protected $nodeController;

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

        $client = new \phparia\Client\Client($userName, $password, $applicationName, $host, $port);
        $this->client = $client;
    }

    /**
     * Build a generic node with our default options
     *
     * @param  string $name
     * @return Node
     */
    protected function buildGenericNode($name)
    {
        return $this->nodeController->register($name)
                        ->maxTotalTimeForInput(50000)
                        ->maxTimeBetweenDigits(3000)
                        ->executeBeforeRun(function (Node $node) {
                            echo "Running: {$node->getName()}";
                        })
        ;
    }

    /**
     * Build a generic node with our default options for use with input
     *
     * @param  string            $name
     * @param  string|null|false $endInputWith
     * @return Node
     */
    protected function buildGenericInputNode($name, $endInputWith = Node::DTMF_HASH)
    {
        $node = $this->buildGenericNode($name)
                ->maxAttemptsForInput(3)
        ;

        if ($endInputWith) {
            $node->endInputWith(Node::DTMF_HASH);
        }

        $this->nodeController->registerResult($name)
                ->onMaxAttemptsReached()
                ->jumpTo('maxAttemptsReachedMenu')
        ;

        return $node;
    }

    /**
     * Build a generic node with our default options for use with option selection (ONLY)
     *
     * @param  string      $menuName
     * @param  array       $allowedDigits
     * @param  string|null $cancelMenuName The menu to navigate to if '#' is pressed
     * @return Node
     */
    protected function buildGenericOptionSelectNode($menuName, $allowedDigits, $cancelMenuName = null)
    {
        // Add '*' by default to repeat the menu
        $allowedDigits = array_unique(array_merge($allowedDigits, array(Node::DTMF_STAR)));

        $node = $this->buildGenericInputNode($menuName, false);

        // Add a cancel action if a cancel menu name was provided
        if ($cancelMenuName) {
            // Add '#' to allowed digits
            $allowedDigits = array_unique(array_merge($allowedDigits, array(Node::DTMF_HASH)));

            $node->cancelWith('#');

            $this->nodeController->registerResult($menuName)
                    ->onCancel()
                    ->jumpTo($cancelMenuName)
            ;
        }

        // Allow entering of allowed digits with validation
        $node
                ->expectExactly(1)
                ->validateInputWith($menuName . "Validator", function (Node $node) use ($allowedDigits) {
                    return in_array($node->getInput(), $allowedDigits);
                }, 'sound:silence/1') // Sound on error is required or the menu will just fail and fall through expection an onInvalidInput result to be registered
        ;

        // Repeat the menu when '*' is pressed
        $this->nodeController->registerResult($menuName)
                ->onComplete()
                ->withInput(Node::DTMF_STAR)
                ->jumpTo($menuName)
        ;

        return $node;
    }

}
