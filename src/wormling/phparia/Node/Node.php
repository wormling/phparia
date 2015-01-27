<?php

/**
 * A node, used to get input from the user, validate it, play prompt messages,
 * etc.
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

use phparia\Node\Exception\NodeException;

/**
 * A node, used to get input from the user, validate it, play prompt messages,
 * etc.
 *
 * @category phpari
 * @package  Node
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://marcelog.github.com/PAGI/ Apache License 2.0
 * @link     http://marcelog.github.com/PAGI/
 */
class Node
{
    /**
     * Any of the available DTMF digits in a classic telephone.
     * @var string
     */
    const DTMF_ANY = '0123456789*#';

    /**
     * DTMF digit '*'
     * @var string
     */
    const DTMF_STAR = '*';

    /**
     * DTMF digit '#'
     * @var string
     */
    const DTMF_HASH = '#';

    /**
     * DTMF digits which are integers (numbers)
     * @var string
     */
    const DTMF_NUMERIC = '1234567890';

    /**
     * DTMF digits non numeric
     * @var string
     */
    const DTMF_NONNUMERIC = '#*';

    /**
     * DTMF digit '1'
     * @var string
     */
    const DTMF_1 = '1';

    /**
     * DTMF digit '2'
     * @var string
     */
    const DTMF_2 = '2';

    /**
     * DTMF digit '3'
     * @var string
     */
    const DTMF_3 = '3';

    /**
     * DTMF digit '4'
     * @var string
     */
    const DTMF_4 = '4';

    /**
     * DTMF digit '5'
     * @var string
     */
    const DTMF_5 = '5';

    /**
     * DTMF digit '6'
     * @var string
     */
    const DTMF_6 = '6';

    /**
     * DTMF digit '7'
     * @var string
     */
    const DTMF_7 = '7';

    /**
     * DTMF digit '8'
     * @var string
     */
    const DTMF_8 = '8';

    /**
     * DTMF digit '9'
     * @var string
     */
    const DTMF_9 = '9';

    /**
     * DTMF digit '0'
     * @var string
     */
    const DTMF_0 = '0';

    /**
     * No DTMFs.
     * @var string
     */
    const DTMF_NONE = '';

    /**
     * State when the node has not be run yet.
     * @var integer
     */
    const STATE_NOT_RUN = 1;

    /**
     * State reached when the node can be cancelled (it has a cancel digit set)
     * and the user pressed it.
     * @var integer
     */
    const STATE_CANCEL = 2;

    /**
     * State reached when the input is considered complete (it has at least
     * the minimum number of digits)
     * @var integer
     */
    const STATE_COMPLETE = 3;

    /**
     * The user has not entered any input.
     * @var integer
     */
    const STATE_TIMEOUT = 4;

    /**
     * The user exceeded the maximum number of attempts allowed to enter
     * a valid option.
     * @var integer
     */
    const STATE_MAX_INPUTS_REACHED = 5;

    /**
     * Progress through prompts
     */
    const PROGRESS_NONE = 1;
    const PROGRESS_PREPROMPT_MESSAGES_PLAYING = 2;
    const PROGRESS_PROMPT_MESSAGES_PLAYING = 3;
    const PROGRESS_PROMPTS_FINISHED = 4;

    /**
     * Prompt type
     */
    const PLAYBACK_PREPROMPT = 1;
    const PLAYBACK_PROMPT = 2;

    /**
     * Used when evaluating input, returned when the user presses the end of
     * input digit.
     * @var integer
     */
    const INPUT_END = 1;

    /**
     * Used when evaluating input, returned when the user presses the cancel
     * digit.
     * @var integer
     */
    const INPUT_CANCEL = 2;

    /**
     * Used when evaluating input, returned when the user presses a digit
     * that is not a cancel or end of input digit.
     * @var integer
     */
    const INPUT_NORMAL = 3;

    /**
     * Used to specify infinite time for timeouts.
     * @var integer
     */
    const TIME_INFINITE = -1;

    /**
     * @var string Emitted when node progress has changed
     */
    const EVENT_PROGRESS_CHANGED = 'progress_changed';

    /**
     * @var string Emitted when node run is finished for any reason
     */
    const EVENT_FINISHED = 'finished';

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
     * Holds the phparia dialed Fgetchannel
     * @var \phparia\Resources\Channel
     */
    private $dialedChannel = null;

    /**
     * Holds the phparia bridge
     * @var \phparia\Resources\Bridge
     */
    private $bridge = null;

    /**
     * Here's where the user input is appended one digit at the time.
     * @var string
     */
    private $input = self::DTMF_NONE;

    /**
     * Node state.
     * @var integer
     */
    protected $state = self::STATE_NOT_RUN;
    protected $progress = self::PROGRESS_NONE;

    /**
     * Holds the configured end of input digit.
     * @var string
     */
    private $endOfInputDigit = null;

    /**
     * Holds the configured cancel digit.
     * @var string
     */
    private $cancelDigit = null;

    /**
     * The minimum configured expected input length.
     * @var integer
     */
    private $minInput = 0;

    /**
     * The maximum configured expected input length.
     * @var integer
     */
    private $maxInput = 0;

    /**
     * In milliseconds, maximum time to wait for user input between digits.
     * Only taken into account when expecting input outside prompt and preprompt
     * messages.
     * @var integer
     */
    private $timeBetweenDigits = self::TIME_INFINITE;

    /**
     * In milliseconds, maximum time to wait for a complete user input (per
     * attempt).
     * @var integer
     */
    private $totalTimeForInput = self::TIME_INFINITE;

    /**
     * Similar to prompt messages, but dynamically populated and cleared with
     * pre prompt messages, like error messages from validations.
     * @var SoundChain
     */
    private $prePrompts = null;

    /**
     * Holds the prompt messages (actions) to be used before expecting the user
     * input (like sounds, numbers, datetimes, etc).
     * @var SoundChain
     */
    private $prompts = null;

    /**
     * Node name.
     * @var string
     */
    private $name = null;

    /**
     * Holds all input validators.
     * @var \Closure[]
     */
    private $inputValidations = array();

    /**
     * Total attempts for the user to enter a valid input. Will loop input
     * routine this many times when the input is not validated.
     * @var integer
     */
    private $totalAttemptsForInput = 1;

    /**
     * Optional message to play when the user did not enter any digits on
     * input.
     * @var string
     */
    private $onNoInputMessage = null;

    /**
     * Sound to play to bridge when a call is answered
     * @var string 
     */
    private $playOnAnswer = null;

    /**
     * Optinal message to play when the user exceeded the maximum allowed
     * attempts to enter a valid input.
     * @var string
     */
    private $onMaxValidInputAttempts = null;

    /**
     * Carries state. This is where optional custom data can be saved in the
     * callbacks and 3rd party software. Keys are strings.
     * @var mixed[]
     */
    private $registry = array();

    /**
     * Callback to execute on valid input from the user.
     * @var \Closure
     */
    private $executeOnValidInput = null;

    /**
     * Callback to execute when the node failed to correctly
     * Enter description here ...
     * @var \Closure
     */
    private $executeOnInputFailed = null;

    /**
     * When true, the user may retry the input by pressing the cancel button
     * if and only if he/she has already input one or more digits.
     * @var boolean
     */
    private $cancelWithInputRetriesInput = false;

    /**
     * Used to save the total amount of opportunities used to enter valid input.
     * @var integer
     */
    private $inputAttemptsUsed = 0;

    /**
     * Execute before running this node.
     * @var \Closure
     */
    private $executeBeforeRun = null;

    /**
     * Execute after running this node.
     * @var \Closure
     */
    private $executeAfterRun = null;

    /**
     * Execute after a validation has failed.
     * @var \Closure
     */
    private $executeAfterFailedValidation = null;

    /**
     * Play "no input" message in last attempt too.
     * @var boolean
     */
    private $playOnNoInputInLastAttempt = false;

    /**
     * @var callable
     */
    private $dtmfCallback = null;

    /**
     * Used only during doInput
     *
     * @var \React\EventLoop\Timer\TimerInterface
     */
    private $timeBetweenDigitsTimer = null;

    /**
     * Used only during doInput
     *
     * @var \React\EventLoop\Timer\TimerInterface
     */
    private $totalTimeForInputTimer = null;

    /**
     * @var integer Last input time in milliseconds
     */
    private $lastInputTime = null;

    /**
     * Holds the dial information
     *
     * @var array 
     */
    private $dial = [];

    /**
     * Holds the recording information
     *
     * @var array
     */
    private $record = [];

    /**
     * Holds the current live recording
     *
     * @var \phparia\Resources\LiveRecording
     */
    private $liveRecording = null;

    public function __construct($name, \phparia\Client\Client $client, \phparia\Resources\Channel $channel, \phparia\Resources\Bridge $bridge)
    {
        $this->name = $name;
        $this->client = $client;
        $this->channel = $channel;
        $this->bridge = $bridge;
        $this->prePrompts = new SoundChain($client, $channel, $bridge);
        $this->prompts = new SoundChain($client, $channel, $bridge);
    }

    /**
     * Make pre prompt messages not interruptable
     *
     * @return Node
     */
    public function prePromptMessagesNotInterruptable()
    {
        $this->prePrompts->setInterruptable(false);
        $this->prePrompts->setValidInterruptDigits(self::DTMF_NONE);

        return $this;
    }

    /**
     * Digits entered during the pre prompt messages are not considered
     * as node input.
     *
     * @return Node
     */
    public function dontAcceptPrePromptInputAsInput()
    {
        $this->prePrompts->setInterruptAsInput(false);

        return $this;
    }

    /**
     * Make prompt messages not interruptable.
     *
     * @return Node
     */
    public function unInterruptablePrompts()
    {
        $this->prompts->setInterruptable(false);
        $this->prompts->setValidInterruptDigits(self::DTMF_NONE);

        return $this;
    }

    /**
     * Specify an optional message to play when the user did not enter any
     * input at all. By default, will NOT be played if this happens in the last
     * allowed attempt.
     *
     * @param string $filename Sound file to play.
     *
     * @return Node
     */
    public function playOnNoInput($filename)
    {
        $this->onNoInputMessage = $filename;

        return $this;
    }

    /**
     * Forces to play "no input" message on last attempt too.
     *
     * @return Node
     */
    public function playNoInputMessageOnLastAttempt()
    {
        $this->playOnNoInputInLastAttempt = true;

        return $this;
    }

    /**
     * Optional message to play when the user exhausted all the available
     * attempts to enter a valid input.
     *
     * @param string $filename Sound file to play.
     *
     * @return Node
     */
    public function playOnMaxValidInputAttempts($filename)
    {
        $this->onMaxValidInputAttempts = $filename;

        return $this;
    }

    /**
     * Sound to play to bridge when a call is answered
     * 
     * @param string $sound
     * @return \phparia\Node\Node
     */
    public function playOnAnswer($sound)
    {
        $this->playOnAnswer = $sound;

        return $this;
    }

    /**
     * Specify a maximum attempt number for the user to enter a valid input.
     * Defaults to 1.
     *
     * @param integer $number
     *
     * @return Node
     */
    public function maxAttemptsForInput($number)
    {
        $this->totalAttemptsForInput = $number;

        return $this;
    }

    /**
     * Given a callback and an optional sound to play on error, this will
     * return a validator information structure to be used with
     * validateInputWith().
     *
     * @param \Closure $validation Callback to use as validator
     * @param string|null $soundOnError Sound file to play on error
     *
     * @return validatorInfo
     */
    public static function createValidatorInfo(\Closure $validation, $soundOnError = null)
    {
        return array(
            'callback' => $validation,
            'soundOnError' => $soundOnError
        );
    }

    /**
     * Given an array of validator information structures, this will load
     * all validators into this node.
     *
     * @param validatorInfo[] $validatorsInformation
     *
     * @return Node
     */
    public function loadValidatorsFrom(array $validatorsInformation)
    {
        foreach ($validatorsInformation as $name => $validatorInfo) {
            $this->validateInputWith($name, $validatorInfo['callback'], $validatorInfo['soundOnError']);
        }

        return $this;
    }

    /**
     * Add an input validation to this node.
     *
     * @param string $name A distrinctive name for this validator
     * @param \Closure $validation Callback to use for validation
     * @param string|null $soundOnError Optional sound to play on error
     *
     * @return Node
     */
    public function validateInputWith($name, \Closure $validation, $soundOnError = null)
    {
        $this->inputValidations[$name] = self::createValidatorInfo($validation, $soundOnError);

        return $this;
    }

    /**
     * Calls all validators in order. Will stop when any of them returns false.
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->inputValidations as $name => $data) {
            $validator = $data['callback'];
            $result = $validator($this);
            if ($result === false) {
                $this->log("Validation FAILED: $name");
                $onError = $data['soundOnError'];
                if (is_array($onError)) {
                    foreach ($onError as $msg) {
                        $this->client->bridges()->playMedia($this->bridge->getId(), $msg);
                    }
                } else if (is_string($onError)) {
                    $this->client->bridges()->playMedia($this->bridge->getId(), $onError);
                } else {
                    $this->log("Ignoring validation sound: " . print_r($onError, true));
                }
                return false;
            }
            $this->log("Validation OK: $name");
        }

        return true;
    }

    /**
     * Removes prompt messages.
     *
     * @deprecated
     * @return Node
     */
    public function clearPromptMessages()
    {
        $this->prompts = new SoundChain($this->client, $this->channel, $this->bridge);

        return $this;
    }

    /**
     * Adds a sound file to play as a pre prompt message.
     *
     * @param string $soundName relative to the sounds/<language> folder
     *
     * @return Node
     */
    public function addPrePromptMessage($soundName)
    {
        $this->prePrompts->add("sound:$soundName");

        return $this;
    }

    /**
     * Loads a prompt message for saying the digits of the given number.
     *
     * @todo Change this when you can cancel more than one digit at once
     * @param string $digits
     *
     * @return Node
     */
    public function sayDigits($digits)
    {
        $digitsArray = str_split($digits);
        foreach ($digitsArray as $digit) {
            $this->prompts->add("digits:$digit");
        }

        return $this;
    }

    /**
     * Loads a prompt message for saying a number.
     *
     * @param integer $number
     *
     * @return Node
     */
    public function sayNumber($number)
    {
        $this->prompts->add("number:$number");

        return $this;
    }

    /**
     * Loads a prompt message for saying a date/time expressed by a unix
     * timestamp and a format.
     *
     * @param \DateTime $timestamp
     * @param string $format 
     * @link http://www.voip-info.org/wiki/view/Asterisk+cmd+SayUnixTime
     *
     * @return Node
     */
    public function sayDateTime($timestamp, $format)
    {
        $this->log('Implement sayDateTime');
        //$this->prompts->add(new Prompt($this->client, $this->bridge, Prompt::SOUND_TYPE_DATETIME, $timestamp, $format));

        return $this;
    }

    /**
     * Loads a prompt message for playing an audio file.
     *
     * @param string $soundName
     *
     * @return Node
     */
    public function saySound($soundName)
    {
        $this->prompts->add("sound:$soundName");

        return $this;
    }

    /**
     * Configure the node to expect at least this many digits. The input is
     * considered complete when this many digits has been entered. Cancel and
     * end of input digits (if configured) are not taken into account.
     *
     * @param integer $length
     *
     * @return Node
     */
    public function expectAtLeast($length)
    {
        $this->minInput = $length;

        return $this;
    }

    /**
     * Configure the node to expect at most this many digits. The reading loop
     * will try to read this many digits.
     *
     * @param integer $length
     *
     * @return Node
     */
    public function expectAtMost($length)
    {
        $this->maxInput = $length;

        return $this;
    }

    /**
     * Configure this node to expect at least and at most this many digits.
     *
     * @param integer $length
     *
     * @return Node
     */
    public function expectExactly($length)
    {
        return $this->expectAtLeast($length)->expectAtMost($length);
    }

    /**
     * Configures a specific digit as the cancel digit.
     *
     * @param string $digit A single character, one of the DTMF_* constants.
     *
     * @return Node
     */
    public function cancelWith($digit)
    {
        $this->cancelDigit = $digit;

        return $this;
    }

    /**
     * Configures a specific digit as the end of input digit.
     *
     * @param string $digit A single character, one of the DTMF_* constants.
     *
     * @return Node
     */
    public function endInputWith($digit)
    {
        $this->endOfInputDigit = $digit;

        return $this;
    }

    /**
     * Configures the maximum time available between digits before a timeout.
     *
     * @param integer $milliseconds
     *
     * @return Node
     */
    public function maxTimeBetweenDigits($milliseconds)
    {
        $this->timeBetweenDigits = $milliseconds;

        return $this;
    }

    /**
     * Configures the maximum time available for the user to enter valid input
     * per attempt.
     *
     * @param integer $milliseconds
     *
     * @return Node
     */
    public function maxTotalTimeForInput($milliseconds)
    {
        $this->totalTimeForInput = $milliseconds;

        return $this;
    }

    /**
     * True if this node has at least this many digits entered.
     *
     * @param integer $length
     *
     * @return boolean
     */
    public function inputLengthIsAtLeast($length)
    {
        return strlen($this->input) >= $length;
    }

    /**
     * True if this node has at least 1 digit as input, excluding cancel and
     * end of input digits.
     *
     * @return boolean
     */
    public function hasInput()
    {
        return $this->inputLengthIsAtLeast(1);
    }

    /**
     * Returns input.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Returns the phparia client in use.
     *
     * @return \phparia\Client\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the phparia channel in use.
     * 
     * @return \phparia\Resources\Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Returns the phparia dialed channel in use.
     * 
     * @return \phparia\Resources\Channel
     */
    public function getDialedChannel()
    {
        return $this->dialedChannel;
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
     * Sets the phparia client to use by this node.
     *
     * @param \phparia\Client\Client $client
     *
     * @return Node
     */
    public function setAriClient(\phparia\Client\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * True if the user reached the maximum allowed attempts for valid input.
     *
     * @return boolean
     */
    public function maxInputsReached()
    {
        return $this->state == self::STATE_MAX_INPUTS_REACHED;
    }

    /**
     * True if this node is in CANCEL state.
     *
     * @return boolean
     */
    public function wasCancelled()
    {
        return $this->state == self::STATE_CANCEL;
    }

    /**
     * True if this node is in TIMEOUT state.
     *
     * @return boolean
     */
    public function isTimeout()
    {
        return $this->state == self::STATE_TIMEOUT;
    }

    /**
     * True if this node is in COMPLETE state.
     *
     * @return boolean
     */
    public function isComplete()
    {
        return $this->state == self::STATE_COMPLETE;
    }

    /**
     * @return \React\Promise\Promise
     */
    public function playPrePrompts()
    {
        return $this->prePrompts->play();
    }

    /**
     * @return \React\Promise\Promise
     */
    public function playPrompts()
    {
        return $this->prompts->play();
    }

    /**
     * Saves a custom key/value to the registry.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Node
     */
    public function saveCustomData($key, $value)
    {
        $this->registry[$key] = $value;

        return $this;
    }

    /**
     * Returns the value for the given key in the registry.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustomData($key)
    {
        return $this->registry[$key];
    }

    /**
     * True if the given key exists in the registry.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasCustomData($key)
    {
        return isset($this->registry[$key]);
    }

    /**
     * Remove a key/value from the registry.
     *
     * @param string $key
     *
     * @return Node
     */
    public function delCustomData($key)
    {
        unset($this->registry[$key]);

        return $this;
    }

    /**
     * Allow the user to retry input by pressing the cancel digit after entered
     * one or more digits. For example, when entering a 12 number pin, the user
     * might press the cancel digit at the 5th digit to re-enter it. This
     * counts as a failed input, but will not cancel the node. The node will be
     * cancelled only if the user presses the cancel digit with NO input at all.
     *
     * @return Node
     */
    public function cancelWithInputRetriesInput()
    {
        $this->cancelWithInputRetriesInput = true;

        return $this;
    }

    /**
     * Specify a callback function to invoke when the user entered a valid
     * input.
     *
     * @param \Closure $callback
     *
     * @return Node
     */
    public function executeOnValidInput(\Closure $callback)
    {
        $this->executeOnValidInput = $callback;

        return $this;
    }

    /**
     * Executes a callback when the node fails to properly get input from the
     * user (either because of cancel, max attempts reached, timeout).
     *
     * @param \Closure $callback
     *
     * @return Node
     */
    public function executeOnInputFailed(\Closure $callback)
    {
        $this->executeOnInputFailed = $callback;

        return $this;
    }

    /**
     * Returns the total number of input attempts used by the user.
     *
     * @return integer
     */
    public function getTotalInputAttemptsUsed()
    {
        return $this->inputAttemptsUsed;
    }

    /**
     * Internally used to clear the input per input attempt. Also resets state
     * to TIMEOUT.
     *
     * @return Node
     */
    protected function resetInput()
    {
        if ($this->minInput === 0) {
            $this->state = self::STATE_COMPLETE;
        } else {
            $this->state = self::STATE_TIMEOUT;
        }
        $this->input = self::DTMF_NONE;

        return $this;
    }

    /**
     * Convenient hook to execute before calling the onValidInput callback.
     *
     * @return void
     */
    protected function beforeOnValidInput()
    {
        
    }

    /**
     * Convenient hook to execute before calling the onInputFailed callback.
     *
     * @return void
     */
    protected function beforeOnInputFailed()
    {
        
    }

    /**
     * Executes before running the node.
     *
     * @param \closure $callback
     *
     * @return Node
     */
    public function executeBeforeRun(\Closure $callback)
    {
        $this->executeBeforeRun = $callback;

        return $this;
    }

    /**
     * Executes after running the node.
     *
     * @param \Closure $callback
     *
     * @return Node
     */
    public function executeAfterRun(\Closure $callback)
    {
        $this->log("Added execute after run for {$this->name}");
        $this->executeAfterRun = $callback;

        return $this;
    }

    /**
     * Executes after the 1st failed validation.
     *
     * @param \Closure $callback
     *
     * @return Node
     */
    public function executeAfterFailedValidation(\Closure $callback)
    {
        $this->executeAfterFailedValidation = $callback;

        return $this;
    }

    /**
     * Dial a number and bridge it to the current channel or return an error such as busy
     * 
     * @param string $endpoint The endpoint such as 'SIP/5555555555@vitelity-out'
     * @param string $app The stasis application name
     * @param string $callerId Caller id string to use for the dial.
     * @param string $recordingFilename Set to record the call.  File extension determines format.
     * @param string $timeout Dialing timeout.
     * @param string $hangupDigit The digit used to hangup and return the caller to the menu
     * @return \phparia\Node\Node
     * @todo Implement caller id.
     * @todo Play sounds to bridge.
     */
    public function dial($endpoint, $app, $callerId = null, $recordingFilename = null, $timeout = 30, $hangupDigit = Node::DTMF_HASH)
    {
        $this->dial['endpoint'] = $endpoint;
        $this->dial['app'] = $app;
        $this->dial['callerId'] = $callerId;
        $this->dial['recordingFilename'] = $recordingFilename;
        $this->dial['timeout'] = $timeout;
        $this->dial['hangupDigit'] = $hangupDigit;

        return $this;
    }

    /**
     * @return \React\Promise\Promise
     */
    public function doDial()
    {
        $deferred = new \React\Promise\Deferred();

        if (!empty($this->dial)) {
            $endpoint = $this->dial['endpoint'];
            $app = $this->dial['app'];
            $callerId = $this->dial['callerId'];
            $recordingFilename = $this->dial['recordingFilename'];
            $timeout = $this->dial['timeout'];
            $hangupDigit = $this->dial['hangupDigit'];
            $id = uniqid();

            $this->client->getStasisClient()->once(\phparia\Events\Event::STASIS_START . '_' . $id, function($event) use ($recordingFilename) {
                $this->client->channels()->answer($event->getChannel()->getId());
                $this->client->bridges()->addChannel($this->bridge->getId(), $event->getChannel()->getId(), null);

                if (!empty($recordingFilename)) {
                    $parts = pathinfo($recordingFilename);
                    $name = $parts['filename'];
                    $format = $parts['extension'];
                    $this->client->bridges()->record($this->bridge->getId(), $name, $format, null, null, 'overwrite');
                }

                if ($this->playOnAnswer !== null) {
                    $this->client->bridges()->playMedia($this->bridge->getId(), $this->playOnAnswer);
                }
            });

            $this->client->getStasisClient()->once(\phparia\Events\Event::STASIS_END . '_' . $id, function($event) use ($recordingFilename, $deferred) {
                if (!empty($recordingFilename)) {
                    $parts = pathinfo($recordingFilename);
                    $name = $parts['filename'];
                    $this->client->recordings()->stopLiveRecording($name);
                }
                $deferred->resolve();
            });

            // Hangup this channel if the caller hangs up
            $this->client->getStasisClient()->once(\phparia\Events\Event::STASIS_END . '_' . $this->channel->getId(), function($event) use ($recordingFilename, $id) {
                if (!empty($recordingFilename)) {
                    $parts = pathinfo($recordingFilename);
                    $name = $parts['filename'];
                    $this->client->recordings()->stopLiveRecording($name);
                }
                try {
                    $this->client->channels()->deleteChannel($id);
                } catch (\Exception $ignore) {
                    
                }
            });

            $this->client->getStasisClient()->once(\phparia\Events\Event::CHANNEL_DTMF_RECEIVED . '_' . $this->channel->getId(), function($event) use ($id, $hangupDigit) {
                if ($event->getDigit() === $hangupDigit) {
                    try {
                        $this->client->channels()->deleteChannel($id);
                    } catch (\Exception $ignore) {
                        
                    }
                }
            });

            $this->dialedChannel = $this->client->channels()->createChannel($endpoint, null, null, null, $app, 'dialed', $callerId, $timeout, $id);
        } else {
            $deferred->resolve();
        }

        return $deferred->promise();
    }

    /**
     * Record the bridge
     * 
     * @param string $recordingName Without extension and only supporting wav format
     * @param string $terminateDigit
     * @return \phparia\Node\Node
     */
    public function record($recordingName, $format = 'wav', $maxDurationSeconds = null, $maxSilenceSeconds = null, $ifExists = 'overwrite', $beep = false, $terminateOn = self::DTMF_HASH)
    {
        $this->record['recordingName'] = $recordingName;
        $this->record['format'] = $format;
        $this->record['maxDurationSeconds'] = $maxDurationSeconds;
        $this->record['maxSilenceSeconds'] = $maxSilenceSeconds;
        $this->record['ifExists'] = $ifExists;
        $this->record['beep'] = $beep;
        $this->record['terminateOn'] = $terminateOn;

        return $this;
    }

    /**
     * @return \React\Promise\Promise
     */
    public function doRecord()
    {
        $deferred = new \React\Promise\Deferred();

        if (!empty($this->record)) {
            $recordingName = $this->record['recordingName'];
            $format = $this->record['format'];
            $maxDurationSeconds = $this->record['maxDurationSeconds'];
            $maxSilenceSeconds = $this->record['maxSilenceSeconds'];
            $ifExists = $this->record['ifExists'];
            $beep = $this->record['beep'];
            $terminateOn = $this->record['terminateOn'];

            $this->liveRecording = $this->client->bridges()->record($this->bridge->getId(), $recordingName, $format, $maxDurationSeconds, $maxSilenceSeconds, $ifExists, $beep, $terminateOn);

            // @todo Stop recording and resolve when the callee hangs up

            $this->liveRecording->onceRecordingFailed(function($event) use ($deferred) {
                $this->log('onceRecordingFailed');
                $deferred->reject();
            });

            $this->liveRecording->onceRecordingFinished(function($event) use ($deferred) {
                $this->log('onceRecordingFinished');
                $deferred->resolve();
            });

            $this->liveRecording->onceRecordingStarted(function($event) use ($deferred) {
                $this->log('onceRecordingStarted');
                $deferred->progress();
            });
        } else {
            $deferred->resolve();
        }

        return $deferred->promise();
    }

    /**
     * Executes this node.
     *
     * @return Node
     */
    public function run()
    {
        // Reset things in case this node was run once before (but do not reset preprompts)
        $this->input = self::DTMF_NONE;
        $this->state = self::STATE_NOT_RUN;
        $this->progress = self::PROGRESS_NONE;
        $this->inputAttemptsUsed = 0;
        $this->liveRecording = null;

        if ($this->executeBeforeRun !== null) {
            $this->log('Executing before run');
            $callback = $this->executeBeforeRun;
            $callback($this);
        }

        // @todo $input in prompts/preprompts will always be an empty string or a single digit... name it $digit instead of $input everywhere maybe?
        $this->playPrePrompts()->then(function($input) {
            $this->log("Finished preprompts with input: $input and appending to: {$this->input}");
            $this->prePrompts = new SoundChain($this->client, $this->channel, $this->bridge);
            if (substr($input, -1)) {
                if ($this->processDigit(substr($input, -1)) === true) { // Done
                    $this->doDial()->then(function() {
                        $this->doRecord()->then(function() {
                            $this->finished();
                        });
                    });
                }
            }
            $this->playPrompts()->then(function($input) {
                $this->log("Finished prompts with input: $input and appending to: {$this->input}");
                if (substr($input, -1)) {
                    if ($this->processDigit(substr($input, -1)) === true) { // Done
                        $this->doDial()->then(function() {
                            $this->doRecord()->then(function() {
                                $this->finished();
                            });
                        });
                    }
                }
                $this->doInput()->then(function() {
                    $this->doDial()->then(function() {
                        $this->doRecord()->then(function() {
                            $this->finished();
                        });
                    });
                });
            });
        });

        return $this;
    }

    /**
     * @todo Refactor the duplicate code in this
     * @return \React\Promise\Promise
     */
    public function doInput()
    {
        $this->timeBetweenDigits = self::TIME_INFINITE;
        $this->totalTimeForInput = self::TIME_INFINITE;

        $deferred = new \React\Promise\Deferred();

        // Not expecting input
        if (($this->minInput < 1) && ($this->maxInput < 1)) {
            $deferred->resolve();

            return $deferred->promise();
        }

        $this->dtmfCallback = function($event) use($deferred) {
            // Reset the time between digits timer
            if ($this->timeBetweenDigitsTimer !== null) {
                $this->log('Resetting time between digits timer');
                $timeBetweenDigitsCallback = $this->timeBetweenDigitsTimer->getCallback();
                $this->timeBetweenDigitsTimer->cancel();
                $this->timeBetweenDigitsTimer = $this->client->getStasisLoop()->addTimer($this->timeBetweenDigits / 1000, $timeBetweenDigitsCallback);
            }

            // Reset the total time for input timer
            if ($this->totalTimeForInputTimer !== null) {
                $this->log('Resetting total time for input timer');
                $totalTimeForInputCallback = $this->totalTimeForInputTimer->getCallback();
                $this->totalTimeForInputTimer->cancel();
                $this->totalTimeForInputTimer = $this->client->getStasisLoop()->addTimer($this->totalTimeForInput / 1000, $totalTimeForInputCallback);
            }

            if ($this->processDigit($event->getDigit()) === true) { // Done
                if ($this->dtmfCallback !== null) {
                    $this->client->getStasisClient()->removeListener(\phparia\Events\Event::CHANNEL_DTMF_RECEIVED . '_' . $this->channel->getId(), $this->dtmfCallback);
                    $this->log("Stopped listening for input");
                }

                $deferred->resolve();
            }

            $this->lastInputTime = round(microtime(true) * 1000);
        };

        // Time between digits timer
        if ($this->timeBetweenDigits !== self::TIME_INFINITE) {
            $this->timeBetweenDigitsTimer = $this->client->getStasisLoop()->addTimer($this->timeBetweenDigits / 1000, function() {
                $this->log("Exceeded {$this->timeBetweenDigits} ms between digits for input");
                $this->inputAttemptsUsed++;

                // @todo Start playing the prompts again
                $this->playPrompts();
            });
        }

        // Total time for input timer
        if ($this->totalTimeForInput !== self::TIME_INFINITE) {
            $this->totalTimeForInputTimer = $this->client->getStasisLoop()->addTimer($this->totalTimeForInput / 1000, function() {
                $this->log("Exceeded {$this->totalTimeForInput} ms total time for input");
                $this->inputAttemptsUsed++;

                // @todo Start playing the prompts again
                $this->playPrompts();
            });
        }

        $this->channel->onChannelDtmfReceived($this->dtmfCallback);

        return $deferred->promise();
    }

    /**
     * @param string $digit
     * @return boolean True if input is complete, false to continue
     */
    private function processDigit($digit)
    {
        if (strlen($digit) === 0) {
            throw new Exception\NodeException("No digit to process");
        }

        $this->log("Processing digit: $digit");

        // Process the digit
        switch ($digit) {
            case $this->cancelDigit:
                $this->state = self::STATE_CANCEL;
                $this->log("Got cancel digit: $digit");
                if ($this->cancelWithInputRetriesInput && $this->hasInput()) {
                    $this->log("Cancelled input, retrying");
                    $this->input = self::DTMF_NONE;

                    return false;
                } else {

                    return true;
                }
            case $this->endOfInputDigit;
                $this->state = self::STATE_COMPLETE;
                $this->log("Got end of input digit: $digit, input: {$this->input}");

                if ($this->validate()) {
                    if ($this->executeOnValidInput !== null) {
                        $callback = $this->executeOnValidInput;
                        $callback($this);
                    }
                } else {
                    if ($this->executeOnInputFailed !== null) {
                        $callback = $this->executeOnInputFailed;
                        $callback($this);
                    }

                    // @todo Make sure this is the right spot to call this
                    if ($this->executeAfterFailedValidation !== null) {
                        $callback = $this->executeAfterFailedValidation;
                        $callback($this);
                    }

                    $this->inputAttemptsUsed++;
                    if ($this->inputAttemptsUsed >= $this->totalAttemptsForInput) {
                        // @todo Play the onMaxValidInputAttempts message
                        $this->onMaxValidInputAttempts;
                    }
                    $this->input = self::DTMF_NONE;

                    return false;
                }

                return true;
            default:
                $this->log("Got digit: $digit");
                $this->input .= $digit;
                if (strlen($this->input) >= $this->maxInput) {
                    $this->state = self::STATE_COMPLETE;
                    $this->log("Got the maximum input: {$this->input}");

                    // @todo Refactor the validation code as it duplicates above
                    if ($this->validate()) {
                        if ($this->executeOnValidInput !== null) {
                            $callback = $this->executeOnValidInput;
                            $callback($this);
                        }
                    } else {
                        if ($this->executeOnInputFailed !== null) {
                            $callback = $this->executeOnInputFailed;
                            $callback($this);
                        }

                        // @todo Make sure this is the right spot to call this
                        if ($this->executeAfterFailedValidation !== null) {
                            $callback = $this->executeAfterFailedValidation;
                            $callback($this);
                        }

                        $this->inputAttemptsUsed++;
                        if ($this->inputAttemptsUsed >= $this->totalAttemptsForInput) {
                            // @todo Play the onMaxValidInputAttempts message
                            $this->onMaxValidInputAttempts;
                        }
                        $this->input = self::DTMF_NONE;

                        return false;
                    }

                    return true;
                }

                return false;
        }
    }

    /**
     * Call when node is finished to let the node controller to process and continue
     */
    public function finished()
    {
        if ($this->executeAfterRun !== null) {
            $this->log('Executing after run');
            $callback = $this->executeAfterRun;
            $callback($this);
        } else {
            $this->log('Nothing to execute after run');
        }

        $this->log('Emitting event finished');
        if ($this->state === self::STATE_NOT_RUN) {
            $this->state = self::STATE_COMPLETE;
        }
        $this->client->getStasisClient()->emit(self::EVENT_FINISHED, array($this));
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    protected function log($msg)
    {
        $logger = $this->client->getLogger();
        $logger->notice("Node: {$this->name}: $msg");
    }

    /**
     * Returns the node name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Maps the current node state to a human readable string.
     *
     * @param integer $state One of the STATE_* constants.
     *
     * @return string
     * @throws Exception\NodeException
     */
    protected function stateToString($state)
    {
        switch ($state) {
            case self::STATE_CANCEL:
                return "cancel";
            case self::STATE_COMPLETE:
                return "complete";
            case self::STATE_NOT_RUN: // a string like 'foo' matches here?
                return "not run";
            case self::STATE_TIMEOUT:
                return "timeout";
            case self::STATE_MAX_INPUTS_REACHED:
                return "max valid input attempts reached";
            default:
                throw new NodeException("Bad state for node");
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return
                "[ Node: " . $this->name
                . " input: (" . $this->input . ") "
                . " state: (" . $this->stateToString($this->state) . ")"
                . "]"
        ;
    }

}
