<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="card shadow rounded-4">
<div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top-4">
            <h3 class="mb-0">Détails de la tâche</h3>
            <a href="/projet_java/app/project/show/<?= $project->id ?>" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour au tableau
            </a>
        </div>

        <div class="card-body p-4 bg-body-tertiary">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <h2 class="fw-semibold text-dark"><?= htmlspecialchars($task->title) ?></h2>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($task->description)) ?></p>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light p-3 rounded-3">
                        <div class="mb-2 d-flex align-items-center">
                            <strong class="me-2 text-secondary">Statut:</strong>
                            <span class="badge bg-<?= $task->status === 'To Do' ? 'info' : ($task->status === 'In Progress' ? 'warning' : 'success') ?>">
                                <?= $task->status ?>
                            </span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <strong class="me-2 text-secondary">Priorité:</strong>
                            <span class="badge bg-<?= strtolower($task->priority) === 'high' ? 'danger' : (strtolower($task->priority) === 'medium' ? 'warning' : 'success') ?>">
                                <?= $task->priority ?>
                            </span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <strong class="me-2 text-secondary">Type:</strong>
                            <span class="badge bg-secondary"><?= $task->task_type ?></span>
                        </div>
                        <?php if ($task->deadline): ?>
                        <div class="d-flex align-items-center">
                            <strong class="me-2 text-secondary">Deadline:</strong>
                            <span class="text-<?= strtotime($task->deadline) < time() ? 'danger' : 'dark' ?>">
                                <?= date('d/m/Y', strtotime($task->deadline)) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Informations</h5>
                        </div>
                        <div class="card-body">
                            <p><i class="fas fa-project-diagram me-2 text-secondary"></i><strong>Projet :</strong> 
                                <a href="/projet_java/app/project/show/<?= $project->id ?>">
                                    <?= htmlspecialchars($project->name) ?>
                                </a>
                            </p>
                            <p><i class="fas fa-user-plus me-2 text-secondary"></i><strong>Créée par :</strong> <?= htmlspecialchars($creator->username) ?></p>
                            <?php if ($assignee): ?>
                            <p><i class="fas fa-user-check me-2 text-secondary"></i><strong>Assignée à :</strong> <?= htmlspecialchars($assignee->username) ?></p>
                            <?php endif; ?>
                            <?php if ($parentTask): ?>
                            <p><i class="fas fa-level-up-alt me-2 text-secondary"></i><strong>Sous-tâche de :</strong> 
                                <a href="/projet_java/app/task/view/<?= $parentTask->id ?>">
                                    <?= htmlspecialchars($parentTask->title) ?>
                                </a>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Actions</h5>
                            <div class="btn-group gap-2">
                                <a href="/projet_java/app/task/update/<?= $task->id ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                                <form action="/projet_java/app/task/delete/<?= $task->id ?>" method="post" class="d-inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($task->status !== 'Done'): ?>
                            <form action="/projet_java/app/task/move/<?= $task->id ?>" method="post" class="mb-3">
                                <label class="form-label">Changer le statut :</label>
                                <select name="status" class="form-select mb-2">
                                    <option value="To Do" <?= $task->status === 'To Do' ? 'selected' : '' ?>>To Do</option>
                                    <option value="In Progress" <?= $task->status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Done" <?= $task->status === 'Done' ? 'selected' : '' ?>>Done</option>
                                </select>
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i> Mettre à jour
                                </button>
                            </form>
                            <?php endif; ?>

                            <?php if ($task->task_type === 'complex'): ?>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Sous-tâches</h6>
                                <a href="/projet_java/app/task/create?project_id=<?= $task->project_id ?>&parent_id=<?= $task->id ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </a>
                            </div>

                            <?php if (!empty($subtasks)): ?>
                                <ul class="list-group mt-3">
                                    <?php foreach ($subtasks as $subtask): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="/projet_java/app/task/view/<?= $subtask->id ?>">
                                            <?= htmlspecialchars($subtask->title) ?>
                                        </a>
                                        <span class="badge bg-<?= $subtask->status === 'To Do' ? 'info' : ($subtask->status === 'In Progress' ? 'warning' : 'success') ?>">
                                            <?= $subtask->status ?>
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-info mt-3">Aucune sous-tâche</div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


