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

namespace phparia\Client;

use Pest_BadRequest;
use Pest_Conflict;
use Pest_NotFound;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Resources\Playback;

/**
 * Playbacks API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Playbacks extends Base
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
        $uri = "/playbacks/$playbackId";
        try {
            $response = $this->client->getAriEndpoint()->get($uri);
        } catch (Pest_NotFound $e) { // Playback not found
            throw new NotFoundException($e);
        }

        return new Playback($this->client, $response);
    }

    /**
     * Stop a playback.
     *
     * @param string $playbackId Playback's id
     * @throws NotFoundException
     */
    public function stopPlayback($playbackId)
    {
        $uri = "/playbacks/$playbackId";
        try {
            $this->client->getAriEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) { // Playback not found
            throw new NotFoundException($e);
        }
    }

    /**
     * Control a playback.
     *
     * @param string $playbackId Playback's id
     * @param string $operation (required) Operation to perform on the playback.
     * @throws ConflictException
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function controlPlayback($playbackId, $operation)
    {
        $uri = "/playbacks/$playbackId/control";
        try {
            $this->client->getAriEndpoint()->post($uri, array(
                'operation' => $operation,
            ));
        } catch (Pest_BadRequest $e) {
            throw new InvalidParameterException($e);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) {
            throw new ConflictException($e);
        }
    }

}
