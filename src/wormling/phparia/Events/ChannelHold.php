<?php
/**
 * Created by Piskle.
 * Date: 8/22/16
 * Time: 3:00 PM
 */

namespace phparia\Events;

use phparia\Client\AriClient;
use phparia\Resources\Channel;


class ChannelHold extends Event implements IdentifiableEventInterface
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function getEventId()
    {
        return "{$this->getType()}_{$this->getChannel()->getId()}";
    }

    /**
     * @param AriClient $client
     * @param string $response
     */
    public function __construct(AriClient $client, $response)
    {
        parent::__construct($client, $response);

        $this->channel = $this->getResponseValue('channel', 'phparia\Resources\Channel', $client);
    }
}