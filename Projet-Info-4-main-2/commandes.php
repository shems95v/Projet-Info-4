<?php
session_start();
require_once("includes/functions.php");
date_default_timezone_set('Europe/Paris');

/* sécurité : utilisateur connecté obligatoire */
if (!isset($_SESSION["user"])) {
    header("Location: connexion.php");
    exit();
}

$user = $_SESSION["user"];
$commandes = lireJSON("data/commandes.json");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - L'Atlas des Saveurs</title>
    <link rel="stylesheet" href="commandes.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <h1>L'Atlas des Saveurs</h1>
    <nav class="navigation">
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="profil.php">Mon profil</a>
        <a href="panier.php">Panier</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="conteneur-commandes">
    <h2>Mes commandes</h2>

    <?php
    $commandesTrouvees = false;

    foreach ($commandes as $commande):
        if (!isset($commande["user_id"]) || $commande["user_id"] != $user["id"]) {
            continue;
        }

        $commandesTrouvees = true;

        $statutCommande = $commande["statut_commande"] ?? $commande["statut"] ?? "Non défini";
        $dateCommande = $commande["date_creation"] ?? $commande["date"] ?? "Date inconnue";
    ?>
        <article class="commande-card">
            <div class="commande-header">
                <span class="commande-numero">Commande #<?= htmlspecialchars($commande["id"]) ?></span>
                <span class="commande-date"><?= htmlspecialchars($dateCommande) ?></span>
            </div>

            <div class="commande-details">
                <p>
                    <strong>Statut commande :</strong>
                    <span class="statut-badge">
                        <?= htmlspecialchars($statutCommande) ?>
                    </span>
                </p>

                <p>
                    <strong>Statut paiement :</strong>
                    <?= htmlspecialchars($commande["statut_paiement"] ?? "Non défini") ?>
                </p>

                <?php if (isset($commande["date_livraison_prevue"])): ?>
                    <p>
                        <strong>Date prévue :</strong>
                        <?= htmlspecialchars($commande["date_livraison_prevue"]) ?>
                    </p>
                <?php endif; ?>

                <?php if (isset($commande["mode_recuperation"])): ?>
                    <p>
                        <strong>Mode :</strong>
                        <?= htmlspecialchars($commande["mode_recuperation"]) ?>
                    </p>
                <?php endif; ?>

                <p><strong>Articles :</strong></p>
                <ul class="liste-plats">
                    <?php if (isset($commande["plats"]) && is_array($commande["plats"])): ?>
                        <?php foreach ($commande["plats"] as $plat): ?>
                            <li>
                                <?= htmlspecialchars($plat["nom"] ?? "Plat inconnu") ?>
                                x<?= htmlspecialchars($plat["quantite"] ?? 1) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucun article enregistré</li>
                    <?php endif; ?>
                </ul>

                <p>
                    <strong>Total :</strong>
                    <?= number_format((float)($commande["total"] ?? 0), 2, ",", " ") ?> €
                </p>

                <?php if (mb_strtolower($statutCommande) === "livrée" || mb_strtolower($statutCommande) === "livree"): ?>
                    <a href="notation.php" class="btn-noter">⭐ Noter la commande</a>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (!$commandesTrouvees): ?>
        <div class="aucune-commande">
            <p>Vous n’avez encore aucune commande.</p>
            <a href="presentation.php" class="btn-retour-menu">Voir le menu</a>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2026 L'Atlas des Saveurs — Tous droits réservés</p>
</footer>

</body>
</html>