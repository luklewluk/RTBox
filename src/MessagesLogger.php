<?php
/**
 * Created by PhpStorm.
 * User: luklew
 * Date: 23.03.15
 * Time: 17:40
 */

namespace luklew\RTBox;


use noFlash\TinyWs\Message;

/**
 * Class MessagesLogger
 * @package luklew\RTBox
 */
class MessagesLogger implements ClientInterface{

    /**
     * @var Message[] - temporary way to keep last messages (Message objects)
     */
    protected $lastMessages = [];

    /**
     * Save a message in log.
     * @param Message $message
     */
    public function sendMessage(Message $message)
    {
        // Temporary limit to 20 until I implement better way to keep messages
        if (sizeof($this->lastMessages) >= 20)
        {
            array_shift($this->lastMessages);
        }
        $this->lastMessages[] = $message;
    }

    /**
     * @param int $number
     * Returns array with last messages.
     */
    public function getLastMessages($number = 20){
        return $this->lastMessages;
    }
}