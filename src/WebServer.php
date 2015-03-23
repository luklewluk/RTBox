<?php
namespace luklew\RTBox;

/**
 * Created by PhpStorm.
 * User: luklew
 * Date: 16.03.15
 * Time: 18:42
 */

use noFlash\CherryHttp\HttpClient;
use noFlash\CherryHttp\HttpRequest;
use noFlash\CherryHttp\HttpResponse;
use noFlash\TinyWs\ClientsHandlerInterface;
use noFlash\TinyWs\DataFrame;
use noFlash\TinyWs\Message;
use noFlash\TinyWs\WebSocketClient;
use Psr\Log\LoggerInterface;

class WebServer implements ClientsHandlerInterface
{

    /** @var LoggerInterface */
    protected $logger;
    /** @var ShoutBoxUser[] */
    protected $users = [];
    /** @var Message[] - temporary way to keep last messages */
    protected $last_messages = [];

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Called when new client is connected & upgrade is possible.
     * Method is called only if valid upgrade headers are provided.
     *
     * @param HttpClient $client
     * @param HttpRequest $request
     * @param HttpResponse $response
     *
     * @return HttpResponse
     */
    public function onUpgrade(HttpClient $client, HttpRequest $request, HttpResponse $response)
    {
        return $response;
    }

    /**
     * Called just after client upgrade.
     *
     * Method saves a new user to user array.
     * Also it sends the last messages stored in last_messages.
     *
     * @param WebSocketClient $client
     */
    public function onAfterUpgrade(WebSocketClient $client)
    {
        $user = new ShoutBoxUser();
        $user->onConnect($client);
        $this->users[$client->getPeerName()] = $user;

        if (sizeof($this->last_messages) !== 0) {
            foreach ($this->last_messages as $message) {
                $client->pushData($message);
            }
        }
    }

    /**
     * Called everytime new message is received.
     * This method is manages messages currently and does things like:
     * - Checking messages if are correct
     * - Removing HTML chars
     * - Logging in && setting nicknames
     * - Sending errors
     * - Sending notifies
     * - Sending warnings
     * - Sending messages
     * TODO: Write it in more object oriented way
     *
     * @param WebSocketClient $client
     * @param Message $message
     *
     */
    public function onMessage(WebSocketClient $client, Message $message)
    {
        $raw_msg = htmlspecialchars($message->getPayload());
        if (substr($raw_msg, 0, 5) === '/NICK' && empty($this->users[$client->getPeerName()]->nickname)) {
            $nick = substr($raw_msg, 5);
            $nick = preg_replace("/[^a-zA-Z0-9]+/", "", $nick);
            if ($nick !== 'null' && !empty($nick) && strlen($nick) < 15) {
                $this->users[$client->getPeerName()]->nickRename($nick);
                $msg = new Message();
                $msg->setPayload('loggedin');
                $client->pushData($msg);
            } else {
                $msg = new Message();
                $msg->setPayload('loginerr');
                $client->pushData($msg);
            }

        } elseif ($raw_msg !== '') {
            if (!empty($this->users[$client->getPeerName()]->nickname)) {
                $nickname = $this->users[$client->getPeerName()]->nickname;
                $html_msg = $this->createMessage($nickname, $raw_msg);
                $this->sendShoutToAll($html_msg);
            } else {
                $msg = new Message();
                $msg->setPayload('nonickname');
                $client->pushData($msg);
            }
        }
    }

    /**
     * If something bad happen during communication this method is called with failure code & client object
     *
     * @param WebSocketClient $client
     * @param int $code Failure code as described by DataFrame::CODE_* constants
     *
     * @see DataFrame
     */
    public function onException(WebSocketClient $client, $code)
    {
    }

    /**
     * Called everytime valid pong packet is received in response to sent ping packet.
     *
     * @param WebSocketClient $client
     * @param DataFrame $pongFrame
     */
    public function onPong(WebSocketClient $client, DataFrame $pongFrame)
    {
    }

    /**
     * Called when client disconnection was requested (either by server or client).
     * Note: This method can be called multiple times for single client. Implementation should ignore unwanted calls
     * without throwing exception.
     * Removing user from the array.
     *
     * @param WebSocketClient $client
     *
     * @see Client::disconnect()
     * @see \noFlash\CherryHttp\StreamServerNode::disconnect()
     */
    public function onClose(WebSocketClient $client)
    {
        unset($this->users[$client->getPeerName()]);
    }


    /**
     * @param $nickname
     * @param $message
     * @return string JSON:
     * nick: $nickname
     * date: [current server time]
     * text: $message
     */
    protected function createMessage($nickname, $message)
    {
        $msg['nick'] = $nickname;
        $msg['date'] = date('j-m-H:i');
        $msg['text'] = $message;
        return json_encode($msg);
    }

    /**
     * Receives message as JSON, puts it in Message object and sends it to every user.
     * Also saves the last message in last_messages array - it is a temporary solution.
     * @param string $message
     */
    protected function sendShoutToAll($message)
    {
        $msg = new Message();
        $msg->setPayload($message);
        /** @var ShoutBoxUser $user */
        foreach ($this->users as $user) {
            $user->sendMessage($msg);

        }
        if (sizeof($this->last_messages) >= 20)
        {
            array_shift($this->last_messages);
        }
        $this->last_messages[] = $msg;
    }
}
