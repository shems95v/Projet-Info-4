<?php
session_start();

/* sécurité : livreur uniquement */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'livreur') {
    header("Location: connexion.php");
    exit();
}

/* charger commandes */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

/* utilisateur connecté */
$user = $_SESSION['user'];
$livraisonTrouvee = false;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Livraisons</title>
<link rel="stylesheet" href="livraison.css">
</head>

<body>

<header>
    <h1>Mes livraisons</h1>
    <a href="profil.php">← Retour</a>
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
            <a
                class="btn"
                href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($commande['adresse'] ?? '') ?>"
                target="_blank"
            >
                Ouvrir dans Maps
            </a>

            <button class="btn btn-delivered" type="button">
                Livraison terminée
            </button>
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