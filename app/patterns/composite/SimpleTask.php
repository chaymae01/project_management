<?php
namespace App\Patterns\Composite;

use App\Models\Entities\Task as TaskEntity;

class SimpleTask implements TaskComponent
{
    private TaskEntity $task;

    public function __construct(TaskEntity $task)
    {
        $this->task = $task;
    }

    public function getId(): ?int
    {
        return $this->task->id;
    }

    public function getTitle(): string
    {
        return $this->task->title;
    }

    public function getDescription(): string
    {
        return $this->task->description ?? '';
    }

    public function getProgress(): float
    {
        return $this->task->status === 'Done' ? 100.0 : 0.0;
    }

    public function getChildren(): array
    {
        return []; 
    }

    public function addChild(TaskComponent $child): void
    {
        throw new \RuntimeException('Cannot add child to a simple task');
    }

    public function removeChild(TaskComponent $child): void
    {
        throw new \RuntimeException('Cannot remove child from a simple task');
    }

    public function findChild(int $id): ?TaskComponent
    {
        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'type' => 'simple',
            'progress' => $this->getProgress(),
            'status' => $this->task->status,
            'children' => []
        ];
    }

    public function getEntity(): TaskEntity
    {
        return $this->task;
    }
    public function getPriority(): string {
    return $this->entity->priority ?? 'low';
}


}