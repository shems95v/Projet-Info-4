<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(["ok" => false, "msg" => "Accès refusé"]);
    exit();
}

$action  = $_POST['action'] ?? '';
$id      = $_POST['id']     ?? '';
$fichier = "data/users.json";
$users   = json_decode(file_get_contents($fichier), true);

if ($action === "toggle_actif") {

    $nouvelActif = $_POST['actif'] === "1";

    foreach ($users as &$u) {
        if ($u['id'] == $id) {
            $u['actif'] = $nouvelActif;
            break;
        }
    }

    file_put_contents($fichier, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $msg = $nouvelActif ? "Utilisateur débloqué." : "Utilisateur bloqué.";
    echo json_encode(["ok" => true, "msg" => $msg]);

} elseif ($action === "change_role") {

    $roles   = ['client', 'admin', 'livreur', 'restaurateur'];
    $nouveau = $_POST['role'] ?? '';

    if (!in_array($nouveau, $roles)) {
        echo json_encode(["ok" => false, "msg" => "Rôle invalide."]);
        exit();
    }

    foreach ($users as &$u) {
        if ($u['id'] == $id) {
            $u['role'] = $nouveau;
            break;
        }
    }

    file_put_contents($fichier, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(["ok" => true, "msg" => "Rôle mis à jour."]);

} else {
    echo json_encode(["ok" => false, "msg" => "Action inconnue."]);
}