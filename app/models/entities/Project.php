<?php

    namespace App\Models\Entities;

    class Project implements ProductInterface
    {
      public $id;
      public $name;
      public $description;
      public $start_date;
      public $end_date;
      public $type;
      public $creator_id;
      public $board;

      public function __construct(string $name, string $description, string $start_date, string $end_date, int $creator_id, string $type = 'kanban')
      {
        $this->name = $name;
        $this->description = $description;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->creator_id = $creator_id;
        $this->type = $type;
      }

      public function getId() { return $this->id; }
      public function setId($id) { $this->id = $id; }
      public function getName() { return $this->name; }
      public function setName($name) { $this->name = $name; }
      public function getDescription() { return $this->description; }
      public function setDescription($description) { $this->description = $description; }
      public function getStartDate() { return $this->start_date; }
      public function setStartDate($start_date) { $this->start_date = $start_date; }
      public function getEndDate() { return $this->end_date; }
      public function setEndDate($end_date) { $this->end_date = $end_date; }
      public function getType() { return $this->type; }
      public function setType($type) { $this->type = $type; }
      public function getCreatorId() { return $this->creator_id; }
      public function setCreatorId($creator_id) { $this->creator_id = $creator_id; }
      public function getBoard() { return $this->board; }
      public function setBoard(array $board): void
{
    if ($this->type === 'scrum') {
        $this->board = $board;
    } else {
        $simpleBoard = [];
        foreach ($board as $colName => $colConfig) {
            $simpleBoard[$colName] = $colConfig;
        }
        $this->board = $simpleBoard;
    }
}
    }
    ?>
m