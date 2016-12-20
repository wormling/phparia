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

namespace phparia\Resources;

/**
 * A text message.
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class TextMessage extends Response
{
    /**
     * @var string The text of the message.
     */
    private $body;

    /**
     * @var string A technology specific URI specifying the source of the message. For sip and pjsip technologies, any SIP URI can be specified. For xmpp, the URI must correspond to the client connection being used to send the message.
     */
    private $from;

    /**
     * @var string A technology specific URI specifying the destination of the message. Valid technologies include sip, pjsip, and xmp. The destination of a message should be an endpoint.
     */
    private $to;

    /**
     * @var array (optional) - Technology specific key/value pairs associated with the message.
     */
    private $variables;

    /**
     * @return string The text of the message.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string A technology specific URI specifying the source of the message. For sip and pjsip technologies, any SIP URI can be specified. For xmpp, the URI must correspond to the client connection being used to send the message.
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string A technology specific URI specifying the destination of the message. Valid technologies include sip, pjsip, and xmp. The destination of a message should be an endpoint.
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return TextMessageVariable[] (optional) - Technology specific key/value pairs associated with the message.
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->body = $this->getResponseValue('body');
        $this->from = $this->getResponseValue('from');
        $this->to = $this->getResponseValue('to');
        $this->variables = $this->getResponseValue('variables');
    }

}
