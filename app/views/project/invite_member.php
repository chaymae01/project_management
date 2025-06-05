<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5 mb-5"> 
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
          <h4 class="mb-0">Invite Member</h4>
        </div>
        <div class="card-body">
          <form action="/projet_java/app/project/inviteMember/<?php echo $projectId; ?>" method="post">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="ex: user@example.com" required>
            </div>

            <div class="mb-3">
              <label for="role" class="form-label">Select Role</label>
              <select class="form-select" id="role" name="role">
                <?php foreach ($roles as $role) : ?>
                  <option value="<?php echo htmlspecialchars($role->name); ?>">
                    <?php echo htmlspecialchars($role->name); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary">Invite</button>
              <a href="/projet_java/app/project/show/<?php echo $projectId; ?>" class="btn btn-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


