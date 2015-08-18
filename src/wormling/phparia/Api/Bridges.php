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
use phparia\Exception\ConflictException;
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

    public function getType()
    {
        return 'bridges';
    }
}
