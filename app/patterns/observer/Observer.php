<?php

namespace App\Patterns\Observer;

interface Observer
{
  
    public function update(string $eventType, $data);
}
