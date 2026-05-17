<?php
session_start();
require_once "auth.php";
verifierSession(['livreur', 'admin']);

$user = $_SESSION['user'];

/* traitement livraison terminée */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commande_id"])) {

    $commandes = json_decode(file_get_contents("data/commandes.json"), true);

    foreach ($commandes as &$commande) {

        if ($commande["id"] == $_POST["commande_id"]) {

            // sécurité : bon livreur
            if (($commande["livreur_id"] ?? null) != $user["id"]) {
                die("Accès refusé");
            }

            // sécurité : bon statut
            if (($commande["statut_commande"] ?? "") !== "En livraison") {
                die("Action impossible");
            }

            // mise à jour
            $commande["statut_commande"] = "Livrée";

            break;
        }
    }

    file_put_contents("data/commandes.json", json_encode($commandes, JSON_PRETTY_PRINT));

    header("Location: livraison.php");
    exit();
}

/* charger commandes */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

$livraisonTrouvee = false;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Livraisons</title>
<link rel="stylesheet" href="livraison.css">
<script src="modeSombre.js"></script>
</head>

<body>

<header>
    <h1>Mes livraisons</h1>
    <button id="btn-dark-mode">Changer thème</button>
     <nav class="navigation">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                echo '<a href="admin.php">Admin</a>';
                echo '<a href="livraison.php">Livraison</a>';
                echo '<a href="commandes.php">commandes</a>';
            }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur') {
                echo '<a href="livraison.php">Livraison</a>';
             }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'restaurateur') {
                echo '<a href="commande.php">commandes</a>';
             }
             ?>
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <?php
            if (!isset($_SESSION['user'])) {
                echo '<a href="inscription.php">Inscription</a>';
                echo '<a href="connexion.php">Connexion</a>';
            } else {
                echo '<a href="profil.php">Mon profil</a>';
                echo '<a href="panier.php">Panier</a>';
                echo '<a href="logout.php">Déconnexion</a>';
            }
            ?>
        </nav>
</header>

<main>

<?php foreach ($commandes as $commande): ?>
    <?php
    $statutCommande = $commande['statut_commande'] ?? $commande['statut'] ?? '';
    ?>

    <?php if (($commande['livreur_id'] ?? null) == $user['id'] && $statutCommande === "En livraison"): ?>
        <?php $livraisonTrouvee = true; ?>

        <section class="infos-livraison">
            <h2>Commande #<?= htmlspecialchars($commande['id']) ?></h2>

            <p><b>Nom :</b> <?= htmlspecialchars($commande['nom_client'] ?? 'Client inconnu') ?></p>
            <p><b>Adresse :</b> <?= htmlspecialchars($commande['adresse'] ?? 'Adresse inconnue') ?></p>
            <p><b>Interphone :</b> <?= htmlspecialchars($commande['interphone'] ?? '') ?></p>
            <p><b>Étage :</b> <?= htmlspecialchars($commande['etage'] ?? '') ?></p>
            <p><b>Commentaires :</b> <?= htmlspecialchars($commande['commentaire'] ?? '') ?></p>

            <p>
                <b>Téléphone :</b>
                <a href="tel:<?= htmlspecialchars($commande['telephone'] ?? '') ?>">
                    <?= htmlspecialchars($commande['telephone'] ?? '') ?>
                </a>
            </p>
        </section>

        <section class="actions">
<form method="POST">
    <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">

    <a
        class="btn"
        href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($commande['adresse'] ?? '') ?>"
        target="_blank"
    >
        Ouvrir dans Maps
    </a>

    <button class="btn btn-delivered" type="submit" name="livrer">
        🚚 Livraison terminée
    </button>
</form>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!$livraisonTrouvee): ?>
    <section class="infos-livraison">
        <h2>Aucune livraison</h2>
        <p>Vous n'avez aucune commande en livraison pour le moment.</p>
    </section>
<?php endif; ?>

</main>

</body>
</html>