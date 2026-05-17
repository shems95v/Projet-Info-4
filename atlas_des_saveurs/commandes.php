<?php
session_start();
require_once("includes/functions.php");
date_default_timezone_set('Europe/Paris');

// Redirige si l'utilisateur n'est pas connecté
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
    <script src="modeSombre.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <h1>L'Atlas des Saveurs</h1>
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
                echo '<a href=\"restaurateur.php\">Gestion commandes</a>';
             }
             ?>
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="profil.php">Mon profil</a>
        <a href="panier.php">Panier</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main class="conteneur-commandes">
    <h2>Mes commandes</h2>

    <!-- Message affiché après un paiement réussi -->
    <?php if (isset($_SESSION['message_paiement'])): ?>
        <p style="background:#dcfce7;border:1px solid #16a34a;border-radius:8px;
                  padding:12px;color:#15803d;font-weight:600;margin-bottom:20px;">
            ✅ <?= htmlspecialchars($_SESSION['message_paiement']) ?>
        </p>
        <?php unset($_SESSION['message_paiement']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['modif']) && $_GET['modif'] === 'ok'): ?>
        <p style="background:#dcfce7;border:1px solid #16a34a;border-radius:8px;
                  padding:12px;color:#15803d;font-weight:600;margin-bottom:20px;">
            ✅ Votre commande a bien été modifiée.
        </p>
    <?php endif; ?>

    <?php
    $commandesTrouvees = false;

    foreach ($commandes as $commande):
        // On affiche uniquement les commandes de l'utilisateur connecté
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

<?php
// On vérifie les conditions pour afficher le bouton de notation
$estLivree = strtolower($statutCommande) === "livrée" || strtolower($statutCommande) === "livree";
$estLivraison = isset($commande["mode_recuperation"]) && $commande["mode_recuperation"] === "livraison";
$pasEncoreNotee = !isset($commande["note_livraison"]) || $commande["note_livraison"] === null;
?>

<?php if ($estLivree && $estLivraison && $pasEncoreNotee): ?>
    <a href="notation.php?id=<?= $commande["id"] ?>" class="btn-noter">
        ⭐ Noter la commande
    </a>
<?php elseif ($estLivree && $estLivraison && !$pasEncoreNotee): ?>
    <p style="color: green;">✔️ Déjà notée</p>
<?php endif; ?>

<?php if (($commande['statut_commande'] ?? '') === 'Payée'): ?>
    <a href="modifier_commande.php?id=<?= $commande['id'] ?>" class="btn-noter"
       style="background:#f59e0b;margin-top:8px;display:inline-block;">
        ✏️ Modifier la commande
    </a>
    <!-- Affiche le ticket de réduction s'il y en a un -->
    <?php if (isset($commande['ticket_reduction']) && $commande['ticket_reduction'] > 0): ?>
        <p style="color:#16a34a;margin-top:6px;">
            🎟️ Ticket de réduction disponible :
            <strong><?= number_format($commande['ticket_reduction'], 2, ',', ' ') ?> €</strong>
        </p>
    <?php endif; ?>
<?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (!$commandesTrouvees): ?>
        <div class="aucune-commande">
            <p>Il n'y a encore aucune commande.</p>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2026 L'Atlas des Saveurs — Tous droits réservés</p>
</footer>

</body>
</html>