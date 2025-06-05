```php
    <?php

    namespace App\Patterns\Factory;

    use App\Models\Entities\Project;

    class ProjectFactory implements FactoryInterface
    {
      public static function create(string $name, string $description, string $start_date, string $end_date, int $creator_id, string $type = 'kanban'): Project
      {
        return new Project($name, $description, $start_date, $end_date, $creator_id, $type);
      }
    }
    ?>
   
