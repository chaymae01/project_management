<?php include_once __DIR__ . '/../layouts/header.php'; ?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="container-fluid kanban-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="kanban-title">Kanban Board</h1>
            <div class="btn-group">
                <a href="/projet_java/app/project/show/<?= $project->id ?>?sort=priority" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sort-amount-down-alt"></i> Trier par Priorité
                </a>
                <a href="/projet_java/app/project/show/<?= $project->id ?>?sort=deadline" class="btn btn-sm btn-outline-secondary">
                    <i class="far fa-calendar-alt"></i> Trier par Deadline
                </a>
            </div>
        </div>
        <div>
            <a href="/projet_java/app/project/inviteMember/<?php echo $project->id; ?>" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Inviter un membre
            </a>
        </div>
    </div>

    <div class="project-header mb-4 p-4 rounded shadow-sm">
        <h2 class="project-name"><?php echo htmlspecialchars($project->name); ?></h2>
        <p class="project-description"><?php echo htmlspecialchars($project->description); ?></p>
        <div class="project-meta d-flex flex-wrap gap-3">
            <div class="meta-item">
                <i class="far fa-calendar-start"></i>
                <span>Début: <?php echo htmlspecialchars($project->start_date); ?></span>
            </div>
            <div class="meta-item">
                <i class="far fa-calendar-end"></i>
                <span>Fin: <?php echo htmlspecialchars($project->end_date); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-tag"></i>
                <span>Type: <?php echo htmlspecialchars($project->type); ?></span>
            </div>
        </div>
    </div>

    <div class="kanban-board">
        <?php foreach (['To Do', 'In Progress', 'Done'] as $column) : ?>
            <div class="kanban-column" data-status="<?php echo htmlspecialchars($column); ?>">
                <div class="column-header">
                    <h3><?php echo $column; ?></h3>
                    <span class="task-counter badge bg-secondary">0 tâches</span>
                </div>
                <div class="kanban-task-container">
                    <?php if (!empty($tasks[$column])) : ?>
                        <?php
                        if (!function_exists('displayTask')) {
                            function displayTask($task, $compositeService, $project) {
                        ?>
                                <div class="kanban-task <?php echo htmlspecialchars($task->getEntity()->task_type); ?>" 
                                     draggable="true" 
                                     data-task-id="<?php echo $task->getId(); ?>"
                                     data-status="<?php echo htmlspecialchars($task->getEntity()->status); ?>">
                                    <div class="task-header">
                                        <div class="kanban-task-title"><?php echo htmlspecialchars($task->getTitle()); ?></div>
                                        <div class="task-actions">
                                            <a href="/projet_java/app/task/update/<?php echo $task->getId(); ?>" 
                                               title="Modifier" 
                                               class="text-primary me-2">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                             <a href="/projet_java/app/task/view/<?php echo $task->getId(); ?>" 
       title="Voir les détails" 
       class="text-info me-2">
        <i class="fas fa-eye"></i>
    </a>
                                            <form action="/projet_java/app/task/delete/<?php echo $task->getId(); ?>" method="post" style="display: inline;">
                                                <button type="submit" class="text-danger btn btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche?')" style="border:none; background:none; padding:0; cursor: pointer;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="task-meta">
                                        <span class="priority <?= strtolower($task->getPriority()) ?>">
                                            <i class="fas fa-exclamation-circle"></i> <?= $task->getPriority() ?>
                                        </span>
                                        <span class="task-type <?php echo htmlspecialchars($task->getEntity()->task_type); ?>">
                                            <?php echo htmlspecialchars($task->getEntity()->task_type); ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($task instanceof \App\Patterns\Composite\ComplexTask) : ?>
                                        <div class="complex-task-info mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="complex-task-indicator">
                                                    <i class="fas fa-tasks"></i>
                                                    <?php echo $compositeService->getSubtaskCount($task->getId()); ?> sous-tâches
                                                </span>
                                                <a href="/projet_java/app/task/create?project_id=<?php echo $project->id; ?>&parent_id=<?php echo $task->getId(); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Ajouter
                                                </a>
                                            </div>
                                            <div class="progress mt-2">
                                                <div class="progress-bar" 
                                                     style="width: <?php echo $task->getProgress(); ?>%">
                                                    <?php echo round($task->getProgress()); ?>%
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($task instanceof \App\Patterns\Composite\ComplexTask && !empty($task->getChildren())) : ?>
                                        <div class="nested-tasks mt-2">
                                            <?php foreach ($task->getChildren() as $subtask) : ?>
                                                <?php displayTask($subtask, $compositeService, $project); ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                        <?php
                            }
                        }

                        foreach ($tasks[$column] as $task) {
                            displayTask($task, $this->compositeService, $project);
                        }
                        ?>
                    <?php else : ?>
                        <div class="no-tasks">
                            <i class="far fa-check-circle"></i>
                            <p>Aucune tâche dans <?php echo $column; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($column === 'To Do') : ?>
                    <a href="/projet_java/app/task/create?project_id=<?php echo $project->id; ?>" 
                       class="btn btn-primary add-task-btn">
                        <i class="fas fa-plus"></i> Ajouter une tâche
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<link rel="stylesheet" href="/projet_java/app/views/css/show.css">

<script src="/projet_java/app/views/js/show.js"></script>