<?php
session_start();
require_once "auth.php";
verifierSession(['restaurateur', 'admin']);

$users    = json_decode(file_get_contents("data/users.json"), true);
$commandes = json_decode(file_get_contents("data/commandes.json"), true);

/* Livreurs disponibles */
$livreurs = array_values(array_filter($users, function($u) {
    return ($u['role'] ?? '') === 'livreur' && ($u['actif'] ?? false);
}));

$user = $_SESSION['user'];

$transitions = [
    'Payée'          => 'En préparation',
    'En préparation' => 'Prête',
    'Prête'          => 'En livraison',
];

/* Ordre d'affichage des sections */
$sections = [
    'Payée'          => ['label' => '💳 Payées — à lancer',    'classe' => 'payee'],
    'En préparation' => ['label' => '🍳 En préparation',       'classe' => 'en-preparation'],
    'Prête'          => ['label' => '✅ Prêtes — à envoyer',   'classe' => 'prete'],
    'En livraison'   => ['label' => '🚚 En livraison',         'classe' => 'en-livraison'],
    'Livrée'         => ['label' => '📦 Livrées',              'classe' => 'livree'],
    'En attente'     => ['label' => '⏳ En attente (non payées)', 'classe' => 'en-attente'],
];

/* Regroupement par statut */
$parStatut = [];
foreach ($commandes as $c) {
    if (($c['restaurant_id'] ?? 0) != 1) continue;
    $s = $c['statut_commande'] ?? 'En attente';
    $parStatut[$s][] = $c;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes — Restaurateur</title>
    <link rel="stylesheet" href="restaurateur.css">
    <script src="modeSombre.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="header-left">
        <h1>🍽️ L'Atlas des Saveurs</h1>
        <span class="header-sub">Espace restaurateur</span>
    </div>
    <nav class="header-nav">
        <button id="btn-dark-mode">Changer thème</button>
        <a href="profil.php">Mon profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<main>
    <h2 class="page-titre">Gestion des commandes</h2>

    <?php foreach ($sections as $statut => $info):
        $liste = $parStatut[$statut] ?? [];
        if (empty($liste)) continue;
    ?>
    <section class="section-statut">
        <h3 class="section-titre <?= $info['classe'] ?>"><?= $info['label'] ?> <span class="compteur"><?= count($liste) ?></span></h3>

        <?php foreach ($liste as $commande):
            $sid         = $commande['id'];
            $statutActuel = $commande['statut_commande'] ?? 'En attente';
            $suivant      = $transitions[$statutActuel] ?? null;
            $livreurAssigne = $commande['livreur_id'] ?? null;

            /* Nom du livreur si assigné */
            $nomLivreur = '';
            foreach ($users as $u) {
                if ((string)$u['id'] === (string)$livreurAssigne) {
                    $nomLivreur = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''));
                    break;
                }
            }
        ?>
        <div class="commande-card" id="card-<?= $sid ?>">

            <div class="commande-header">
                <span class="commande-id">#<?= htmlspecialchars($sid) ?></span>
                <span class="commande-date"><?= htmlspecialchars($commande['date_creation'] ?? 'Date inconnue') ?></span>
                <span class="badge <?= $info['classe'] ?>" id="badge-<?= $sid ?>"><?= htmlspecialchars($statutActuel) ?></span>
            </div>

            <div class="commande-body">
                <div class="commande-info-grid">
                    <div>
                        <p><strong>Client :</strong> <?= htmlspecialchars($commande['nom_client'] ?? '—') ?></p>
                        <p><strong>Tél :</strong> <?= htmlspecialchars($commande['telephone'] ?? '—') ?></p>
                    </div>
                    <div>
                        <p><strong>Mode :</strong> <?= htmlspecialchars($commande['mode_recuperation'] ?? '—') ?></p>
                        <p><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse'] ?? '—') ?></p>
                    </div>
                </div>

                <?php if (!empty($commande['plats'])): ?>
                <ul class="liste-plats">
                    <?php foreach ($commande['plats'] as $plat): ?>
                    <li>
                        <span class="plat-nom"><?= htmlspecialchars($plat['nom'] ?? '?') ?></span>
                        <span class="plat-qte">×<?= (int)($plat['quantite'] ?? 1) ?></span>
                        <span class="plat-prix"><?= number_format((float)($plat['prix'] ?? 0), 2, ',', ' ') ?> €</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="aucun-plat">Aucun article enregistré</p>
                <?php endif; ?>

                <p class="total-ligne"><strong>Total :</strong> <?= number_format((float)($commande['total'] ?? 0), 2, ',', ' ') ?> €</p>

                <?php if ($nomLivreur): ?>
                <p class="livreur-info">🚴 Livreur : <strong><?= htmlspecialchars($nomLivreur) ?></strong></p>
                <?php endif; ?>

                <!-- Zone d'actions -->
                <div class="actions" id="actions-<?= $sid ?>">
                    <?php if ($suivant): ?>

                        <?php if ($suivant === 'En livraison'): ?>
                        <!-- Sélection livreur obligatoire avant de passer en livraison -->
                        <select id="livreur-<?= $sid ?>" class="select-livreur">
                            <option value="">— Choisir un livreur —</option>
                            <?php foreach ($livreurs as $livreur): ?>
                            <option value="<?= htmlspecialchars($livreur['id']) ?>"
                                <?= ((string)$livreurAssigne === (string)$livreur['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(trim(($livreur['prenom'] ?? '') . ' ' . ($livreur['nom'] ?? ''))) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>

                        <button
                            class="btn-statut btn-<?= strtolower(str_replace([' ', 'é','è','ê'], ['-','e','e','e'], $suivant)) ?>"
                            data-id="<?= $sid ?>"
                            data-suivant="<?= htmlspecialchars($suivant) ?>"
                            onclick="changerStatut(<?= $sid ?>, '<?= addslashes($suivant) ?>', this)">
                            → <?= htmlspecialchars($suivant) ?>
                        </button>

                    <?php else: ?>
                        <span class="commande-terminee">✔ Commande terminée</span>
                    <?php endif; ?>

                    <span class="msg-retour" id="msg-<?= $sid ?>"></span>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </section>
    <?php endforeach; ?>

    <?php if (empty(array_filter($parStatut))): ?>
    <p class="aucune-commande">Aucune commande pour ce restaurant.</p>
    <?php endif; ?>

</main>

<!-- Données livreurs pour JS -->
<script>
const LIVREURS = <?= json_encode($livreurs, JSON_UNESCAPED_UNICODE) ?>;

/* Transitions côté JS */
const TRANSITIONS = {
    "Payée":          "En préparation",
    "En préparation": "Prête",
    "Prête":          "En livraison",
    "En livraison":   null,
    "Livrée":         null
};

/* Labels CSS des statuts */
const CLASSES_STATUT = {
    "En attente":     "en-attente",
    "Payée":          "payee",
    "En préparation": "en-preparation",
    "Prête":          "prete",
    "En livraison":   "en-livraison",
    "Livrée":         "livree"
};

function changerStatut(idCommande, nouveauStatut, bouton) {

    /* Récupération éventuelle du livreur */
    let livreurId = null;
    const selectLivreur = document.getElementById("livreur-" + idCommande);
    if (selectLivreur) {
        livreurId = selectLivreur.value;
        if (!livreurId) {
            alert("Veuillez choisir un livreur avant de passer la commande en livraison !");
            return;
        }
    }

    /* Désactiver le bouton pendant la requête */
    bouton.disabled = true;
    bouton.textContent = "…";

    fetch("maj_commande.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: idCommande, statut: nouveauStatut, livreur_id: livreurId })
    })
    .then(r => r.json())
    .then(data => {
        const msg   = document.getElementById("msg-" + idCommande);
        const badge = document.getElementById("badge-" + idCommande);
        const zone  = document.getElementById("actions-" + idCommande);

        if (!data.succes) {
            msg.textContent = "❌ " + (data.erreur || "Erreur inconnue");
            msg.style.color = "red";
            bouton.disabled = false;
            bouton.textContent = "→ " + nouveauStatut;
            return;
        }

        /* Mise à jour du badge */
        const ancienneClasse = badge.className.replace("badge", "").trim();
        badge.classList.remove(ancienneClasse);
        badge.classList.add(CLASSES_STATUT[nouveauStatut] || "");
        badge.textContent = nouveauStatut;

        msg.textContent = "✓ Statut mis à jour !";
        msg.style.color = "green";

        /* Si un livreur vient d'être assigné, afficher son nom */
        if (livreurId) {
            const livreur = LIVREURS.find(l => String(l.id) === String(livreurId));
            if (livreur) {
                const body = document.querySelector("#card-" + idCommande + " .commande-body");
                let livreurInfo = body.querySelector(".livreur-info");
                if (!livreurInfo) {
                    livreurInfo = document.createElement("p");
                    livreurInfo.className = "livreur-info";
                    body.querySelector(".total-ligne").insertAdjacentElement("afterend", livreurInfo);
                }
                livreurInfo.innerHTML = "🚴 Livreur : <strong>" +
                    livreur.prenom + " " + livreur.nom + "</strong>";
            }
        }

        /* Reconstruire la zone d'actions pour le statut suivant */
        const prochainStatut = TRANSITIONS[nouveauStatut];
        zone.innerHTML = "";

        if (prochainStatut) {
            /* Si le prochain est "En livraison", ajouter le select livreur */
            if (prochainStatut === "En livraison") {
                const sel = document.createElement("select");
                sel.id = "livreur-" + idCommande;
                sel.className = "select-livreur";
                sel.innerHTML = '<option value="">— Choisir un livreur —</option>' +
                    LIVREURS.map(l =>
                        '<option value="' + l.id + '">' + l.prenom + " " + l.nom + "</option>"
                    ).join("");
                zone.appendChild(sel);
            }

            const btnSuivant = document.createElement("button");
            const classeBtn  = "btn-" + prochainStatut.toLowerCase().replace(/ /g, "-").replace(/[éèê]/g, "e");
            btnSuivant.className = "btn-statut " + classeBtn;
            btnSuivant.textContent = "→ " + prochainStatut;
            btnSuivant.onclick = function() { changerStatut(idCommande, prochainStatut, this); };
            zone.appendChild(btnSuivant);

        } else {
            const fin = document.createElement("span");
            fin.className = "commande-terminee";
            fin.textContent = "✔ Commande terminée";
            zone.appendChild(fin);
        }

        /* Ajouter le span msg */
        const newMsg = document.createElement("span");
        newMsg.className = "msg-retour";
        newMsg.id = "msg-" + idCommande;
        zone.appendChild(newMsg);
    })
    .catch(() => {
        const msg = document.getElementById("msg-" + idCommande);
        msg.textContent = "❌ Erreur de connexion";
        msg.style.color = "red";
        bouton.disabled = false;
        bouton.textContent = "→ " + nouveauStatut;
    });
}
</script>
</body>
</html>