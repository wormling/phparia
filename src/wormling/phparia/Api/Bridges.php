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
use phparia\Resources\Bridge;
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
        $uri = 'bridges';
        $response = $this->client->getEndpoint()->get($uri);

        $bridges = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $bridge) {
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
        $uri = 'bridges';
        $response = $this->client->getEndpoint()->post($uri, [
            'form_params' => [
                'bridgeId' => $bridgeId,
                'type' => $type,
                'name' => $name,
            ]
        ]);

        return new Bridge($this->client, \GuzzleHttp\json_decode($response->getBody()));
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
        $uri = "bridges/$bridgeId";
        $response = $this->client->getEndpoint()->post($uri, [
            'form_params' => [
                'type' => $type,
                'name' => $name,
            ]
        ]);

        return new Bridge($this->client, \GuzzleHttp\json_decode($response->getBody()));
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
        $uri = "bridges/$bridgeId";
        try {
            $response = $this->client->getEndpoint()->get($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }

        return new Bridge($this->client, \GuzzleHttp\json_decode($response->getBody()));
    }

    /**
     * Shut down a bridge. If any channels are in this bridge, they will be removed and resume whatever they were doing beforehand.
     *
     * @param string $bridgeId Bridge's id
     * @throws NotFoundException
     */
    public function deleteBridge($bridgeId)
    {
        $uri = "bridges/$bridgeId";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
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
        $uri = "bridges/$bridgeId/addChannel";
        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'channel' => $channel,
                    'role' => $role,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Remove a channel from a bridge.
     *
     * @param string $bridgeId Bridge's id
     * @param string $channel (required) Ids of channels to remove from bridge.  Allows comma separated values.
     * @throwe InvalidParameterException
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function removeChannel($bridgeId, $channel)
    {
        $uri = "bridges/$bridgeId/removeChannel";
        try {
            $this->client->getEndpoint()->post($uri, [
                'form_params' => [
                    'channel' => $channel,
                ]
            ]);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Set a channel as the video source in a multi-party mixing bridge. This operation has no effect on bridges with
     * two or fewer participants.
     *
     * @param string $id Bridge's id
     * @param string $channelId Channel's id
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function setVideoSource($id, $channelId)
    {
        $uri = "bridges/$id/videoSource/$channelId";
        try {
            $this->client->getEndpoint()->post($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    /**
     * Removes any explicit video source in a multi-party mixing bridge. This operation has no effect on bridges with
     * two or fewer participants. When no explicit video source is set, talk detection will be used to determine the
     * active video stream.
     *
     * @param string $id Bridge's id
     * @throws NotFoundException
     * @throws ConflictException
     * @throws UnprocessableEntityException
     */
    public function clearVideoSource($id)
    {
        $uri = "bridges/$id/videoSource";
        try {
            $this->client->getEndpoint()->delete($uri);
        } catch (RequestException $e) {
            $this->processRequestException($e);
        }
    }

    public function getType()
    {
        return 'bridges';
    }
}
