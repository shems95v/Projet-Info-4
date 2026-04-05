<?php
session_start();

/* sécurité restaurateur */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'restaurateur') {
    header("Location: connexion.php");
    exit();
}

/* charger données */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

/* utilisateur connecté */
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des commandes</title>
<link rel="stylesheet" href="restaurateur.css">
</head>

<body>

<header>
    <h1>Commandes du restaurant</h1>
    <a href="profil.php">← Retour</a>
</header>

<main>

<?php foreach ($commandes as $commande): ?>

    <?php if (($commande['restaurant_id'] ?? 0) == 1): ?>

        <?php
        $statut = $commande['statut_commande'] ?? $commande['statut'] ?? 'Statut inconnu';
        $dateAffichee = $commande['date_creation'] ?? $commande['date'] ?? 'Date inconnue';

        $classeStatut = strtolower($statut);
        $classeStatut = str_replace(
            [' ', 'é', 'è', 'ê', 'à', 'ù'],
            ['-', 'e', 'e', 'e', 'a', 'u'],
            $classeStatut
        );
        ?>

        <div class="commande-card">

            <div class="commande-header">
                <span>#<?= htmlspecialchars($commande['id']) ?></span>
                <span><?= htmlspecialchars($dateAffichee) ?></span>
            </div>

            <div class="commande-body">

                <p><strong>Client :</strong> <?= htmlspecialchars($commande['nom_client'] ?? 'Client inconnu') ?></p>

                <p>
                    <strong>Statut :</strong>
                    <span class="badge <?= htmlspecialchars($classeStatut) ?>">
                        <?= htmlspecialchars($statut) ?>
                    </span>
                </p>

                <p><strong>Articles :</strong></p>
                <ul>
                    <?php if (!empty($commande['plats']) && is_array($commande['plats'])): ?>
                        <?php foreach ($commande['plats'] as $plat): ?>
                            <li>
                                <?= htmlspecialchars($plat['nom'] ?? 'Plat inconnu') ?>
                                x<?= htmlspecialchars($plat['quantite'] ?? 1) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucun article enregistré</li>
                    <?php endif; ?>
                </ul>

                <p><strong>Total :</strong> <?= number_format((float)($commande['total'] ?? 0), 2, ",", " ") ?> €</p>

                <!-- actions (phase 2 affichage uniquement) -->
                <div class="actions">

                    <select>
                        <option>Changer statut</option>
                        <option>En attente</option>
                        <option>À préparer</option>
                        <option>En préparation</option>
                        <option>En livraison</option>
                        <option>Livrée</option>
                    </select>

                    <select>
                        <option>Attribuer livreur</option>
                        <option>Livreur #1</option>
                        <option>Livreur #2</option>
                    </select>

                    <button type="button">Valider</button>

                </div>

            </div>

        </div>

    <?php endif; ?>

<?php endforeach; ?>

</main>

</body>
</html>