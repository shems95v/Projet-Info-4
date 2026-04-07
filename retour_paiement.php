<?php
session_start();
require_once("includes/functions.php");
require_once("getapikey.php");

$transaction = $_GET["transaction"] ?? "";
$montant = $_GET["montant"] ?? "";
$vendeur = $_GET["vendeur"] ?? "";
$status = $_GET["status"] ?? "";
$controlRecu = $_GET["control"] ?? "";

$api_key = getAPIKey($vendeur);

$controlCalcule = md5(
    $api_key . "#" .
    $transaction . "#" .
    $montant . "#" .
    $vendeur . "#" .
    $status . "#"
);

$paiementValide = ($controlRecu === $controlCalcule);

if ($paiementValide && $status === "accepted") {
    mettreAJourStatutCommande($transaction, "payé", "À préparer");
    unset($_SESSION["panier"]);
    unset($_SESSION["transaction_en_cours"]);
    $message = "Paiement accepté. Votre commande est enregistrée.";
} else {
    mettreAJourStatutCommande($transaction, "refusé", "Paiement refusé");
    $message = "Paiement refusé ou retour invalide.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Retour paiement</title>
<link rel="stylesheet" href="connexion.css">
</head>
<body>
<header>
    <h1>Retour de paiement</h1>
</header>

<main>
    <div class="conteneur-formulaire">
        <h2>Résultat</h2>
        <p><?= htmlspecialchars($message) ?></p>
        <p><a href="profil.php">Retour au profil</a></p>
        <p><a href="commandes.php">Voir mes commandes</a></p>
    </div>
</main>
</body>
</html>