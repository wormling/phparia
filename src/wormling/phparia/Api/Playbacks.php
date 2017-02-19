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

namespace phparia\Api;

use GuzzleHttp\Exception\RequestException;
use phparia\Client\AriClientAware;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Resources\Playback;

/**
 * Playbacks API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Playbacks extends AriClientAware
{
    const OPERATION_PAUSE = 'pause';
    const OPERATION_UNPAUSE = 'unpause';
    const OPERATION_REVERSE = 'reverse';
    const OPERATION_FORWARD = 'forward';
    const OPERATION_RESTART = 'restart';

    /**
     * Get a playback's details.
     *
     * @param string $playbackId Playback's id
     * @return Playback
     * @throws NotFoundException
     */
    public function getPlayback($playbackId)
    {
        $uri = "playbacks/$playbackId";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Playback($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Stop a playback.
     *
     * @param string $playbackId Playback's id
     * @throws NotFoundException
     */
    public function stopPlayback($playbackId)
    {
        $uri = "playbacks/$playbackId";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Control a playback.
     *
     * @param string $playbackId Playback's id
     * @param string $operation (required) Operation to perform on the playback.  Allowed values: restart, pause, unpause, reverse, forward.
     * @throws ConflictException
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function controlPlayback($playbackId, $operation)
    {
        $uri = "playbacks/$playbackId/control";
        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'operation' => $operation,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }
}
