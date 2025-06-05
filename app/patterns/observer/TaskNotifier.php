<?php

namespace App\Patterns\Observer;

class TaskNotifier
{
    private $observers = [];

    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer)
    {
        $this->observers = array_filter($this->observers, function ($a) use ($observer) {
            return ($a !== $observer);
        });
    }

    public function notify(string $eventType, array $data)
    {
        foreach ($this->observers as $observer) {
            try {
                $observer->update($eventType, $data);
            } catch (\Exception $e) {
                error_log("Observer update failed: " . get_class($observer) . " - " . $e->getMessage());
            }
        }
    }
}
