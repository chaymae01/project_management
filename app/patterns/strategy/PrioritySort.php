<?php
namespace App\Patterns\Strategy;
use App\Patterns\Composite\ComplexTask;

class PrioritySort implements SortStrategy
{
    public function sort(array $components): array
    {
        $priorityOrder = ['high' => 2,  'low' => 1];
        
        $sorter = function($a, $b) use ($priorityOrder, &$sorter) {
            $cmp = $priorityOrder[$b->getPriority()] <=> $priorityOrder[$a->getPriority()];
            if ($cmp !== 0) return $cmp;
            
            return strcmp($a->getTitle(), $b->getTitle());
        };
        
        return $this->sortRecursive($components, $sorter);
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
}
?>