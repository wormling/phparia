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

use phparia\Client\AriClientAware;
use phparia\Resources\Sound;

/**
 * Sounds API
 *
 * @author Brian Smith <wormling@gmail.com>
 */
class Sounds extends AriClientAware
{
    /**
     * List all sounds.
     *
     * @return Sound[]
     */
    public function getSounds()
    {
        $uri = 'sounds';
        $response = $this->client->getEndpoint()->get($uri);

        $sounds = [];
        foreach (\GuzzleHttp\json_decode($response->getBody()) as $sound) {
            $sounds[] = new Sound($sound);
        }

        return $sounds;
    }

    /**
     * Get a sound's details.
     *
     * @param string $soundId Sound's id
     * @return Sound
     */
    public function getSound($soundId)
    {
        $uri = "sounds/$soundId";
        $response = $this->client->getEndpoint()->get($uri);

        return new Sound(\GuzzleHttp\json_decode($response->getBody()));
    }
}
