<?php

    namespace App\Patterns\Command;

    class CommandInvoker
    {
      private $history = [];

      public function executeCommand(TaskCommand $command)
      {
        $command->execute();
        $this->history[] = $command;
      }

    
    }
    ?>
