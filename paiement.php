<?php
session_start();
require_once("includes/functions.php");

require_once("getapikey.php");

if (!isset($_SESSION["user"]) || !isset($_SESSION["transaction_en_cours"])) {
    header("Location: panier.php");
    exit();
}

$transaction = $_SESSION["transaction_en_cours"];
$commande = trouverCommandeParTransaction($transaction);

if (!$commande) {
    die("Commande introuvable.");
}

$montant = number_format((float)$commande["total"], 2, ".", "");

$vendeur = "MI-1_J";

$retour = "http://localhost/atlas/retour_paiement.php";

$api_key = getAPIKey($vendeur);

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
    <h1>Paiement</h1>
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