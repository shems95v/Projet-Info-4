<?php
session_start();
require_once "auth.php";
verifierSession(['restaurateur', 'admin']);

/* charger commandes et utilisateurs */
$commandes = json_decode(file_get_contents("data/commandes.json"), true);
$users = json_decode(file_get_contents("data/users.json"), true);

/* récupérer uniquement les livreurs */
$livreurs = array_filter($users, function($u) {
    return ($u['role'] ?? '') === 'livreur' && ($u['actif'] ?? false);
});

$user = $_SESSION['user'];

/* statuts possibles dans l'ordre */
$statutsSuivants = [
    "En attente"    => "En préparation",
    "En préparation"=> "Prête",
    "Prête"         => "En livraison",
    "En livraison"  => "Livrée",
    "Livrée"        => null
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes</title>
    <link rel="stylesheet" href="restaurateur.css">
    <script src="modeSombre.js"></script>
</head>

<body>

<header>
    <h1>Commandes du restaurant</h1>
    <button id="btn-dark-mode">Changer thème</button>
    <a href="profil.php">← Retour</a>
</header>

<main>

<?php foreach ($commandes as $commande): ?>
    <?php if (($commande['restaurant_id'] ?? 0) == 1): ?>

        <?php
        $statut = $commande['statut_commande'] ?? 'Statut inconnu';
        $dateAffichee = $commande['date_creation'] ?? 'Date inconnue';
        $statutSuivant = $statutsSuivants[$statut] ?? null;

        /* classe CSS pour le badge */
        $classeStatut = strtolower($statut);
        $classeStatut = str_replace(
            [' ', 'é', 'è', 'ê', 'à', 'ù'],
            ['-', 'e', 'e', 'e', 'a', 'u'],
            $classeStatut
        );
        ?>

        <div class="commande-card" id="commande-<?= $commande['id'] ?>">

            <div class="commande-header">
                <span>#<?= htmlspecialchars($commande['id']) ?></span>
                <span><?= htmlspecialchars($dateAffichee) ?></span>
            </div>

            <div class="commande-body">

                <p><strong>Client :</strong> <?= htmlspecialchars($commande['nom_client'] ?? 'Client inconnu') ?></p>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse'] ?? 'Non renseignée') ?></p>
                <p><strong>Mode :</strong> <?= htmlspecialchars($commande['mode_recuperation'] ?? '') ?></p>

                <p>
                    <strong>Statut :</strong>
                    <span class="badge <?= htmlspecialchars($classeStatut) ?>" id="badge-<?= $commande['id'] ?>">
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
                                — <?= number_format((float)($plat['prix'] ?? 0), 2, ",", " ") ?> €
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Aucun article enregistré</li>
                    <?php endif; ?>
                </ul>

                <p><strong>Total :</strong> <?= number_format((float)($commande['total'] ?? 0), 2, ",", " ") ?> €</p>

                <!-- Actions -->
                <div class="actions">

                    <?php if ($statutSuivant): ?>
                        <!-- Sélection du livreur si on passe en livraison -->
                        <?php if ($statutSuivant === "En livraison"): ?>
                            <select id="livreur-<?= $commande['id'] ?>">
                                <option value="">Choisir un livreur</option>
                                <?php foreach ($livreurs as $livreur): ?>
                                    <option value="<?= htmlspecialchars($livreur['id']) ?>"
                                        <?= ($commande['livreur_id'] == $livreur['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($livreur['prenom'] . ' ' . $livreur['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <button 
                            class="btn-statut"
                            onclick="changerStatut(<?= $commande['id'] ?>, '<?= addslashes($statutSuivant) ?>', this)">
                            → <?= htmlspecialchars($statutSuivant) ?>
                        </button>
                    <?php else: ?>
                        <p><em>Commande terminée</em></p>
                    <?php endif; ?>

                    <!-- Message de retour -->
                    <span class="msg-retour" id="msg-<?= $commande['id'] ?>"></span>

                </div>

            </div>

        </div>

    <?php endif; ?>
<?php endforeach; ?>

</main>

<script>
function changerStatut(idCommande, nouveauStatut, bouton) {
    /* si on passe en livraison on vérifie qu'un livreur est sélectionné */
    var livreurId = null;
    var selectLivreur = document.getElementById("livreur-" + idCommande);
    if (selectLivreur) {
        livreurId = selectLivreur.value;
        if (!livreurId) {
            alert("Veuillez choisir un livreur avant de passer en livraison !");
            return;
        }
    }

    fetch("maj_commande.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: idCommande,
            statut: nouveauStatut,
            livreur_id: livreurId
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        var msg = document.getElementById("msg-" + idCommande);
        if (data.succes) {
            /* mettre à jour le badge */
            var badge = document.getElementById("badge-" + idCommande);
            badge.textContent = nouveauStatut;

            msg.textContent = "✓ Statut mis à jour !";
            msg.style.color = "green";

            /* désactiver le bouton si statut final */
            if (nouveauStatut === "Livrée") {
                bouton.disabled = true;
                bouton.textContent = "Commande terminée";
            } else {
                bouton.disabled = true;
                msg.textContent = "✓ Recharge la page pour continuer";
            }
        } else {
            bouton.disabled = true;
        bouton.textContent = "Commande terminée";
        }
    })
    .catch(function() {
        var msg = document.getElementById("msg-" + idCommande);
        msg.textContent = "Erreur de connexion";
        msg.style.color = "red";
    });
}
</script>

</body>
</html>