<?php

namespace luklew\RTBox;


use noFlash\TinyWs\Message;

/**
 * Interface ClientInterface
 * @package luklew\RTBox
 */
interface ClientInterface {

    /**
     * Gets Message object and sends it to client
     * @param Message $message
     */
    public function sendMessage(Message $message);
}