<?php

namespace App\Models\Entities;

use App\Patterns\State\TaskState;

class Task implements ProductInterface
{
  public $id;
  public $title;
  public $description;
  public $status;
  public $priority;
  public $position;
  public $project_id;
  public $creator_id;
  public $assignee_id;
  public $parent_id;
  public $task_type; 
  private $state;
  

  public function __construct(string $title, string $description, int $project_id, int $creator_id)
  {
    $this->title = $title;
    $this->description = $description;
    $this->project_id = $project_id;
    $this->creator_id = $creator_id;
  }

  public function setState(TaskState $state)
  {
    $this->state = $state;
  }

  public function getState(): TaskState
  {
    return $this->state;
  }

   public function move(): void
    {
        if ($this->state === null) {
            throw new \RuntimeException("State is not initialized");
        }
        $this->state->move($this);
    } public function moveToState(TaskState $newState): void
    {
        if ($this->state === null) {
            $this->setState($newState);
        }
        
        if ($this->state->canTransitionTo($newState)) {
            $this->state = $newState;
            $this->state->move($this);
        } else {
            throw new \RuntimeException("Invalid state transition");
        }
    }

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }
  public function getTitle() { return $this->title; }
  public function setTitle($title) { $this->title = $title; }
  public function getDescription() { return $this->description; }
  public function setDescription($description) { $this->description = $description; }
  public function getStatus() { return $this->status; }
 public function setStatus($status)
{
    $allowed = ['To Do', 'In Progress', 'Done'];
    if (!in_array($status, $allowed)) {
        throw new \InvalidArgumentException("Statut invalide");
    }
    $this->status = $status;
}
  public function getPriority() { return $this->priority; }
  public function setPriority($priority) { $this->priority = $priority; }
  public function getPosition() { return $this->position; }
  public function setPosition($position) { $this->position = $position; }
  public function getProjectId() { return $this->project_id; }
  public function setProjectId($project_id) { $this->project_id = $project_id; }
  public function getCreatorId() { return $this->creator_id; }
  public function setCreatorId($creator_id) { $this->creator_id = $creator_id; }
  public function getAssigneeId() { return $this->assignee_id; }
  public function setAssigneeId($assignee_id) { $this->assignee_id = $assignee_id; }
  public function getParentId() { return $this->parent_id; }
  public function setParentId($parent_id) { $this->parent_id = $parent_id; }
  public function getTaskType() { return $this->task_type; }
  public function setTaskType($task_type) { $this->task_type = $task_type; }
 public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'creator_id' => $this->creator_id,
            'assignee_id' => $this->assignee_id,
            'parent_id' => $this->parent_id,
            'task_type' => $this->task_type,
            'deadline' => $this->deadline
        ];
    }
public ?string $deadline = null;

public function getDeadline(): ?string {
    return $this->deadline;
}

public function setDeadline(?string $deadline): void {
    $this->deadline = $deadline;
}



}
