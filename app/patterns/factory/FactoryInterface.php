```php
    <?php

    namespace App\Patterns\Factory;

    interface FactoryInterface
    {
      public static function create(string $name, string $description, string $start_date, string $end_date, int $creator_id, string $type);
    }
    ?>
  
