<?php

session_start();
require_once "auth.php";
verifierSession(['admin']); // redirige si pas admin

// Chargement de tous les utilisateurs
$users = json_decode(file_get_contents("data/users.json"), true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Utilisateurs</title>
    <link rel="stylesheet" href="admin.css">
    <script src="modeSombre.js"></script>
    <script src="admin.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <h1>Panel Administrateur</h1>
    <button id="btn-dark-mode">Changer thème</button>
    <nav class="navigation">
        <a href="admin.php">Admin</a>
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="profil.php">Mon profil</a>
        <a href="panier.php">Panier</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>
<main>
    <h2>Liste des utilisateurs</h2>

    <!-- Zone pour afficher les messages de retour (succès / erreur) -->
    <div id="message-admin"></div>

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
                <?php $actif = !empty($user['actif']); ?>
                <tr id="ligne-<?= $user['id'] ?>">
                    <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['login'] ?? '') ?></td>
                    <td><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                    <td>
                        <!-- Badge rôle, mis à jour en JS après changement -->
                        <span class="badge role-<?= htmlspecialchars($user['role'] ?? 'inconnu') ?>" id="role-<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['role'] ?? 'inconnu') ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $actif ? 'badge-actif' : 'badge-bloque' ?>" id="statut-<?= $user['id'] ?>">
                            <?= $actif ? 'Actif' : 'Bloqué' ?>
                        </span>
                    </td>
                    <td>
                        <button 
                            class="bouton-bloquer" 
                            data-id="<?= $user['id'] ?>"
                            data-actif="<?= $actif ? '1' : '0' ?>">
                            <?= $actif ? "Bloquer" : "Débloquer" ?>
                        </button>

                        <!-- Sélecteur de rôle, pré-sélectionné sur le rôle actuel -->
                        <select class="select-role" data-id="<?= $user['id'] ?>">
                            <?php foreach (['client','admin','livreur','restaurateur'] as $r): ?>
                                <option value="<?= $r ?>" <?= ($user['role'] ?? '') === $r ? 'selected' : '' ?>>
                                    <?= $r ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="bouton-role" data-id="<?= $user['id'] ?>">
                            Changer rôle
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>