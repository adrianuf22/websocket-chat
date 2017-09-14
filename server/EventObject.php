<?php

namespace App\Chat;

class EventObject
{
    protected $eventName;
    
    protected $eventData;

    public function __construct($eventName, $data)
    {
        $this->eventName = $eventName;
        $this->eventData = $data;
    }
    
    public function encode()
    {
        return json_encode($this->toArray());
    }
    
    public function toArray()
    {
        return [
            "event" => $this->eventName,
            "data" => $this->eventData
        ];
    }
}
