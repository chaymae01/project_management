<?php

    namespace App\Patterns\Factory;

    use App\Models\Entities\Project;

    interface IProjectFactory
    {
      public function createProject(string $name, string $description, string $start_date, string $end_date, int $creator_id): Project;
    }
    ?>
  
