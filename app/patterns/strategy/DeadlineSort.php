<?php
namespace App\Patterns\Strategy;
use App\Patterns\Composite\ComplexTask;

class DeadlineSort implements SortStrategy
{
   public function sort(array $components): array
    {
        return $this->sortRecursive($components, function($a, $b) {
            $deadlineA = $a->getEntity()->deadline;
            $deadlineB = $b->getEntity()->deadline;
            
            if ($deadlineA === $deadlineB) return 0;
            if ($deadlineA === null) return 1;
            if ($deadlineB === null) return -1;
            
            return strtotime($deadlineA) <=> strtotime($deadlineB);
        });
    }
     private function sortRecursive(array $components, callable $sorter): array
    {
        usort($components, $sorter);
        
        foreach ($components as $component) {
            if ($component instanceof ComplexTask) {
                $sortedChildren = $this->sortRecursive(
                    $component->getChildren(),
                    $sorter
                );
                foreach ($sortedChildren as $child) {
                    $component->removeChild($child);
                    $component->addChild($child);
                }
            }
        }
        
        return $components;
    }

   private function getTaskDeadline($task): ?string
{
    if (property_exists($task, 'deadline') && $task->deadline !== null) {
        return $task->deadline;
    }
    
    if (property_exists($task, 'end_date') && $task->end_date !== null) {
        return $task->end_date;
    }
    
    if (property_exists($task, 'created_at') && $task->created_at !== null) {
        return $task->created_at;
    }
    
    return null;
}

}
?>
