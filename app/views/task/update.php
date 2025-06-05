<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<main class="flex-grow-1">
  <div class="container my-5">
    <div class="card shadow-lg">
      <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Mettre à jour la tâche</h4>
      </div>
      <div class="card-body">
        <form action="/projet_java/app/task/update/<?php echo htmlspecialchars($task->id); ?>" method="post">
          <div class="mb-3">
            <label for="title" class="form-label">Titre :</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="<?php echo htmlspecialchars($task->title); ?>" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description :</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?php 
              echo htmlspecialchars($task->description); ?></textarea>
          </div>

          <div class="mb-3">
            <label for="task_type" class="form-label">Type de tâche :</label>
            <select class="form-select" id="task_type" name="task_type">
              <option value="standard" <?php echo ($task->task_type === 'standard') ? 'selected' : ''; ?>>Tâche standard</option>
              <option value="complex" <?php echo ($task->task_type === 'complex') ? 'selected' : ''; ?>>Tâche complexe</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="deadline" class="form-label">Date limite :</label>
            <input type="date" class="form-control" id="deadline" name="deadline" 
                   value="<?php echo htmlspecialchars($task->deadline); ?>">
          </div>

          <div class="mb-3">
            <label for="assignee_id" class="form-label">Assigner à :</label>
            <select class="form-select" id="assignee_id" name="assignee_id">
              <option value="">Non assignée</option>
              <?php if (isset($projectMembers) && is_array($projectMembers)) : ?>
                <?php foreach ($projectMembers as $member) : ?>
                  <option value="<?php echo htmlspecialchars($member->user_id); ?>" 
                    <?php echo ($task->assignee_id == $member->user_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($member->username); ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="status" class="form-label">Statut :</label>
            <select class="form-select" id="status" name="status">
              <option value="To Do" <?php echo ($task->status === 'To Do') ? 'selected' : ''; ?>>À faire</option>
              <option value="In Progress" <?php echo ($task->status === 'In Progress') ? 'selected' : ''; ?>>En cours</option>
              <option value="Done" <?php echo ($task->status === 'Done') ? 'selected' : ''; ?>>Terminée</option>
            </select>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">Mettre à jour</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

