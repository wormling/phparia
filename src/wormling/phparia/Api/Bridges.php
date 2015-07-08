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

use Pest_BadRequest;
use Pest_Conflict;
use Pest_InvalidRecord;
use Pest_NotFound;
use phparia\Resources\Bridge;
use phparia\Resources\LiveRecording;
use phparia\Resources\Playback;
use phparia\Exception\ConflictException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\UnprocessableEntityException;

/**
 * Bridges API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Bridges extends MediaBase
{
    /**
     * List all active bridges in Asterisk.
     *
     * @return Bridge[]
     */
    public function getBridges()
    {
        $uri = '/bridges';
        $response = $this->client->getEndpoint()->get($uri);

        $bridges = [];
        foreach ((array)$response as $bridge) {
            $bridges[] = new Bridge($this->client, $bridge);
        }

        return $bridges;
    }

    /**
     * Create a new bridge. This bridge persists until it has been shut down, or Asterisk has been shut down.
     *
     * @param string $bridgeId Unique ID to give to the bridge being created.
     * @param string $type Comma separated list of bridge type attributes (mixing, holding, dtmf_events, proxy_media).
     * @param string $name Name to give to the bridge being created.
     * @return Bridge
     */
    public function createBridge($bridgeId, $type, $name)
    {
        $uri = '/bridges';
        $response = $this->client->getEndpoint()->post($uri, array(
            'bridgeId' => $bridgeId,
            'type' => $type,
            'name' => $name,
        ));

        return new Bridge($this->client, $response);
    }

    /**
     * Create a new bridge or updates an existing one. This bridge persists until it has been shut down, or Asterisk has been shut down.
     *
     * @param string $bridgeId Unique ID to give to the bridge being created.
     * @param string $type Comma separated list of bridge type attributes (mixing, holding, dtmf_events, proxy_media) to set.
     * @param string $name Set the name of the bridge.
     * @return Bridge
     */
    public function updateBridge($bridgeId, $type, $name)
    {
        $uri = "/bridges/$bridgeId";
        $response = $this->client->getEndpoint()->post($uri, array(
            'type' => $type,
            'name' => $name,
        ));

        return new Bridge($this->client, $response);
    }

    /**
     * Get bridge details.
     *
     * @param string $bridgeId Bridge's id
     * @return Bridge
     * @throws NotFoundException
     */
    public function getBridge($bridgeId)
    {
        $uri = "/bridges/$bridgeId";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        }

        return new Bridge($this->client, $response);
    }

    /**
     * Shut down a bridge. If any channels are in this bridge, they will be removed and resume whatever they were doing beforehand.
     *
     * @param string $bridgeId Bridge's id
     * @throws NotFoundException
     */
    public function deleteBridge($bridgeId)
    {
        $uri = "/bridges/$bridgeId";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (Pest_NotFound $e) {
            throw new NotFoundException($e);
        }
    }

    /**
     * Add a channel to a bridge.
     *
     * @param string $bridgeId Bridge's id
     * @param string $channel (required) Ids of channels to add to bridge.  Allows comma separated values.
     * @param string $role Channel's role in the bridge
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function addChannel($bridgeId, $channel, $role = null)
    {
        $uri = "/bridges/$bridgeId/addChannel";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'channel' => $channel,
                'role' => $role,
            ));
        } catch (Pest_BadRequest $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_NotFound $e) { // Bridge not found
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Bridge not in Stasis application; Channel currently recording
            throw new ConflictException($e);
        } catch (Pest_InvalidRecord $e) { // Channel not in Stasis application
            throw new UnprocessableEntityException($e);
        }
    }

    /**
     * Remove a channel from a bridge.
     *
     * @param string $bridgeId Bridge's id
     * @param string $channel (required) Ids of channels to remove from bridge.  Allows comma separated values.
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function removeChannel($bridgeId, $channel)
    {
        $uri = "/bridges/$bridgeId/removeChannel";
        try {
            $this->client->getEndpoint()->post($uri, array(
                'channel' => $channel,
            ));
        } catch (Pest_BadRequest $e) { // Channel not found
            throw new NotFoundException($e);
        } catch (Pest_NotFound $e) { // Bridge not found
            throw new NotFoundException($e);
        } catch (Pest_Conflict $e) { // Bridge not in Stasis application
            throw new ConflictException($e);
        } catch (Pest_InvalidRecord $e) { // Channel not in Stasis application
            throw new UnprocessableEntityException($e);
        }
    }

    /**
     * Play music on hold to a bridge or change the MOH class that is playing.
     *
     * @param string $bridgeId Bridge's id
     * @param string $mohClass Music on hold class to use
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function startMusicOnHold($bridgeId, $mohClass)
    {
        parent::startMusicOnHold($bridgeId, $mohClass);
    }

    /**
     * Stop playing music on hold to a bridge. This will only stop music on hold being played via POST bridges/{bridgeId}/moh.
     *
     * @param string $bridgeId Bridge's id
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function stopMusicOnHold($bridgeId)
    {
        parent::stopMusicOnHold($bridgeId);
    }

    /**
     * Start playback of media on a bridge. The media URI may be any of a number of URI's. Currently
     * sound:, recording:, number:, digits:, characters:, and tone: URI's are supported. This operation
     * creates a playback resource that can be used to control the playback of media (pause, rewind,
     * fast forward, etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $bridgeId Bridge's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMedia($bridgeId, $media, $lang = null, $offsetms = null, $skipms = null, $playbackId = null)
    {
        return parent::playMedia($bridgeId, $media, $lang, $offsetms, $skipms, $playbackId);
    }

    /**
     * Start playback of media on a bridge. The media URI may be any of a number of URI's. Currently
     * sound:, recording:, number:, digits:, characters:, and tone: URI's are supported. This operation
     * creates a playback resource that can be used to control the playback of media (pause, rewind,
     * fast forward, etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $bridgeId Bridge's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public function playMediaWithId(
        $bridgeId,
        $media,
        $lang = null,
        $offsetms = null,
        $skipms = null,
        $playbackId = null
    ) {
        return parent::playMediaWithId($bridgeId, $media, $lang, $offsetms, $skipms, $playbackId);
    }

    /**
     * Start a recording. This records the mixed audio from all channels participating in this bridge.
     *
     * @param string $bridgeId
     * @param string $name
     * @param string $format
     * @param int $maxDurationSeconds
     * @param int $maxSilenceSeconds
     * @param string $ifExists
     * @param boolean $beep
     * @param string $terminateOn
     * @return LiveRecording
     * @throws InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function record(
        $bridgeId,
        $name,
        $format,
        $maxDurationSeconds = null,
        $maxSilenceSeconds = null,
        $ifExists = null,
        $beep = null,
        $terminateOn = null
    ) {
        return parent::record($bridgeId, $name, $format, $maxDurationSeconds, $maxSilenceSeconds,
            $ifExists, $beep, $terminateOn);
    }

    public function getType()
    {
        return 'bridges';
    }
}
