```php
    <?php

    namespace App\Patterns\Factory;

    use App\Models\Entities\Task;

    class TaskFactory implements FactoryInterface
    {
      public static function create(
        string $title,
        string $description,
        string $start_date,
        string $end_date,
        int $creator_id,
        string $type
      ): Task {
        return new Task($title, $description, $start_date, $end_date, $creator_id, $type);
      }
    }

    ?>
    
