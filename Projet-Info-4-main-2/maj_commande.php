<?php
session_start();

/* sécurité restaurateur */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'restaurateur') {
    http_response_code(403);
    echo json_encode(["erreur" => "Accès refusé"]);
    exit();
}

/* on récupère les données envoyées en JSON */
$data = json_decode(file_get_contents("php://input"), true);

$idCommande = $data['id'] ?? null;
$nouveauStatut = $data['statut'] ?? null;
$livreurId = $data['livreur_id'] ?? null;

if (!$idCommande || !$nouveauStatut) {
    echo json_encode(["erreur" => "Données manquantes"]);
    exit();
}

/* statuts autorisés */
$statutsAutorises = ["En attente", "En préparation", "Prête", "En livraison", "Livrée"];
if (!in_array($nouveauStatut, $statutsAutorises)) {
    echo json_encode(["erreur" => "Statut invalide"]);
    exit();
}

/* charger les commandes */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

$trouve = false;
foreach ($commandes as &$commande) {
    if ($commande['id'] == $idCommande) {
        $commande['statut_commande'] = $nouveauStatut;
        if ($livreurId !== null) {
            $commande['livreur_id'] = $livreurId;
        }
        $trouve = true;
        break;
    }
}

if (!$trouve) {
    echo json_encode(["erreur" => "Commande introuvable"]);
    exit();
}

/* sauvegarder */
file_put_contents("data/commandes.json", json_encode($commandes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(["succes" => true, "nouveau_statut" => $nouveauStatut]);