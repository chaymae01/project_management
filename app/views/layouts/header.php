<?php
require_once __DIR__ . '/../../services/NotificationService.php';

$notificationCount = 0;
$notifications = [];

if (isset($_SESSION['user_id'])) {
    $notificationService = new App\Services\NotificationService();
    $notificationCount = $notificationService->countUnreadNotifications($_SESSION['user_id']);
    $notifications = $notificationService->getUnreadNotifications($_SESSION['user_id'], 5);
}

$currentPath = $_SERVER['REQUEST_URI'];
$hideNavbar = strpos($currentPath, '/app/login') !== false || strpos($currentPath, '/app/register') !== false;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>tskFlow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="/projet_java/assets/css/app.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/projet_java/app/views/css/header.css">



</head>

<body>
<?php if (!$hideNavbar): ?>
<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-4">
    <a class="navbar-brand fs-3 fw-bold text-primary" href="/projet_java/app/Projects">
      Projet<span class="light">Manager</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">

        <?php if (isset($_SESSION['user_id'])) : ?>
          <li class="nav-item">
           <a class="nav-link d-flex align-items-center" href="/projet_java/app/Dashboard">
  Dashboard
</a>

          </li>

          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="/projet_java/app/Projects">
              Mes Projects
            </a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-bell fs-5"></i>
              <?php if ($notificationCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  <?= $notificationCount ?>
                  <span class="visually-hidden">notifications non lues</span>
                </span>
              <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationDropdown" style="min-width: 300px;">
              <li class="d-flex justify-content-between align-items-center mb-2 px-2">
                <strong>Notifications</strong>
                <?php if ($notificationCount > 0): ?>
                  <a href="/projet_java/app/notifications/mark-all-read" class="small text-primary mark-all-read">Tout marquer comme lu</a>
                <?php endif; ?>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <div class="notification-body">
                  <?php if (empty($notifications)): ?>
                    <div class="text-muted text-center py-2">Aucune notification</div>
                  <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                      <a href="/projet_java/app/notifications/view/<?= $notification->id ?>"
                         class="dropdown-item <?= $notification->is_read ? '' : 'fw-bold' ?>"
                         data-notification-id="<?= $notification->id ?>">
                        <div class="d-flex align-items-center">
                          <div class="me-2">
                            <?php if ($notification->related_entity_type === 'task'): ?>
                              <i class="fas fa-tasks text-primary"></i>
                            <?php elseif ($notification->related_entity_type === 'project'): ?>
                              <i class="fas fa-project-diagram text-info"></i>
                            <?php else: ?>
                              <i class="fas fa-info-circle text-secondary"></i>
                            <?php endif; ?>
                          </div>
                          <div>
                            <div><?= htmlspecialchars($notification->message) ?></div>
                            <small class="text-muted"><?= date('M j, g:i a', strtotime($notification->created_at)) ?></small>
                          </div>
                        </div>
                      </a>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li class="text-center">
                <a href="/projet_java/app/notifications" class="text-primary small">Voir toutes les notifications</a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link text-danger d-flex align-items-center" href="/projet_java/app/logout">
              <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
            </a>
          </li>

        <?php else : ?>
          <li class="nav-item"><a class="nav-link" href="/projet_java/app/login">Connexion</a></li>
          <li class="nav-item"><a class="nav-link" href="/projet_java/app/register">Inscription</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </nav>
</header>
<?php endif; ?>
<main>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="/projet_java/app/views/js/js_headr.js"></script>
