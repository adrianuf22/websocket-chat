<?php

namespace App\Chat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Exception;
use SplObjectStorage;

class Chat implements MessageComponentInterface {
    const USER_JOIN_US = 'USER_JOIN_US';
    const USER_LEFT_US = 'USER_LEFT_US';
    const MESSAGE = 'MESSAGE';
    const AVAILABLE_USERS = 'AVAILABLE_USERS';
    
    protected $clients;

    public function __construct() {
        $this->log("Starting server...");
        
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->log("Connection was opened!");
        
        $extraData = $this->getExtraData($conn);
        
        $resourceId = $conn->resourceId;
        $nickname = $this->extractNicknameFromExtraData($extraData);
        
        $client = new Client($conn);
        $client->setNickname($nickname);
        
        $this->clients->attach($client);
        
        $this->log("New connection!");
        
        $clientsInfo = $this->getConnectedClientsInfo($resourceId);
        $this->sendMessage($resourceId, new EventObject(self::AVAILABLE_USERS, $clientsInfo));
        
        $this->sendMessageForAll(new EventObject(self::USER_JOIN_US, [ "rid" => $resourceId, "nickname" => $nickname ]), $client);
    }

    public function onMessage(ConnectionInterface $conn, $rawData) {
        $this->log("Message received! Data: {$rawData}");
        
        $receiver = new ReceiverObject($rawData);
        
        $eventMessage = new EventObject(self::MESSAGE, $receiver->message());

        if (!empty($receiver->resourceId())) {
            $this->sendMessage($receiver->resourceId(), $eventMessage);
            return;
        }
        
        $this->sendMessageForAll($eventMessage, $conn);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->log("Closing connection...");
        
        $client = $this->findClientByResourceId($conn->resourceId);
        $this->clients->detach($client);
        
        $this->sendMessageForAll(new EventObject(self::USER_LEFT_US, [ "rid" => $client->resourceId(), "nickname" => $client->nickname() ]), $client);
        
        $this->log("Connection {$client->resourceId()} closed!");
    }

    public function onError(ConnectionInterface $conn, Exception $e) {
        $this->log("Oh no, an error! The system said: bla bla {$e->getMessage()}");
        $conn->close();
    }
    
    protected function sendMessageForAll($eventMessage, $myClient = null)
    {
        $ignoreMyself = null !== $myClient;
        
        foreach ($this->clients as $client) {
            if ($ignoreMyself && $myClient === $client) {
                continue;
            }
            $client->send($eventMessage->encode());
        }
    }
    
    protected function sendMessage($resourceId, $eventMessage)
    {
        $client = $this->findClientByResourceId($resourceId);
        $client->send($eventMessage->encode());
    }

    /**
     * 
     * @param string $resourceId
     * @return null|ConnectionInterface
     */
    protected function findClientByResourceId($resourceId)
    {
        foreach ($this->clients as $client) {
            if ((int)$resourceId === $client->resourceId()) {
                return $client;
            }
        }
        return null;
    }
    
    protected function log($message)
    {
        echo "$message\n";
    }
    
    protected function getConnectedClientsInfo($myResourceId)
    {
        $clientsInfo = [];
        
        foreach ($this->clients as $client) {
            $isCurrentUser = false;
            if ($myResourceId === $client->resourceId()) {
                $isCurrentUser = true;
            }
            
            array_push($clientsInfo, [
                'rid' => $client->resourceId(),
                'nickname' => $client->nickname(),
                'myself' => $isCurrentUser
            ]);
        }
        
        return $clientsInfo;
    }

    protected function getExtraData($conn) {
        return $conn->WebSocket->request->getQuery()->toArray();
    }

    protected function extractNicknameFromExtraData($extraData) {
        $userIx = $this->clients->count() + time();
        
        $nickname = "User {$userIx}";
        if (!empty($extraData['nickname'])) {
            $nickname = $extraData['nickname'];
        }
        
        return $nickname;
    }

}