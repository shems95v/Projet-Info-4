<?php
session_start();
require_once("includes/functions.php");
require_once("getapikey.php");

// Vérification des paramètres obligatoires envoyés par CYBank
if (!isset($_POST['transaction'], $_POST['control'], $_POST['montant'], $_POST['vendeur'])) {
    die("Données manquantes.");
}

$transaction = $_POST['transaction'];
$montant_recu = $_POST['montant'];
$vendeur = $_POST['vendeur'];
$control_recu = $_POST['control'];

// Récupère la clé API
$api_key = getAPIKey($vendeur);

// Recalcul du control côté serveur pour sécurité
$control_calcule = md5($api_key . "#" . $transaction . "#" . $montant_recu . "#" . $vendeur . "##");

// Vérifier que le contrôle est correct
if ($control_recu !== $control_calcule) {
    die("Erreur de sécurité : contrôle invalide.");
}

// Récupération de la commande par transaction
$commande = trouverCommandeParTransaction($transaction);
if (!$commande) {
    die("Commande introuvable.");
}

// Mettre à jour le statut de la commande
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

foreach ($commandes as &$c) {
    if ($c['transaction'] === $transaction) {
        $c['statut_commande'] = "Payée"; // ou "En préparation" selon ton workflow
        $c['date_paiement'] = date("Y-m-d H:i:s");
        break;
    }
}

// Sauvegarde des changements
file_put_contents("data/commandes.json", json_encode($commandes, JSON_PRETTY_PRINT));

// Optionnel : supprimer la transaction en cours dans la session
unset($_SESSION['transaction_en_cours']);

// Redirection vers le profil avec message de succès
$_SESSION['message_paiement'] = "Votre paiement a été effectué avec succès !";
header("Location: profil.php");
exit();