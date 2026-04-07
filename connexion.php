<?php
session_start();
require_once("includes/functions.php");

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $password = trim($_POST["motdepasse"] ?? "");

    $user = trouverUtilisateurParLogin($login, $password);

    if ($user) {
        $_SESSION["user"] = $user;

        if ($user["role"] === "admin") {
            header("Location: admin.php");
            exit();
        } elseif ($user["role"] === "restaurateur") {
            header("Location: restaurateur.php");
            exit();
        } elseif ($user["role"] === "livreur") {
            header("Location: livraison.php");
            exit();
        } else {
            header("Location: profil.php");
            exit();
        }
    } else {
        $erreur = "Login ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Atlas des Saveurs</title>
    <link rel="stylesheet" href="connexion.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <h1>L'Atlas des Saveurs</h1>
    <nav>
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="profil.php">Mon Profil</a>
        <a href="inscription.php">Inscription</a>
        <a href="connexion.php">Connexion</a>
        <a href="panier.php">Panier</a>
    </nav>
</header>

<main>
    <div class="conteneur-formulaire">
        <h2>Connexion</h2>

        <?php if (!empty($erreur)): ?>
            <p style="color:red; margin-bottom:15px; text-align:center;">
                <?= htmlspecialchars($erreur) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="connexion.php">

            <div class="groupe-formulaire">
                <label for="login">Login <span class="obligatoire">*</span></label>
                <input type="text" id="login" name="login" placeholder="Votre login" required>
            </div>

            <div class="groupe-formulaire">
                <label for="motdepasse">Mot de passe <span class="obligatoire">*</span></label>
                <input type="password" id="motdepasse" name="motdepasse" placeholder="Votre mot de passe" required>
            </div>

            <div class="groupe-formulaire">
                <button type="submit">Se connecter</button>
            </div>

        </form>

        <div class="liens-formulaire">
            <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
</footer>

</body>
</html>