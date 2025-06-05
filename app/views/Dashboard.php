<?php
include_once __DIR__ . '/layouts/header.php'; 
use App\Repositories\TaskRepository;
use App\Repositories\ProjectRepository;

$projectRepository = new ProjectRepository();
$taskRepository = new TaskRepository();
$userId = $_SESSION['user_id'] ?? null;

$projects = $projectRepository->getProjectsByUserIdash($userId);
$totalProjects = count($projects);
$totalTasks = 0;
$tasksByStatus = [
    'To Do' => 0,
    'In Progress' => 0,
    'Done' => 0
];

foreach ($projects as $project) {
    $tasks = $taskRepository->getTasksByProjectId($project->id);
    $totalTasks += count($tasks);
    
    foreach ($tasks as $task) {
        if (isset($tasksByStatus[$task->status])) {
            $tasksByStatus[$task->status]++;
        }
    }
}

$recentProjects = array_slice($projects, 0, 5);
$recentTasks = $taskRepository->getRecentTasksByUser($userId, 5);
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/projet_java/app/views/css/dashboard.css">

<div class="dashboard-container">
    <h1>Tableau de bord</h1>
    
    <div class="stats-grid">
        <div class="stat-card stat-projects">
            <h3>Projets actifs</h3>
            <h2><?= $totalProjects ?></h2>
        </div>
        
        <div class="stat-card stat-tasks">
            <h3>Tâches totales</h3>
            <h2><?= $totalTasks ?></h2>
        </div>
        
        <div class="stat-card stat-done">
            <h3>Tâches terminées</h3>
            <h2><?= $tasksByStatus['Done'] ?></h2>
        </div>
    </div>
    
  
    <div class="cards-container">
     
        <div class="card">
            <div class="card-header">
                <h2>Répartition des tâches</h2>
            </div>
            <div class="chart-container">
                <canvas id="taskStatusChart"></canvas>
            </div>
        </div>
        
   
        <div class="card">
            <div class="card-header">
                <h2>Projets récents</h2>
                <a href="/projet_java/app/Projects" class="btn-modern">
                    Voir tout
                </a>
            </div>
            <div class="project-list">
                <?php if (!empty($recentProjects)): ?>
                    <?php foreach ($recentProjects as $project): ?>
                        <div class="project-item">
                            <a href="/projet_java/app/project/show/<?= $project->id ?>" class="project-link">
                                <?= htmlspecialchars($project->name) ?>
                            </a>
                            <span class="badge <?= $project->type === 'kanban' ? 'badge-project-kanban' : 'badge-project-other' ?>">
                                <?= strtoupper($project->type) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">Aucun projet récent</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Tâches récentes</h2>
        </div>
        <div class="table-responsive">
            <?php if (!empty($recentTasks)): ?>
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th>Tâche</th>
                            <th>Projet</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Date limite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTasks as $task): ?>
                            <tr onclick="window.location.href='/projet_java/app/project/show/<?= $task->project_id ?>'">
                                <td>
                                    <a href="/projet_java/app/project/show/<?= $task->project_id ?>" class="task-link">
                                        <?= htmlspecialchars($task->title) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($task->project_name ?? 'Inconnu') ?></td>
                                <td>
                                    <span class="badge
                                        <?= $task->status === 'To Do' ? 'badge-status-todo' : 
                                            ($task->status === 'In Progress' ? 'badge-status-progress' : 'badge-status-done') ?>">
                                        <?= $task->status ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($task->priority): ?>
                                        <span class="badge
                                            <?= $task->priority === 'high' ? 'badge-priority-high' : 
                                                ($task->priority === 'medium' ? 'badge-priority-medium' : 'badge-priority-low') ?>">
                                            <?= ucfirst($task->priority) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $task->deadline ? date('d/m/Y', strtotime($task->deadline)) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">Aucune tâche récente</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  window.tasksByStatus = {
    todo: <?= $tasksByStatus['To Do'] ?>,
    inProgress: <?= $tasksByStatus['In Progress'] ?>,
    done: <?= $tasksByStatus['Done'] ?>
  };
</script>
<script src="/projet_java/app/views/js/dashboard.js"></script>


<?php include_once __DIR__ . '/layouts/footer.php'; ?>