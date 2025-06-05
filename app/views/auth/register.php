<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<style>
  body {
    background: linear-gradient(135deg,rgb(74, 75, 91) 0%,rgb(184, 199, 225) 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
  }

  .form-container {
    background: #fff;
    padding: 3rem 4rem;
    max-width: 500px; /* Largeur fixe mais raisonnable */
    width: 100%; /* Responsive */
    border-radius: 20px; /* Angles arrondis */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
  }

  .card-title {
    font-weight: 600;
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: #212529;
    text-align: center;
  }

  .btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    border-radius: 8px;
    padding: 0.6rem;
    font-weight: 600;
  }

  .btn-primary:hover {
    background-color: #084cd8;
    border-color: #084cd8;
  }

  .btn-link {
    color: #0d6efd;
    font-weight: 500;
    text-align: center;
    display: block;
    margin-top: 1rem;
  }

  .btn-link:hover {
    color: #084cd8;
    text-decoration: underline;
  }

  /* Input styles arrondis */
  .form-control {
    border-radius: 10px;
    padding: 0.5rem 1rem;
  }

  /* Alert styles centrées */
  .alert {
    text-align: center;
  }
</style>

<div class="form-container">
  <h3 class="card-title">Inscription</h3>

  <?php if (isset($error)) : ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form action="/projet_java/app/register" method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Nom d'utilisateur</label>
      <input 
        type="text" 
        class="form-control" 
        id="username" 
        name="username" 
        required 
        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
      >
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Adresse e-mail</label>
      <input 
        type="email" 
        class="form-control" 
        id="email" 
        name="email" 
        required 
        value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '') ?>"
      >
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Mot de passe</label>
      <input 
        type="password" 
        class="form-control" 
        id="password" 
        name="password" 
        required
      >
    </div>

    <input 
      type="hidden" 
      name="token" 
      value="<?= isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '' ?>"
    >

    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-primary">S'inscrire</button>
    </div>

    <a href="/projet_java/app/login" class="btn btn-link">Se connecter</a>
  </form>
</div>
