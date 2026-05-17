<?php
session_start();

/* Sécurité : restaurateur ou admin uniquement */
if (!isset($_SESSION['user']) ||
    !in_array($_SESSION['user']['role'], ['restaurateur', 'admin'])) {
    http_response_code(403);
    echo json_encode(["erreur" => "Accès refusé"]);
    exit();
}

/* Lecture des données JSON envoyées */
$data = json_decode(file_get_contents("php://input"), true);

$idCommande    = $data['id']         ?? null;
$nouveauStatut = $data['statut']     ?? null;
$livreurId     = $data['livreur_id'] ?? null;

if (!$idCommande || !$nouveauStatut) {
    echo json_encode(["erreur" => "Données manquantes"]);
    exit();
}

/*
 * Transitions autorisées pour le restaurateur :
 * On ne peut passer QUE vers le statut immédiatement suivant.
 */
$transitionsAutorisees = [
    'Payée'          => 'En préparation',
    'En préparation' => 'Prête',
    'Prête'          => 'En livraison',
];

/* Chargement des commandes */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

$trouve = false;
foreach ($commandes as &$commande) {
    if ((int)$commande['id'] !== (int)$idCommande) continue;

    $statutActuel = $commande['statut_commande'] ?? '';

    /* Vérification que la transition est autorisée */
    if (($transitionsAutorisees[$statutActuel] ?? null) !== $nouveauStatut) {
        echo json_encode([
            "erreur" => "Transition non autorisée : $statutActuel → $nouveauStatut"
        ]);
        exit();
    }

    /* Si on passe en livraison, le livreur est obligatoire */
    if ($nouveauStatut === 'En livraison') {
        if (empty($livreurId)) {
            echo json_encode(["erreur" => "Livreur obligatoire pour passer en livraison"]);
            exit();
        }
        /* Vérifier que le livreur existe et est actif */
        $users = json_decode(file_get_contents("data/users.json"), true);
        $livreurValide = false;
        foreach ($users as $u) {
            if ((string)$u['id'] === (string)$livreurId
                && ($u['role'] ?? '') === 'livreur'
                && ($u['actif'] ?? false)) {
                $livreurValide = true;
                break;
            }
        }
        if (!$livreurValide) {
            echo json_encode(["erreur" => "Livreur invalide ou inactif"]);
            exit();
        }
        $commande['livreur_id'] = $livreurId;
    }

    $commande['statut_commande'] = $nouveauStatut;
    $commande['date_maj_statut'] = date("Y-m-d H:i:s");
    $trouve = true;
    break;
}
unset($commande);

if (!$trouve) {
    echo json_encode(["erreur" => "Commande introuvable"]);
    exit();
}

/* Sauvegarde */
file_put_contents(
    "data/commandes.json",
    json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo json_encode(["succes" => true, "nouveau_statut" => $nouveauStatut]);