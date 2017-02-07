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

namespace phparia\Resources;

/**
 * Detailed information about a contact on an endpoint.
 *
 * @author Eric Smith <eric2733@gmail.com>
 */
class ContactInfo extends Response
{
    /**
     * @var string The Address of Record this contact belongs to.
     */
    private $aor;

    /**
     * @var string The current status of the contact.
     */
    private $contactStatus;

    /**
     * @var string (optional) - Current round trip time, in microseconds, for the contact.
     */
    private $roundtripUsec;

    /**
     * @var string The location of the contact.
     */
    private $uri;

    /**
     * @return string The Address of Record this contact belongs to.
     */
    public function getAor()
    {
        return $this->aor;
    }

    /**
     * @return string The current status of the contact.
     */
    public function getContactStatus()
    {
        return $this->contactStatus;
    }

    /**
     * @return string (optional) - Current round trip time, in microseconds, for the contact.
     */
    public function getRoundtripUsec()
    {
        return $this->roundtripUsec;
    }

    /**
     * @return string The location of the contact.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        parent::__construct($response);

        $this->aor = $this->getResponseValue('aor');
        $this->contactStatus = $this->getResponseValue('contact_status');
        $this->roundtripUsec = $this->getResponseValue('roundtrip_usec');
        $this->uri = $this->getResponseValue('uri');
    }

}
