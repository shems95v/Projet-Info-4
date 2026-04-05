<?php
session_start();

/* Vérification : admin uniquement */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

/* Chargement des utilisateurs */
$users = json_decode(file_get_contents("data/users.json"), true);
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
    <nav>
        <a href="profil.php">Retour profil</a>
        <a href="logout.php">Se déconnecter</a>
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
                    <td>#<?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['login'] ?? '') ?></td>
                    <td>
                        <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                    </td>
                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                    <td>
                        <span class="badge role-<?= htmlspecialchars($user['role'] ?? 'inconnu') ?>">
                            <?= htmlspecialchars($user['role'] ?? 'inconnu') ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= !empty($user['actif']) ? 'badge-actif' : 'badge-bloque' ?>">
                            <?= !empty($user['actif']) ? 'Actif' : 'Bloqué' ?>
                        </span>
                    </td>
                    <td>
                        <!-- Affichage uniquement en phase 2 -->
                        <button type="button">Bloquer</button>
                        <button type="button">Débloquer</button>
                        <button type="button">Passer VIP</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

</body>
</html>