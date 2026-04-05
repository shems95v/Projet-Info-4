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
<title>Admin - Utilisateurs</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>

<header>
    <h1>Panel Administrateur</h1>
    <a href="logout.php">Se déconnecter</a>
</header>

<main>

<h2>Liste des utilisateurs</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Login</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>

<?php foreach ($users as $user): ?>
<tr>
    <td><?= $user['id'] ?></td>
    <td><?= $user['login'] ?></td>
    <td><?= $user['nom'] ?></td>
    <td><?= $user['email'] ?></td>
    <td><?= $user['role'] ?></td>
    <td><?= $user['actif'] ? "Actif" : "Bloqué" ?></td>

    <td>
        <!-- boutons (non fonctionnels phase 2) -->
        <button>Bloquer</button>
        <button>Débloquer</button>
        <button>Passer VIP</button>
    </td>
</tr>
<?php endforeach; ?>

</table>

</main>

</body>
</html>