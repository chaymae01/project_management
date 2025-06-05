<?php

    namespace App\Patterns\Strategy;

    interface SortStrategy
    {
      public function sort(array $tasks): array;
    }
    ?>
