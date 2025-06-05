<?php include_once __DIR__ . '/layouts/header.php'; ?>


<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<link rel="stylesheet" href="/projet_java/app/views/css/projets.css">

<div class="projects-container">
    <div class="page-header">
        <h1 class="page-title">Mes Projets</h1>
        <p class="welcome-text">Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></strong> !</p>
    </div>

    <div class="action-bar">
        <div></div> 
        <a href="/projet_java/app/project/create" class="btn btn-primary">
            <i class='bx bx-plus-circle'></i> Nouveau Projet
        </a>
    </div>

    <?php
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo "<div class='empty-state'>
                <div class='empty-state-icon'><i class='bx bx-lock-alt'></i></div>
                <h3>Accès non autorisé</h3>
                <p class='empty-state-text'>Veuillez vous connecter pour voir vos projets.</p>
              </div>";
        $projects = [];
    } else {
        try {
            $projectRepository = new App\Repositories\ProjectRepository();
            $projects = $projectRepository->getProjectsByUserId($userId);
        } catch (Exception $e) {
            echo "<div class='empty-state'>
                    <div class='empty-state-icon'><i class='bx bx-error-circle'></i></div>
                    <h3>Erreur de chargement</h3>
                    <p class='empty-state-text'>Impossible de charger les projets. Veuillez réessayer plus tard.</p>
                  </div>";
            $projects = [];
        }
    }
    ?>

    <?php if ($projects) : ?>
        <div class="projects-grid">
            <?php foreach ($projects as $project) : ?>
                <div class="project-card">
                    <div class="project-card-header">
                        <h3 class="project-card-title"><?php echo htmlspecialchars($project->name); ?></h3>
                    </div>
                    
                    <div class="project-card-body">
                        <p class="project-card-description"><?php echo htmlspecialchars($project->description ?: 'Aucune description fournie'); ?></p>
                        
                        <div class="project-meta">
                            <div class="meta-item">
                                <span class="meta-label">Date de début</span>
                                <span class="meta-value"><?php echo htmlspecialchars($project->start_date); ?></span>
                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label">Date de fin</span>
                                <span class="meta-value"><?php echo htmlspecialchars($project->end_date); ?></span>
                            </div>
                            
                          
                            
                            <div class="meta-item">
                                <span class="meta-label">Rôle</span>
                                <span class="meta-value"><?php echo htmlspecialchars($project->role_name); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="project-card-footer">
                        <div class="btn-group">
                            <a href="/projet_java/app/project/show/<?php echo $project->id; ?>" class="btn btn-outline btn-view btn-sm">
                                <i class='bx bx-show'></i> Voir
                            </a>
                            <a href="/projet_java/app/project/inviteMember/<?php echo $project->id; ?>" class="btn btn-outline btn-invite btn-sm">
                                <i class='bx bx-user-plus'></i> Inviter
                            </a>
                            <a href="/projet_java/app/project/edit/<?php echo $project->id; ?>" class="btn btn-outline btn-edit btn-sm">
                                <i class='bx bx-edit'></i> Modifier
                            </a>
                            <a href="/projet_java/app/project/delete/<?php echo $project->id; ?>" class="btn btn-outline btn-delete btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                <i class='bx bx-trash'></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class='bx bx-folder-open'></i></div>
            <h3>Aucun projet trouvé</h3>
            <p class="empty-state-text">Vous n'avez aucun projet pour le moment. Créez-en un pour commencer.</p>
            <a href="/projet_java/app/project/create" class="btn btn-primary">
                <i class='bx bx-plus-circle'></i> Créer un projet
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/layouts/footer.php'; ?>