<?php

namespace App\Chat;

use Ratchet\ConnectionInterface;

class Client
{
    protected $conn;
    
    protected $resourceId;
    
    protected  $nickname;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
        $this->resourceId = $conn->resourceId;
    }
    
    public function send($data)
    {
        $this->conn->send($data);
    }
    
    public function resourceId()
    {
        return $this->resourceId;
    }
    
    public function nickname()
    {
        return $this->nickname;
    }
    
    public function setNickname($nickname) 
    {
        $this->nickname = $nickname;
    }
}
