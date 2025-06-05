<?php
// Détermination du message selon l'heure actuelle
date_default_timezone_set('Europe/Paris');
$hour = (int)date('H');

if ($hour >= 5 && $hour < 12) {
    $greeting = "Bonjour";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Bon après-midi";
} elseif ($hour >= 18 && $hour < 22) {
    $greeting = "Bonsoir";
} else {
    $greeting = "Bienvenue";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bienvenue - TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/projet_java/app/views/css/welcome.css" />

</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white">
  <div class="w-100 d-flex justify-content-between align-items-center px-4">
  <a class="navbar-brand fs-3 fw-bold" href="/projet_java/app/Projects">
    <span class="text-primary">Projet</span><span class="text-dark">Manager</span>
  </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-center">
        <li class="nav-item mx-2">
          <a class="nav-link text-primary fw-semibold" href="/features">Accueil</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link text-primary fw-semibold" href="/about">À propos</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link text-primary fw-semibold" href="/contact">Contact</a>
        </li>
        <li class="nav-item mx-2">
          <a href="/projet_java/app/login" class="btn btn-light btn-cta">Se connecter</a>
        </li>
        <li class="nav-item mx-2">
          <a href="/projet_java/app/register" class="btn btn-light btn-cta">Créer un compte</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="spacer"></div>

<section class="hero animate__animated animate__fadeIn">
    <div class="container">
        <h1 class="animate__animated animate__fadeInUp"><?= $greeting ?>, gérez vos projets avec <span class="text-warning">ProjetManager</span></h1>
        <p class="animate__animated animate__fadeInUp animate__delay-1s">La plateforme collaborative ultime pour booster votre productivité et simplifier votre workflow.</p>
        <div class="animate__animated animate__fadeInUp animate__delay-2s mt-4">
           <a href="/projet_java/app/login" class="btn btn-light btn-cta me-3">Se connecter</a>
           <a href="/projet_java/app/register" class="btn btn-outline-light btn-cta"><i class="fas fa-user-plus me-2"></i>Créer un compte</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title animate__animated animate__fadeIn mb-5 text-center">Pourquoi choisir TaskFlow ?</h2>
        <div class="row g-4">
            <div class="col-md-4 animate__animated animate__fadeInUp">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5>Suivi des tâches intelligent</h5>
                    <p>Créez, assignez et suivez l'avancement des tâches en temps réel avec des notifications intelligentes.</p>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5>Collaboration transparente</h5>
                    <p>Travaillez ensemble efficacement avec des espaces de discussion dédiés et le partage de fichiers.</p>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-columns"></i>
                    </div>
                    <h5>Vues personnalisables</h5>
                    <p>Passez du Kanban aux listes ou calendrier selon vos préférences et besoins du moment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 stat-item">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Utilisateurs satisfaits</div>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <div class="stat-number">95%</div>
                <div class="stat-label">Augmentation de productivité</div>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support disponible</div>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Intégrations disponibles</div>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5">
                <h4>TaskFlow</h4>
                <p>La solution tout-en-un pour la gestion de projets et la collaboration d'équipe.</p>
                <div class="social-links mt-4">
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-5">
                <h5>Navigation</h5>
                <a href="/features">Fonctionnalités</a><br />
                <a href="/pricing">Tarifs</a><br />
                <a href="/blog">Blog</a><br />
                <a href="/about">À propos</a>
            </div>
            <div class="col-lg-2 col-md-6 mb-5">
                <h5>Support</h5>
                <a href="/help">Centre d'aide</a><br />
                <a href="/contact">Contact</a><br />
                <a href="/faq">FAQ</a><br />
                <a href="/status">Statut</a>
            </div>
            <div class="col-lg-4 mb-5">
                <h5>Newsletter</h5>
                <p>Abonnez-vous pour recevoir nos dernières actualités et offres.</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Votre email" />
                    <button class="btn btn-primary" type="button">S'abonner</button>
                </div>
            </div>
        </div>
        <hr class="my-4 bg-secondary" />
        <div class="text-center py-3">
            &copy; <?= date("Y") ?> TaskFlow. Tous droits réservés. | 
            <a href="/privacy" class="text-white-50">Confidentialité</a> | 
            <a href="/terms" class="text-white-50">Conditions</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
