<?php

namespace App\Chat;

use Exception;

class ReceiverObject
{
    protected $resourceId;
    
    protected $message;
    
    public function __construct($rawData)
    {
        $data = json_decode($rawData, true);
        
        if (empty($data['message'])) {
            throw new Exception("Receiver Exception: Message is empty");
        }
        
        $resourceId = null;
        if (!empty($data['rid']) && $data['rid'] !== "null") {
            $resourceId = $data['rid'];
        }
        
        $this->resourceId = $resourceId;
        $this->message = $data['message'];
    }
    
    public function resourceId() {
        return $this->resourceId;
    }
    
    public function message() {
        return $this->message;
    }
}