<?php
require_once 'functions.php';

// Si déjà connecté, redirection directe vers la liste
if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
// Pré-remplissage avec le cookie (Exercice 5)
$login_saisi = $_COOKIE['dernier_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mdp'] ?? '';
    $login_saisi = $login;

    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        $_SESSION['user'] = $login;
        // Cookie valable 7 jours (7 * 24 * 3600 secondes)
        setcookie('dernier_user', $login, time() + 7 * 24 * 3600, '/');
        header('Location: liste.php');
        exit;
    } else {
        $erreur = "Identifiant ou mot de passe incorrect.";
    }
}

$titre = "Connexion";
include 'header.php';
?>

<div class="card form-card">
    <img src="img/ensa-logo.png" alt="Logo ENSA Safi" class="logo-login">
    <h2>Connexion</h2>
    <?php if ($erreur !== ''): ?>
        <div class="alert alert-danger"><?= h($erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <div class="form-group">
            <label for="login">Login :</label>
            <input type="text" id="login" name="login" value="<?= h($login_saisi) ?>" required>
        </div>
        <div class="form-group">
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>

<?php include 'footer.php'; ?>