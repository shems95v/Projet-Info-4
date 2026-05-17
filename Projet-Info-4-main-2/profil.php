<?php

session_start();
require_once "auth.php";
verifierSession();

if (!isset($_SESSION["user"])) {
    exit();
}

if (!file_exists("data/users.json")) {
    exit("Fichier introuvable");
}

$backdata = json_decode(file_get_contents("data/users.json"), true);

$idUsers = $_SESSION["user"]["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email2"] ?? "";
    $telephone = $_POST["telephone2"] ?? "";
    $login = $_POST["login"] ?? "";


    

foreach ($backdata as &$user) {
    if ($user["id"] == $idUsers) {
        $user["nom"]         = $_POST["nom2"];
        $user["prenom"]      = $_POST["prenom2"];
        $user["email"]       = $_POST["email2"];
        $user["telephone"]   = $_POST["telephone2"];
        $user["adresse"]     = $_POST["adresse2"];
        $user["commentaire"] = $_POST["commentaire2"];
        $_SESSION["user"] = $user;
        break;
    }
}

file_put_contents(
    "data/users.json",
    json_encode($backdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);
}

$user = $_SESSION["user"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Atlas des Saveurs</title>
    <link rel="stylesheet" href="profil.css">
    <script src="modeSombre.js" defer></script>
    <script src="profil.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <h1>L'Atlas des Saveurs</h1>
        <button id="btn-dark-mode">Changer thème</button>
        <nav class="navigation">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                echo '<a href="admin.php">Admin</a>';
                echo '<a href="livraison.php">Livraison</a>';
                echo '<a href="commandes.php">commandes</a>';
            }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur') {
                echo '<a href="livraison.php">Livraison</a>';
             }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'restaurateur') {
                echo '<a href="commandes.php">commandes</a>';
             }
             ?>
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <?php
            if (!isset($_SESSION['user'])) {
                echo '<a href="inscription.php">Inscription</a>';
                echo '<a href="connexion.php">Connexion</a>';
            } else {
                echo '<a href="profil.php">Mon profil</a>';
                echo '<a href="panier.php">Panier</a>';
                echo '<a href="logout.php">Déconnexion</a>';
            }
            ?>
        </nav>
    </header>

    <main>
        <div class="profil-container">
            <h2>Mon Profil</h2>

            <?php if (($user['role'] ?? '') === 'livreur'): ?>
                <p><a href="livraison.php" class="btn-voir-commandes">Mes livraisons</a></p>
            <?php endif; ?>

            <?php if (($user['role'] ?? '') === 'restaurateur'): ?>
                <p><a href="restaurateur.php" class="btn-voir-commandes">Gérer les commandes</a></p>
            <?php endif; ?>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <p><a href="admin.php" class="btn-voir-commandes">Panel administrateur</a></p>
            <?php endif; ?>

            <section class="profil-section">
    <h3>
        📋 Informations personnelles
        <a href="#" class="edit-icon" id="btn-edit" title="Modifier">✏️</a>
    </h3>

   
    <div id="info-view">
        <div class="info-grid">

            <div class="info-item">
                <strong>Nom :</strong>
                <span id ="nom-affichage"><?= htmlspecialchars($user["nom"] ?? "") ?></span>
            </div>

            <div class="info-item">
                <strong>Prénom :</strong>
                <span id ="prenom-affichage"><?= htmlspecialchars($user["prenom"] ?? "") ?></span>
            </div>

            <div class="info-item">
                <strong>Login :</strong>
                <span id ="login-affichage"><?= htmlspecialchars($user["login"] ?? "") ?></span>
            </div>

            <div class="info-item">
                <strong>Email :</strong>
                <span id ="email-affichage"><?= htmlspecialchars($user["email"] ?? "") ?></span>
            </div>

            <div class="info-item">
                <strong>Téléphone :</strong>
                <span id ="tel-affichage"><?= htmlspecialchars($user["telephone"] ?? "Non renseigné") ?></span>
            </div>

            <div class="info-item full-width">
                <strong>Adresse :</strong>
                <span id ="adresse-affichage"><?= htmlspecialchars($user["adresse"] ?? "Non renseignée") ?></span>
            </div>

            <div class="info-item full-width">
                <strong>Informations complémentaires :</strong>
                <span id ="com-affichage"><?= htmlspecialchars($user["commentaire"] ?? "Aucune information complémentaire") ?></span>
            </div>

        </div>
    </div>

    <form id="info-edit" action="profil.php" method="POST" style="display:none;">
        <div class="info-grid">

            <div class="info-item">
                <strong>Nom :</strong>
                <input type="text" name="nom2" value="<?= htmlspecialchars($user["nom"] ?? "") ?>">
            </div>

            <div class="info-item">
                <strong>Prénom :</strong>
                <input type="text" name="prenom2" value="<?= htmlspecialchars($user["prenom"] ?? "") ?>">
            </div>

            <div class="info-item">
                <strong>Login :</strong>
                <input type="text" name="login" value="<?= htmlspecialchars($user["login"] ?? "") ?>" 
                >
                
            </div>

            <div class="info-item">
                <strong>Email :</strong>
                <input type="email" name="email2" value="<?= htmlspecialchars($user["email"] ?? "") ?>">
            </div>

            <div class="info-item">
                <strong>Téléphone :</strong>
                <input type="text" name="telephone2" value="<?= htmlspecialchars($user["telephone"] ?? "") ?>">
            </div>

            <div class="info-item full-width">
                <strong>Adresse :</strong>
                <input type="text" name="adresse2" value="<?= htmlspecialchars($user["adresse"] ?? "") ?>">
            </div>

            <div class="info-item full-width">
                <strong>Informations complémentaires :</strong>
                <textarea name="commentaire2"><?= htmlspecialchars($user["commentaire"] ?? "") ?></textarea>
            </div>

        </div>

        <button type="submit" class="btn">Enregistrer</button>
    </form>
</section>

            <section class="profil-section">
                <h3>📦 Mes commandes</h3>

                <a href="commandes.php" class="btn-voir-commandes">
                    Voir toutes mes commandes
                </a>

                <div class="commandes-list">
                    <div class="commande-card">
                        <div class="commande-header">
                            <span class="commande-numero">Commande récente</span>
                            <span class="commande-date">Voir historique complet</span>
                        </div>
                        <div class="commande-details">
                            <p><strong>Accès :</strong> Consultez toutes vos commandes dans la page dédiée.</p>
                            <p><strong>Suivi :</strong> Vous y retrouverez les statuts, le paiement et la notation.</p>
                            <a href="commandes.php" class="btn-noter">📦 Ouvrir mes commandes</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

 

    <footer>
        <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
    </footer>

</body>
</html>