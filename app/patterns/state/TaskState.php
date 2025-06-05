<?php

  namespace App\Patterns\State;

interface TaskState
{
    public function canTransitionTo(TaskState $state): bool;
    public function apply($task): void;
    public function getName(): string;
    public function move($task): void; 
}
    ?>
