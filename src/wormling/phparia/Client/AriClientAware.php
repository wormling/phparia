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

use GuzzleHttp\Exception\RequestException;
use phparia\Exception\ConflictException;
use phparia\Exception\ForbiddenException;
use phparia\Exception\InvalidParameterException;
use phparia\Exception\NotFoundException;
use phparia\Exception\PreconditionFailedException;
use phparia\Exception\ServerException;
use phparia\Exception\UnprocessableEntityException;

/**
 * @author Brian Smith <wormling@gmail.com>
 */
abstract class AriClientAware implements AriClientAwareInterface
{
    /**
     * @var AriClient
     */
    protected $client;

    public function __construct(AriClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return AriClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @todo This doesn't really belong here
     * @param RequestException $e
     * @throws ConflictException
     * @throws PreconditionFailedException
     * @throws InvalidParameterException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws UnprocessableEntityException
     * @throws ServerException
     */
    protected function processRequestException(RequestException $e) {
        switch ($e->getCode()) {
            case 400: // Missing parameter
                throw new InvalidParameterException($e);
            case 403: // Forbidden
                throw new ForbiddenException($e);
            case 404: // Does not exist
                throw new NotFoundException($e);
            case 409:
                throw new ConflictException($e);
            case 412:
                throw new PreconditionFailedException($e);
            case 422:
                throw new UnprocessableEntityException($e);
            case 500:
                throw new ServerException($e);
            default:
                throw $e;
        }
    }
}
