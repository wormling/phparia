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

namespace phparia\Node;

use phparia\Resources\Playback;

/**
 * SoundChain - optionally interruptible sound chain that acts as one sound.  It should also optionally accept 
 * the interrupt digit as input.   This is very useful for things like sayDatetime().  Also supports
 * Promise/A.
 * 
 * Sounds are all sent to the asterisk server one after another, then all sounds playing or not yet playing 
 * are stopped then stop() is called.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class SoundChain
{
    /**
     * @var \phparia\Client\Client
     */
    protected $client;

    /**
     * @var \phparia\Resources\Channel
     */
    protected $channel;

    /**
     * @var \phparia\Resources\Bridge
     */
    protected $bridge;

    /**
     * @var array
     */
    protected $sounds = [];

    /**
     * @var Playback[]
     */
    protected $playbacks = [];

    /**
     * When pre prompt or prompt messages can be interrupted, these are the
     * valid interrupt digits.
     * @var string
     */
    protected $validInterruptDigits = Node::DTMF_ANY;

    /**
     * True sounds can be interrupted with a dtmf digit.
     * @var boolean
     */
    private $interruptable = true;

    /**
     * True if interrupt digit is counted as input and not discarded
     * @var boolean
     */
    private $interruptAsInput = true;

    /**
     * @param \phparia\Client\Client $client
     * @param \phparia\Resources\Channel $channel Channel to listen for dtmf (If necessary)
     * @param \phparia\Resources\Bridge $bridge Bridge to play sound(s) on
     */
    public function __construct(\phparia\Client\Client $client, \phparia\Resources\Channel $channel, \phparia\Resources\Bridge $bridge)
    {
        $this->client = $client;
        $this->channel = $channel;
        $this->bridge = $bridge;
    }

    /**
     * 
     * @param string $uri ARI sound URI such as digits:123
     * @return \phparia\Node\SoundChain
     */
    public function add($uri)
    {
        $this->sounds[] = $uri;

        return $this;
    }

    /**
     * @return \React\Promise\Promise
     * @throws Exception\NodeException
     */
    public function play()
    {
        $deferred = new \React\Promise\Deferred();

        if (count($this->sounds) === 0) {
            $deferred->resolve();

            return $deferred->promise();
        }

        foreach ($this->sounds as $sound) {
            $playback = $this->client->bridges()->playMedia($this->bridge->getId(), $sound);
            $this->playbacks[$playback->getId()] = $playback;
        }

        // Listen for last playback to finish
        $playback = end($this->playbacks);
        reset($this->playbacks);

        $playback->oncePlaybackFinished(function ($event) use ($deferred) {
            $this->playbacks = [];
            $deferred->resolve();
        });

        if ($this->interruptable === true) {
            $this->channel->onceChannelDtmfReceived(function ($event) use ($deferred) {
                // @todo Listen again if it wasn't a valid interrupt digit
                if (in_array($event->getDigit(), str_split($this->validInterruptDigits))) {
                    $this->stop();
                    if ($this->interruptAsInput === true) {
                        $deferred->resolve($event->getDigit());
                    } else {
                        $deferred->resolve();
                    }
                }
            });
        }

        return $deferred->promise();
    }

    /**
     * @return \React\Promise\Promise
     */
    public function stop()
    {
        $deferred = new \React\Promise\Deferred();

        foreach ($this->playbacks as $playback) {
            try {
                $this->client->playbacks()->stopPlayback($playback->getId());
                $this->log("Playback stopped: {$playback->getId()}");
            } catch (\Exception $ignore) {
                $this->log("Playback couldn't stop: {$playback->getId()}");
            }
        }

        $this->playbacks = [];
        // @todo Resolve on event instead of assumption?
        $deferred->resolve();

        return $deferred->promise();
    }

    /**
     * @return string
     */
    public function getValidInterruptDigits()
    {
        return $this->validInterruptDigits;
    }

    /**
     * @param string $validInterruptDigits
     * @return \phparia\Node\SoundChain
     */
    public function setValidInterruptDigits($validInterruptDigits)
    {
        $this->validInterruptDigits = $validInterruptDigits;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getInterruptable()
    {
        return $this->interruptable;
    }

    /**
     * @param boolean $interruptable
     * @return \phparia\Node\SoundChain
     */
    public function setInterruptable($interruptable)
    {
        $this->interruptable = $interruptable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getInterruptAsInput()
    {
        return $this->interruptAsInput;
    }

    /**
     * @param boolean $interruptAsInput
     * @return \phparia\Node\SoundChain
     */
    public function setInterruptAsInput($interruptAsInput)
    {
        $this->interruptAsInput = $interruptAsInput;
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
        $logger->notice("SoundChain: $msg");
    }

}
