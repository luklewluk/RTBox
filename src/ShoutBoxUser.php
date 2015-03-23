<?php
/**
 * Created by PhpStorm.
 * User: luklew
 * Date: 16.03.15
 * Time: 19:01
 */

namespace luklew\RTBox;


use noFlash\TinyWs\Message;
use noFlash\TinyWs\WebSocketClient;

/**
 * Class ShoutBoxUser
 * Holds WebSocketClient data.
 * @package luklew\RTBox
 */
class ShoutBoxUser implements ClientInterface
{

    /** @var WebSocketClient */
    protected $websocket;

    /** @var string */
    public $nickname;

    /**
     * @param WebSocketClient $client
     */
    public function onConnect(WebSocketClient $client)
    {
        $this->websocket = $client;
    }

    /**
     * @param Message $message
     */
    public function sendMessage(Message $message)
    {
        $this->websocket->pushData($message);
    }

    /**
     * @param string $new_nick
     */
    public function nickRename($new_nick)
    {
        $this->nickname = $new_nick;
    }
}
