<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm border-0">
<div class="card-header bg-info text-white d-flex justify-content-between align-items-center rounded-top-4">
          <h4 class="mb-0">Create a New Project</h4>
        </div>
        <div class="card-body">
          <form action="/projet_java/app/project/create" method="post">
            <div class="mb-3">
              <label for="name" class="form-label">Project Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date">
              </div>
            </div>

            <div class="mb-3">
              <label for="type" class="form-label">Project Type</label>
              <select class="form-select" id="type" name="type">
                <option value="kanban">Kanban</option>
              </select>
            </div>

          

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Create Project</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


