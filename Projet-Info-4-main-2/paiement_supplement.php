<?php
session_start();
require_once("includes/functions.php");
require_once("getapikey.php");

/* ── Sécurité ─────────────────────────────────────────────────────────── */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header("Location: connexion.php"); exit();
}
if (!isset($_SESSION['supplement_transaction'],
           $_SESSION['supplement_commande_id'],
           $_SESSION['supplement_montant'])) {
    header("Location: commandes.php"); exit();
}

$transaction  = $_SESSION['supplement_transaction'];
$commande_id  = (int)$_SESSION['supplement_commande_id'];
$montant      = round((float)$_SESSION['supplement_montant'], 2);
$montantFmt   = number_format($montant, 2, '.', '');

$vendeur = "MI-1_J";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
             || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'];
$retour   = $protocol . $host . "/retour_paiement_supplement.php";

$api_key = getAPIKey($vendeur);
$control = md5($api_key . "#" . $transaction . "#" . $montantFmt . "#" . $vendeur . "#" . $retour . "#");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement du supplément - L'Atlas des Saveurs</title>
    <script src="modeSombre.js"></script>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
<header>
    <h1>Paiement du supplément</h1>
    <button id="btn-dark-mode">Changer thème</button>
</header>
<main>
    <div class="conteneur-formulaire">
        <h2>Récapitulatif</h2>
        <p><strong>Commande :</strong> #<?= htmlspecialchars($commande_id) ?></p>
        <p><strong>Transaction :</strong> <?= htmlspecialchars($transaction) ?></p>
        <p>Votre commande a été modifiée et son montant a augmenté.</p>
        <p><strong>Supplément à payer :</strong>
           <?= number_format($montant, 2, ',', ' ') ?> €</p>

        <form action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
            <input type="hidden" name="transaction" value="<?= htmlspecialchars($transaction) ?>">
            <input type="hidden" name="montant"     value="<?= htmlspecialchars($montantFmt) ?>">
            <input type="hidden" name="vendeur"     value="<?= htmlspecialchars($vendeur) ?>">
            <input type="hidden" name="retour"      value="<?= htmlspecialchars($retour) ?>">
            <input type="hidden" name="control"     value="<?= htmlspecialchars($control) ?>">
            <div class="groupe-formulaire">
                <button type="submit">Payer le supplément avec CYBank</button>
            </div>
        </form>

        <p style="margin-top:15px;">
            <a href="commandes.php">← Annuler et revenir à mes commandes</a>
        </p>
    </div>
</main>
</body>
</html>