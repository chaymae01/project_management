<?php

use App\Controllers\AuthController;
use App\Controllers\ProjectController;
use App\Controllers\TaskController;
use App\Services\TaskService;
use App\Repositories\TaskRepository;
use App\Services\ProjectService;
use App\Repositories\ProjectRepository;
use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Services\TaskCompositeService;
use App\Patterns\Factory\TaskFactoryProvider;
use App\Patterns\Command\CommandInvoker;
use App\Controllers\NotificationController;

$notificationController = new NotificationController();

$authController = new AuthController(
    new AuthService(new UserRepository()), 
    new ProjectService(new ProjectRepository())
);

$commandInvoker = new App\Patterns\Command\CommandInvoker();

$projectController = new ProjectController(
    new ProjectService(new ProjectRepository()), 
    new TaskService(new TaskRepository()),
    new TaskCompositeService(new TaskRepository()),
    $commandInvoker
);

$taskService = new TaskService(new TaskRepository());
$projectRepository = new ProjectRepository();

$taskController = new TaskController(
    $taskService,
    new TaskCompositeService(new TaskRepository()),
    new TaskRepository(),
    new TaskFactoryProvider(),
    $commandInvoker,
    $projectRepository
);

return [
    '/index.php' => function () {
        include __DIR__ . '/../views/welcome.php';
    },
    '/login' => [$authController, 'login'],
    '/register' => [$authController, 'register'],
    '/logout' => [$authController, 'logout'],
    '/Projects' => function () {
        include __DIR__ . '/../views/Projects.php';
    },
    '/Dashboard' => function () {
        include __DIR__ . '/../views/Dashboard.php';
    },
    '/project/create' => [$projectController, 'create'],
    '/project/show/{id:\d+}' => [$projectController, 'show'],
    '/project/inviteMember/{projectId:\d+}' => [$projectController, 'inviteMember'],
    '/task/create' => [$taskController, 'create'],
    '/task/view/{id:\d+}' => [$taskController, 'view'],
    '/task/addSubtask/{parentId:\d+}' => [$taskController, 'addSubtask'],
    '/acceptInvite' => [$authController, 'acceptInvite'],
    '/project/edit/{id:\d+}' => [$projectController, 'edit'],
    '/project/delete/{id:\d+}' => [$projectController, 'delete'],
    '/task/update/{id:\d+}' => [$taskController, 'update'],
    '/task/delete/{id:\d+}' => [$taskController, 'delete'],
    '/task/move/{id:\d+}' => [$taskController, 'move'],
    '/notifications' => [$notificationController, 'index'],
    '/notifications/view/{id:\d+}' => [$notificationController, 'view'],
    '/notifications/mark-all-read' => [$notificationController, 'markAllRead'],
    '/notifications/count' => [$notificationController, 'count'],
];