<?php
session_start();
require_once("includes/functions.php");

/* Télécharge getapikey.php depuis :
   https://www.plateforme-smc.fr/cybank/getapikey.zip
   et place-le à la racine du projet. */
require_once("getapikey.php");

// Sécurité : utilisateur connecté et transaction définie
if (!isset($_SESSION["user"]) || !isset($_SESSION["transaction_en_cours"])) {
    header("Location: profil.php");
    exit();
}

// Récupération de la transaction
$transaction = $_SESSION["transaction_en_cours"];
$commande = trouverCommandeParTransaction($transaction);

// Vérifier que la commande existe
if (!$commande) {
    die("Commande introuvable.");
}

// Montant formaté pour CYBank
$montant = number_format((float)$commande["total"], 2, ".", "");

// Code vendeur CYBank
$vendeur = "MI-1_J";

// Récupère le protocole et l'hôte dynamiquement
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// URL de retour après paiement
$retour = $protocol . $host . "/profil.php";

// Clé API
$api_key = getAPIKey($vendeur);

// Control hash obligatoire pour CYBank
$control = md5(
    $api_key . "#" .
    $transaction . "#" .
    $montant . "#" .
    $vendeur . "#" .
    $retour . "#"
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement CYBank</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>

<header>
    <h1>Paiement de votre commande</h1>
</header>

<main>
    <div class="conteneur-formulaire">
        <h2>Récapitulatif</h2>

        <p><strong>Commande :</strong> #<?= htmlspecialchars($commande["id"]) ?></p>
        <p><strong>Transaction :</strong> <?= htmlspecialchars($transaction) ?></p>
        <p><strong>Montant :</strong> <?= number_format((float)$commande["total"], 2, ",", " ") ?> €</p>

        <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            <input type="hidden" name="transaction" value="<?= htmlspecialchars($transaction) ?>">
            <input type="hidden" name="montant" value="<?= htmlspecialchars($montant) ?>">
            <input type="hidden" name="vendeur" value="<?= htmlspecialchars($vendeur) ?>">
            <input type="hidden" name="retour" value="<?= htmlspecialchars($retour) ?>">
            <input type="hidden" name="control" value="<?= htmlspecialchars($control) ?>">

            <div class="groupe-formulaire">
                <button type="submit">Payer avec CYBank</button>
            </div>
        </form>
    </div>
</main>

</body>
</html>