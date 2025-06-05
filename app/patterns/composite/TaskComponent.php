<?php
namespace App\Patterns\Composite;

interface TaskComponent
{
    public function getId(): ?int;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getProgress(): float;
    public function getChildren(): array;
    public function addChild(TaskComponent $child): void;
    public function removeChild(TaskComponent $child): void;
    public function findChild(int $id): ?TaskComponent;
    public function toArray(): array;
 

   public function getPriority(): string ;
}