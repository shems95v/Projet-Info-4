<?php
session_start();
require_once("includes/functions.php");
require_once("getapikey.php");

/* ── Vérification des paramètres CYBank ──────────────────────────────── */
if (!isset($_POST['transaction'], $_POST['control'], $_POST['montant'], $_POST['vendeur'])) {
    die("Données manquantes.");
}

$transaction    = $_POST['transaction'];
$montant_recu   = $_POST['montant'];
$vendeur        = $_POST['vendeur'];
$control_recu   = $_POST['control'];

$api_key        = getAPIKey($vendeur);
$control_calcule = md5($api_key . "#" . $transaction . "#" . $montant_recu . "#" . $vendeur . "##");

if ($control_recu !== $control_calcule) {
    die("Erreur de sécurité : contrôle invalide.");
}

/* ── Mise à jour de la commande ──────────────────────────────────────── */
$commandes = lireJSON("data/commandes.json");

foreach ($commandes as &$c) {
    if (($c['transaction_supplement'] ?? '') === $transaction) {
        $c['statut_supplement']      = 'payé';
        $c['date_paiement_supplement'] = date("Y-m-d H:i:s");
        break;
    }
}
unset($c);

ecrireJSON("data/commandes.json", $commandes);

/* ── Nettoyage session ───────────────────────────────────────────────── */
unset($_SESSION['supplement_transaction'],
      $_SESSION['supplement_commande_id'],
      $_SESSION['supplement_montant']);

$_SESSION['message_paiement'] = "Supplément payé avec succès. Votre commande a bien été modifiée !";
header("Location: commandes.php");
exit();