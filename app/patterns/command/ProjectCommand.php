<?php
namespace App\Patterns\Command;

use App\Patterns\Command\TaskCommand;

interface ProjectCommand extends TaskCommand
{
 
    public function execute(): array; 

  
}

