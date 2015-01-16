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

use phparia\Entity\Playback;

/**
 * Playbacks API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class PlaybacksApi
{

    /**
     * Get a playback's details.
     * 
     * @param string $playbackId Playback's id
     * @return Playback
     */
    public function getPlayback($playbackId)
    {
        $uri = "/playbacks/$playbackId";
        $response = $this->client->getAriEndpoint()->get($uri);

        return new Playback($response);
    }

    /**
     * Stop a playback.
     * 
     * @param string $playbackId Playback's id
     */
    public function stopPlayback($playbackId)
    {
        $uri = "/playbacks/$playbackId";
        $this->client->getAriEndpoint()->delete($uri);
    }
    
    /**
     * Control a playback.
     * 
     * @param string $playbackId Playback's id
     * @param string $operation (required) Operation to perform on the playback.
     */
    public function controlPlayback($playbackId, $operation)
    {
        $uri = "/playbacks/$playbackId/control";
        $this->client->getAriEndpoint()->post($uri, array(
            'operation' => $operation,
        ));
    }

}
