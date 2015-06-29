<?php
/**
 * Created by PhpStorm.
 * User: wormling
 * Date: 29.06.15
 * Time: 13:32
 */
namespace phparia\Client;


/**
 * @author Brian Smith <wormling@gmail.com>
 */
interface AriClientAwareInterface
{
    /**
     * @return AriClient
     */
    public function getClient();
}