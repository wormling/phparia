<?php

/**
 * A node controller, used to execute a series of nodes while evaluating
 * their output.
 *
 * PHP Version 5.3
 *
 * @category PAGI
 * @package  Node
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://marcelog.github.com/PAGI/ Apache License 2.0
 * @version  SVN: $Id$
 * @link     http://marcelog.github.com/PAGI/
 *
 * Copyright 2011 Marcelo Gornstein <marcelog@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace phparia\Node;

use phparia\Client\Client;
use phparia\Node\Exception\NodeException;

/**
 * A node controller, used to execute a series of nodes while evaluating
 * their output.
 *
 * @category PAGI
 * @package  Node
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://marcelog.github.com/PAGI/ Apache License 2.0
 * @link     http://marcelog.github.com/PAGI/
 */
class NodeController
{
    /**
     * All registered nodes.
     * @var \phparia\Node\Node[]
     */
    protected $nodes = array();

    /**
     * All registered node results.
     * @var \phparia\Node\NodeActionCommand[]
     */
    protected $nodeResults = array();

    /**
     * Holds the phparia client.
     * @var \phparia\Client\Client
     */
    private $client = null;

    /**
     * Holds the phparia channel
     * @var \phparia\Resources\Channel
     */
    private $channel = null;

    /**
     * Holds the phparia bridge
     * @var \phparia\Resources\Bridge 
     */
    private $bridge = null;

    /**
     * Asterisk logger instance to use.
     * @var \phparia\Logger\Asterisk\IAsteriskLogger
     */
    protected $logger;

    /**
     * Node name.
     * @var string
     */
    private $name = 'X';

    public function __construct(\phparia\Client\Client $client, \phparia\Resources\Channel $channel, \phparia\Resources\Bridge $bridge)
    {
        $this->client = $client;
        $this->channel = $channel;
        $this->bridge = $bridge;

        // Listen for a node finished processing event once per jumpTo(...) call
        $this->client->getStasisClient()->on(Node::EVENT_FINISHED, function($node) {
            $name = $this->processNodeResult($node);
            $this->log("Got EVENT_FINISHED, jumping to $name");
            if ($name !== false) {
                $this->jumpTo($name);
            }
            $this->log("Got EVENT_FINISHED, but node controller has no more nodes to jump to");
        });
    }

    /**
     * Runs a node and process the result.
     *
     * @param string $name Node to run.
     *
     * @return void
     * @throws NodeException
     */
    public function jumpTo($name)
    {
        if (!isset($this->nodes[$name])) {
            throw new NodeException("Unknown node: $name");
        }

        $node = $this->nodes[$name];
        $this->log("Running $name");
        $node->run();
    }

    /**
     * Process the result of the given node. Returns false if no other nodes
     * should be run, or a string with the next node name.
     *
     * @param Node $node Node that was run.
     *
     * @return string|false
     */
    protected function processNodeResult(Node $node)
    {
        $nextNodeName = false;
        $name = $node->getName();

        if (isset($this->nodeResults[$name])) {
            foreach ($this->nodeResults[$name] as $resultInfo) {
                /* @var $resultInfo NodeActionCommand */
                if ($resultInfo->appliesTo($node)) {
                    if ($resultInfo->isActionHangup()) {
                        $this->log("Hanging up after $name");
                        $data = $resultInfo->getActionData();
                        $all = $data['all'];
                        if ($all) {
                            if ($node->getDialedChannel() instanceof \phparia\Entity\Channel) {
                                $this->client->channels()->deleteChannel($node->getDialedChannel()->getId());
                            }
                        }
                        $this->client->channels()->deleteChannel($node->getChannel()->getId());
                    } else if ($resultInfo->isActionJumpTo()) {
                        $data = $resultInfo->getActionData();
                        if (isset($data['nodeEval'])) {
                            $callback = $data['nodeEval'];
                            $nodeName = $callback($node);
                        } else {
                            $nodeName = $data['nodeName'];
                        }
                        $this->log("Jumping from $name to $nodeName");
                        $nextNodeName = $nodeName;
                        break;
                    } else if ($resultInfo->isActionExecute()) {
                        $this->log("Executing callback after $name");
                        $data = $resultInfo->getActionData();
                        $callback = $data['callback'];
                        $callback($node);
                    }
                }
            }
        }

        return $nextNodeName;
    }

    /**
     * Registers a new node result to be taken into account when the given node
     * is ran.
     *
     * @param string $name
     *
     * @return NodeActionCommand
     */
    public function registerResult($name)
    {
        $nodeActionCommand = new NodeActionCommand();
        if (!isset($this->nodeResults[$name])) {
            $this->nodeResults[$name] = array();
        }
        $this->nodeResults[$name][] = $nodeActionCommand;

        return $nodeActionCommand->whenNode($name);
    }

    /**
     * Registers a new node in the application. Returns the created node.
     *
     * @param string $name The node to be registered
     * @param Client $client The ARI client
     * @param \phparia\Resources\Channel $channel The channel to use for the voicemenu
     *
     * @return \phparia\Node\Node
     */
    public function register($name)
    {
        $node = new Node($name, $this->client, $this->channel, $this->bridge);
        $this->nodes[$name] = $node;

        return $node;
    }

    /**
     * Gives a name for this node.
     *
     * @param string $name
     *
     * @return Node
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    protected function log($msg)
    {
        $logger = $this->client->getLogger();
        $logger->err("NodeController: {$this->name}: $msg");
    }

}
