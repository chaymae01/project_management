<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<style>
  body {
    background: linear-gradient(135deg,rgb(90, 91, 74) 0%,rgb(184, 199, 225) 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
  }

  .login-container {
    background: #fff;
    padding: 2.5rem 3rem;
    border-radius: 1rem;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
  }

  .login-container h3 {
    margin-bottom: 1.5rem;
    color: #333;
    font-weight: 700;
  }

  .form-label {
    display: block;
    text-align: left;
    font-weight: 600;
    margin-bottom: 0.4rem;
    color: #444;
  }

  .form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 1.2rem;
    border-radius: 0.5rem;
    border: 1.5px solid #ddd;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  .form-control:focus {
    outline: none;
    border-color:rgb(188, 203, 230);
    box-shadow: 0 0 8px rgba(163, 172, 186, 0.4);
  }

  .btn-primary {
    width: 100%;
    padding: 0.8rem 0;
    background: #2575fc;
    border: none;
    border-radius: 0.6rem;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-primary:hover {
    background:rgb(143, 171, 238);
  }

  .create-account {
    margin-top: 1rem;
    font-size: 0.95rem;
    color: #555;
  }

  .create-account a {
    color:rgb(179, 194, 219);
    font-weight: 600;
    text-decoration: none;
    margin-left: 0.3rem;
  }

  .create-account a:hover {
    text-decoration: underline;
  }

  .alert-info {
    background-color: #cce5ff;
    color:rgb(173, 200, 230);
    border-radius: 0.5rem;
    padding: 0.8rem 1rem;
    margin-bottom: 1rem;
    font-weight: 600;
  }
</style>

<div class="login-container shadow-sm">
  <h3>Connexion</h3>

  <?php if (isset($_GET['message'])): ?>
    <div class="alert-info">
      <?= htmlspecialchars($_GET['message']) ?>
    </div>
  <?php endif; ?>

  <form action="/projet_java/app/login" method="post">
    <label for="email" class="form-label">Adresse e-mail</label>
    <input 
      type="email" 
      class="form-control" 
      id="email" 
      name="email" 
      required 
      value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>"
    >

    <label for="password" class="form-label">Mot de passe</label>
    <input 
      type="password" 
      class="form-control" 
      id="password" 
      name="password" 
      required
    >

    <input 
      type="hidden" 
      name="invite_token" 
      value="<?= isset($_GET['invite_token']) ? htmlspecialchars($_GET['invite_token']) : '' ?>"
    >

    <button type="submit" class="btn-primary">Se connecter</button>
  </form>

  <div class="create-account">
    Pas encore de compte ? <a href="/projet_java/app/register">Créer un compte</a>
  </div>
</div>

