<?php
namespace App\Patterns\Composite;

use App\Models\Entities\Task as TaskEntity;

class ComplexTask implements TaskComponent
{
    private TaskEntity $task;
    private array $children = [];

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
        if (empty($this->children)) {
            return $this->task->status === 'Done' ? 100.0 : 0.0;
        }

        $total = 0.0;
        foreach ($this->children as $child) {
            $total += $child->getProgress();
        }

        return $total / count($this->children);
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(TaskComponent $child): void
    {
        $this->children[] = $child;
    }

    public function removeChild(TaskComponent $child): void
    {
        $this->children = array_filter($this->children, fn($c) => $c->getId() !== $child->getId());
    }

    public function findChild(int $id): ?TaskComponent
    {
        if ($this->getId() === $id) {
            return $this;
        }

        foreach ($this->children as $child) {
            $found = $child->findChild($id);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'type' => 'complex',
            'progress' => $this->getProgress(),
            'status' => $this->task->status,
            'children' => array_map(fn($child) => $child->toArray(), $this->children)
        ];
    }

    public function getEntity(): TaskEntity
    {
        return $this->task;
    }
   
public function getPriority(): string {
    return $this->entity->priority ?? 'high';
}
}