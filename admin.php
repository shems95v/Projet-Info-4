<?php
session_start();

/* Vérification : admin uniquement */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

/* Chargement des utilisateurs */
$users = json_decode(file_get_contents("users.json"), true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Utilisateurs</title>
<link rel="stylesheet" href="admin.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <h1>Panel Administrateur</h1>
     <nav class="navigation">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                echo '<a href="admin.php">Admin</a>';
            }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur') {
                echo '<a href="livraison.php">Livraison</a>';
             }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'restaurateur') {
                echo '<a href="commande.php">commande</a>';
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
    <h2>Liste des utilisateurs</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?? ' '?></td>
                    <td><?= $user['nom'] ?? ' '?></td>
                    <td><?= $user['telephone'] ?? ' '?></td>
                    <td><?= $user['email'] ?? ' '?></td>
                    <td>
                        <span class="badge role-<?= htmlspecialchars($user['role'] ?? 'inconnu') ?>">
                            <?= htmlspecialchars($user['role'] ?? 'inconnu') ?>
                        </span>
                    </td>
                    <td>
                       <?php $actif = $user['actif'] ?? false; ?>

                    <td>
                        <span class="badge <?= !empty($user['actif']) ? 'badge-actif' : 'badge-bloque' ?>">
                            <?= !empty($user['actif']) ? 'Actif' : 'Bloqué' ?>
                        </span>
                    </td>

                    <td>
                        <button class="bouton-modifier">
                            <?= $actif ? "Bloquer" : "Débloquer" ?>
                        </button>
                        <button class="bouton-modifier">Modifier Role</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

</body>
</html>