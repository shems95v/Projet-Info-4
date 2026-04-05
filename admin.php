<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: connexion.php");
    exit();
}

if(!file_exists("inscription.json")){
    $message[] = "Fichier JSON manquant";
    $user= array();
}
else {
    $users = json_decode(file_get_contents("inscription.json"), true);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Utilisateurs</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>

<div class="conteneur-admin">

    <!-- ===== BARRE LATÉRALE ===== -->
    <aside class="barre-laterale">
        <h2>Admin</h2>
        <ul>
            <li class="actif"><a href="#">Utilisateurs</a></li> <br>
            <li class="actif"><a href="accueil.php" class="bouton-modifier">Accueil</a> </li> <br>
            <li class="actif"><a href="presentation.php" class="bouton-modifier">Menu</a></li><br>
            <li class="actif"><a href="profil.php" class="bouton-modifier">Mon profil </a></li><br>
        </ul>
    </aside>

    <!-- ===== CONTENU PRINCIPAL ===== -->
    <div class="contenu-principal">

        <header>
            <h1>Panel Administrateur</h1>
            <a href="logout.php" class="bouton-modifier">Se déconnecter</a>

        </header>

        <h2>Liste des utilisateurs</h2>

        <div class="conteneur-tableau">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Numéro de portable</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?? ' '?></td>
                    <td><?= $user['nom'] ?? ' '?></td>
                    <td><?= $user['telephone'] ?? ' '?></td>
                    <td><?= $user['email'] ?? ' '?></td>

                    <!-- BADGE ROLE -->
                    <td>
                        <span class="badge <?= $user['role'] ?>">
                            <?= $user['role'] ?>
                        </span>
                    </td>

                    <!-- BADGE STATUT -->
                    <td>
                       <?php $actif = $user['actif'] ?? false; ?>

                    <td>
                        <span class="badge <?= $actif ? 'actif' : 'hors-service' ?>">
                            <?= $actif ? "Actif" : "Bloqué" ?>
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

            </table>
        </div>

    </div>

</div>

</body>
</html>
