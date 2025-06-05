<?php
namespace App\Patterns\State;

class TaskStateContext
{
    private $task;
    private $currentState;

    public function __construct($task)
    {
        $this->task = $task;
        $this->setStateBasedOnStatus();
    }

    private function setStateBasedOnStatus()
    {
        switch ($this->task->status) {
            case 'To Do':
                $this->currentState = new TodoState();
                break;
            case 'In Progress':
                $this->currentState = new InProgressState();
                break;
            case 'Done':
                $this->currentState = new DoneState();
                break;
            default:
                throw new \Exception("Statut inconnu");
        }
    }

   public function transitionTo(TaskState $state): bool
{
  
    $this->currentState = $state;
    $this->currentState->apply($this->task);
    return true;
}

    public function getCurrentStatus(): string
    {
        return $this->currentState->getName();
    }

    public function transitionToTodo(): bool
    {
        return $this->transitionTo(new TodoState());
    }

    public function transitionToInProgress(): bool
    {
        return $this->transitionTo(new InProgressState());
    }

    public function transitionToDone(): bool
    {
        return $this->transitionTo(new DoneState());
    }
}
?>
