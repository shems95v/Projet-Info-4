<?php
function verifierSession($rolesAutorises = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: connexion.php");
        exit();
    }

    // Recharger depuis le JSON pour avoir les données fraîches
    $users = json_decode(file_get_contents("data/users.json"), true);
    foreach ($users as $u) {
        if ($u['id'] == $_SESSION['user']['id']) {
            // Mettre à jour la session avec les données actuelles
            $_SESSION['user'] = $u;

            // Vérifier si bloqué
            if (empty($u['actif'])) {
                session_destroy();
                header("Location: connexion.php?bloque=1");
                exit();
            }

            break;
        }
    }

    // Vérifier le rôle si nécessaire
    if (!empty($rolesAutorises) && !in_array($_SESSION['user']['role'], $rolesAutorises)) {
        header("Location: accueil.php");
        exit();
    }
}