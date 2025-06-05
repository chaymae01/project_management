<?php

namespace App\Patterns\Observer;

class LogObserver implements Observer
{
  
    public function update(string $eventType, $data)
    {
        error_log("Event: " . $eventType . " - Data: " . print_r($data, true));
    }
}
