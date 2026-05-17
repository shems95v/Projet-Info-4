<?php
session_start();
require_once("includes/functions.php");

/* ── Sécurité : client connecté ─────────────────────────────────────── */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header("Location: connexion.php");
    exit();
}

$user = $_SESSION['user'];

/* ── Récupération de la commande ─────────────────────────────────────── */
$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($commande_id === 0) { header("Location: commandes.php"); exit(); }

$commandes   = lireJSON("data/commandes.json");
$index       = null;
$commande    = null;

foreach ($commandes as $i => $c) {
    if ((int)$c['id'] === $commande_id) { $index = $i; $commande = $c; break; }
}

/* ── Vérifications ───────────────────────────────────────────────────── */
if ($commande === null) { header("Location: commandes.php"); exit(); }

// Appartient au client connecté
if ((string)($commande['user_id'] ?? '') !== (string)$user['id']) {
    header("Location: commandes.php"); exit();
}

// Statut = "Payée" uniquement (pas encore en préparation)
if (($commande['statut_commande'] ?? '') !== 'Payée') {
    header("Location: commandes.php"); exit();
}

/* ── Traitement du formulaire de modification ────────────────────────── */
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $plats_catalogue = lireJSON("data/plats.json");
    $plats_index     = [];
    foreach ($plats_catalogue as $p) { $plats_index[$p['id']] = $p; }

    /* Reconstruire la liste des plats depuis les quantités postées */
    $nouveaux_plats = [];
    $nouveau_total  = 0.0;

    // Plats existants dont on modifie la quantité
    foreach ($_POST['quantites'] ?? [] as $plat_id => $qte) {
        $plat_id = (int)$plat_id;
        $qte     = (int)$qte;
        if ($qte <= 0) continue;                       // supprimé
        if (!isset($plats_index[$plat_id])) continue;
        $p = $plats_index[$plat_id];
        $nouveaux_plats[] = ['id' => $p['id'], 'nom' => $p['nom'],
                             'prix' => $p['prix'], 'quantite' => $qte];
        $nouveau_total += $p['prix'] * $qte;
    }

    // Nouveau plat à ajouter
    $ajout_id  = (int)($_POST['ajout_plat_id'] ?? 0);
    $ajout_qte = (int)($_POST['ajout_quantite'] ?? 0);
    if ($ajout_id > 0 && $ajout_qte > 0 && isset($plats_index[$ajout_id])) {
        $p = $plats_index[$ajout_id];
        // Fusionner si déjà présent
        $trouve = false;
        foreach ($nouveaux_plats as &$np) {
            if ($np['id'] === $p['id']) { $np['quantite'] += $ajout_qte;
                $nouveau_total += $p['prix'] * $ajout_qte; $trouve = true; break; }
        }
        unset($np);
        if (!$trouve) {
            $nouveaux_plats[] = ['id' => $p['id'], 'nom' => $p['nom'],
                                 'prix' => $p['prix'], 'quantite' => $ajout_qte];
            $nouveau_total += $p['prix'] * $ajout_qte;
        }
    }

    if (empty($nouveaux_plats)) {
        $erreur = "La commande ne peut pas être vide.";
    } else {

        $ancien_total  = (float)$commande['total'];
        $nouveau_total = round($nouveau_total, 2);
        $difference    = round($nouveau_total - $ancien_total, 2);

        // Sauvegarde des nouveaux plats et du nouveau total
        $commandes[$index]['plats'] = $nouveaux_plats;
        $commandes[$index]['total'] = $nouveau_total;

        if ($difference > 0) {
            /* ── Commande plus chère : paiement supplémentaire ── */
            $transaction_supp = genererTransactionId();
            $commandes[$index]['transaction_supplement'] = $transaction_supp;
            $commandes[$index]['montant_supplement']     = $difference;
            $commandes[$index]['statut_supplement']      = 'en_attente';

            ecrireJSON("data/commandes.json", $commandes);

            $_SESSION['supplement_transaction'] = $transaction_supp;
            $_SESSION['supplement_commande_id'] = $commande_id;
            $_SESSION['supplement_montant']      = $difference;

            header("Location: paiement_supplement.php");
            exit();

        } else {
            /* ── Commande moins chère ou identique : ticket de réduction ── */
            if ($difference < 0) {
                $reduction = abs($difference);
                // On crée / cumule un ticket de réduction dans la commande
                $commandes[$index]['ticket_reduction'] = round(
                    ($commandes[$index]['ticket_reduction'] ?? 0) + $reduction, 2
                );
            }

            ecrireJSON("data/commandes.json", $commandes);
            header("Location: commandes.php?modif=ok");
            exit();
        }
    }
}

/* ── Chargement du catalogue pour le formulaire ─────────────────────── */
$plats_catalogue = lireJSON("data/plats.json");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la commande #<?= $commande_id ?> - L'Atlas des Saveurs</title>
    <link rel="stylesheet" href="modifier_commande.css">
    <script src="modeSombre.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <h1>L'Atlas des Saveurs</h1>
    <button id="btn-dark-mode">Changer thème</button>
    <nav class="navigation">
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="commandes.php">Mes commandes</a>
        <a href="profil.php">Mon profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main>
<div class="panier-box">
    <h2>✏️ Modifier la commande #<?= htmlspecialchars($commande_id) ?></h2>
    <p>Vous pouvez modifier cette commande tant qu'elle n'est pas encore en préparation.</p>

    <?php if ($erreur): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php
    /* Prix initiaux pour le JS */
    $prix_catalogue = [];
    foreach ($plats_catalogue as $p) {
        $prix_catalogue[$p['id']] = (float)$p['prix'];
    }
    $total_initial = (float)$commande['total'];
    ?>

    <form method="POST" action="modifier_commande.php?id=<?= $commande_id ?>" id="form-modif">

        <!-- ── Plats existants ── -->
        <?php foreach ($commande['plats'] as $plat): ?>
        <div class="panier-ligne">
            <div>
                <strong><?= htmlspecialchars($plat['nom']) ?></strong><br>
                <?= number_format((float)$plat['prix'], 2, ',', ' ') ?> € / unité
            </div>
            <div class="panier-actions">
                <label>Qté :</label>
                <input
                    type="number"
                    name="quantites[<?= (int)$plat['id'] ?>]"
                    value="<?= (int)$plat['quantite'] ?>"
                    min="0" max="20"
                    data-prix="<?= (float)$plat['prix'] ?>"
                    class="qte-input"
                >
                <span class="sous-total">
                    <?= number_format((float)$plat['prix'] * (int)$plat['quantite'], 2, ',', ' ') ?> €
                </span>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── Ajouter un nouveau plat ── -->
        <div class="section-ajout">
            <h3>➕ Ajouter un plat</h3>
            <div class="ajout-ligne">
                <select name="ajout_plat_id" id="ajout-plat-select">
                    <option value="0">-- Choisir un plat --</option>
                    <?php foreach ($plats_catalogue as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"
                            data-prix="<?= (float)$p['prix'] ?>">
                        <?= htmlspecialchars($p['nom']) ?>
                        (<?= number_format((float)$p['prix'], 2, ',', ' ') ?> €)
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="ajout_quantite" id="ajout-quantite"
                       value="1" min="1" max="20">
            </div>
        </div>

        <!-- ── Récapitulatif dynamique ── -->
        <div class="total-box">
            Nouveau total : <span id="nouveau-total">
                <?= number_format($total_initial, 2, ',', ' ') ?>
            </span> €
        </div>
        <div id="diff-zone" style="margin-top:8px; font-size:1em;"></div>
        <div class="info-ticket" id="info-ticket" style="display:none;">
            🎟️ La commande étant moins chère, vous recevrez un <strong>ticket de réduction</strong>
            de <span id="montant-ticket">0,00</span> € utilisable sur une prochaine commande.
        </div>

        <!-- Données pour JS -->
        <input type="hidden" id="total-initial" value="<?= $total_initial ?>">
        <input type="hidden" id="prix-json"
               value='<?= htmlspecialchars(json_encode($prix_catalogue), ENT_QUOTES) ?>'>

        <div class="actions-bas" style="margin-top:20px;">
            <button class="btn-principal" type="submit">Valider les modifications</button>
            <a class="btn-secondaire" href="commandes.php">Annuler</a>
        </div>

    </form>
</div>
</main>

<script>
(function () {
    const prixCatalogue  = JSON.parse(document.getElementById('prix-json').value);
    const totalInitial   = parseFloat(document.getElementById('total-initial').value);
    const newTotalEl     = document.getElementById('nouveau-total');
    const diffZone       = document.getElementById('diff-zone');
    const infoTicket     = document.getElementById('info-ticket');
    const montantTicket  = document.getElementById('montant-ticket');

    function recalculer() {
        let total = 0;

        // Plats existants
        document.querySelectorAll('.qte-input').forEach(input => {
            const prix = parseFloat(input.dataset.prix) || 0;
            const qte  = Math.max(0, parseInt(input.value) || 0);
            total += prix * qte;
        });

        // Ajout
        const selectAjout = document.getElementById('ajout-plat-select');
        const qteAjout    = parseInt(document.getElementById('ajout-quantite').value) || 0;
        const opt         = selectAjout.selectedOptions[0];
        if (opt && opt.value !== '0' && qteAjout > 0) {
            const prixAjout = parseFloat(opt.dataset.prix) || 0;
            total += prixAjout * qteAjout;
        }

        total = Math.round(total * 100) / 100;
        const diff = Math.round((total - totalInitial) * 100) / 100;

        newTotalEl.textContent = total.toFixed(2).replace('.', ',') + ' €';

        if (diff > 0.001) {
            diffZone.innerHTML = `<span class="diff-positif">
                ↑ Supplément à payer : +${diff.toFixed(2).replace('.', ',')} €
            </span>`;
            infoTicket.style.display = 'none';
        } else if (diff < -0.001) {
            const reduction = Math.abs(diff);
            diffZone.innerHTML = `<span class="diff-negatif">
                ↓ Commande moins chère de ${reduction.toFixed(2).replace('.', ',')} €
            </span>`;
            montantTicket.textContent = reduction.toFixed(2).replace('.', ',');
            infoTicket.style.display = 'block';
        } else {
            diffZone.innerHTML = '<span style="color:#6b7280;">Montant identique à la commande initiale.</span>';
            infoTicket.style.display = 'none';
        }
    }

    document.querySelectorAll('.qte-input').forEach(i => i.addEventListener('input', recalculer));
    document.getElementById('ajout-plat-select').addEventListener('change', recalculer);
    document.getElementById('ajout-quantite').addEventListener('input', recalculer);

    recalculer();
})();
</script>
</body>
</html>