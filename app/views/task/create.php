<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
  <div class="card shadow-lg">
<div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top-4">
      <h4 class="mb-0">Créer une tâche</h4>
    </div>
    <div class="card-body">
      <form action="/projet_java/app/task/create" method="post">
        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($_GET['project_id'] ?? ''); ?>">

        <?php if (isset($_GET['parent_id'])) : ?>
          <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($_GET['parent_id']); ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label for="title" class="form-label">Titre :</label>
          <input type="text" class="form-control" id="title" name="title" placeholder="Ex : Implémenter le backend" required>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Description :</label>
          <textarea class="form-control" id="description" name="description" rows="4" placeholder="Décrivez la tâche..."></textarea>
        </div>

        <div class="mb-3">
          <label for="task_type" class="form-label">Type de tâche :</label>
          <select class="form-select" id="task_type" name="task_type">
            <option value="standard">Tâche standard</option>
            <option value="complex">Tâche complexe</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="deadline" class="form-label">Date limite :</label>
          <input type="date" class="form-control" id="deadline" name="deadline">
        </div>

        <div class="mb-4">
          <label for="assignee_id" class="form-label">Assigner à :</label>
          <select class="form-select" id="assignee_id" name="assignee_id">
            <option value="">Non assignée</option>
            <?php if (isset($projectMembers) && is_array($projectMembers)) : ?>
              <?php foreach ($projectMembers as $member) : ?>
                <option value="<?php echo htmlspecialchars($member->user_id); ?>">
                  <?php echo htmlspecialchars($member->username); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success">Créer la tâche</button>
        </div>
      </form>
    </div>
  </div>
</div>

