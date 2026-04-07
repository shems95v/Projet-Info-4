<?php
session_start();
require_once("includes/functions.php");

if (!isset($_SESSION["user"])) {
    header("Location: connexion.php");
    exit();
}

if (empty($_SESSION["panier"])) {
    header("Location: panier.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmer_commande"])) {
    $dateLivraison = trim($_POST["date_livraison"] ?? "");
    $modeRecuperation = $_POST["mode_recuperation"] ?? "livraison";

    $commandes = lireJSON("data/commandes.json");
    $nouvelId = count($commandes) > 0 ? (max(array_column($commandes, "id")) + 1) : 1;
    $transaction = genererTransactionId();

    $platsCommande = [];
    foreach ($_SESSION["panier"] as $platId => $quantite) {
        $plat = trouverPlatParId($platId);
        if ($plat) {
            $platsCommande[] = [
                "id" => $plat["id"],
                "nom" => $plat["nom"],
                "prix" => $plat["prix"],
                "quantite" => (int)$quantite
            ];
        }
    }

    $commande = [
        "id" => $nouvelId,
        "transaction" => $transaction,
        "user_id" => $_SESSION["user"]["id"],
        "restaurant_id" => 1,
        "livreur_id" => null,
        "nom_client" => trim(($_SESSION["user"]["prenom"] ?? "") . " " . ($_SESSION["user"]["nom"] ?? "")),
        "adresse" => $_SESSION["user"]["adresse"] ?? "Adresse non renseignée",
        "telephone" => $_SESSION["user"]["telephone"] ?? "",
        "interphone" => $_SESSION["user"]["interphone"] ?? "",
        "etage" => $_SESSION["user"]["etage"] ?? "",
        "commentaire" => trim($_POST["commentaire"] ?? ""),
        "date_creation" => date("Y-m-d H:i:s"),
        "date_livraison_prevue" => $dateLivraison !== "" ? $dateLivraison : date("Y-m-d H:i:s"),
        "mode_recuperation" => $modeRecuperation,
        "plats" => $platsCommande,
        "total" => calculerTotalPanier($_SESSION["panier"]),
        "statut_paiement" => "en_attente",
        "statut_commande" => "En attente"
    ];

    $commandes[] = $commande;
    ecrireJSON("data/commandes.json", $commandes);

    $_SESSION["transaction_en_cours"] = $transaction;

    header("Location: paiement.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Validation de la commande</title>
<link rel="stylesheet" href="inscription.css">
</head>
<body>
<header>
    <h1>L'Atlas des Saveurs</h1>
</header>

<main>
    <div class="conteneur-formulaire">
        <h2>Valider ma commande</h2>

        <form method="POST" action="valider_commande.php">
            <div class="groupe-formulaire">
                <label for="mode_recuperation">Mode</label>
                <select id="mode_recuperation" name="mode_recuperation" required>
                    <option value="livraison">Livraison</option>
                    <option value="emporter">À emporter</option>
                </select>
            </div>

            <div class="groupe-formulaire">
                <label for="date_livraison">Date/heure souhaitée</label>
                <input type="datetime-local" id="date_livraison" name="date_livraison">
            </div>

            <div class="groupe-formulaire">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" name="commentaire" placeholder="Instructions de livraison..."></textarea>
            </div>

            <div class="groupe-formulaire">
                <button type="submit" name="confirmer_commande">Continuer vers le paiement</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>