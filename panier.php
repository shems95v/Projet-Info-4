<?php
session_start();
require_once("includes/functions.php");

if (!isset($_SESSION["panier"])) {
    $_SESSION["panier"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "ajouter") {
        $platId = (int)($_POST["plat_id"] ?? 0);
        $quantite = (int)($_POST["quantite"] ?? 1);

        if ($platId > 0 && $quantite > 0) {
            if (!isset($_SESSION["panier"][$platId])) {
                $_SESSION["panier"][$platId] = 0;
            }
            $_SESSION["panier"][$platId] += $quantite;
        }
    }

    if ($action === "modifier") {
        foreach ($_POST["quantites"] ?? [] as $platId => $quantite) {
            $platId = (int)$platId;
            $quantite = (int)$quantite;

            if ($quantite <= 0) {
                unset($_SESSION["panier"][$platId]);
            } else {
                $_SESSION["panier"][$platId] = $quantite;
            }
        }
    }

    if ($action === "supprimer") {
        $platId = (int)($_POST["plat_id"] ?? 0);
        unset($_SESSION["panier"][$platId]);
    }
}

$panier = $_SESSION["panier"];
$total = calculerTotalPanier($panier);

$nombreArticles = 0;
foreach ($panier as $quantite) {
    $nombreArticles += (int)$quantite;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon panier - L'Atlas des Saveurs</title>
<link rel="stylesheet" href="presentation.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
.panier-box {
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.panier-box h2 {
    margin-bottom: 20px;
    color: #2563eb;
    text-align: center;
    font-family: 'Playfair Display', Georgia, serif;
}

.panier-resume {
    margin-bottom: 20px;
    color: #444;
    font-weight: 600;
}

.panier-ligne {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
    gap: 15px;
}

.panier-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.total-box {
    margin-top: 20px;
    font-size: 1.2em;
    font-weight: bold;
    color: #2563eb;
}

.btn-principal {
    display: inline-block;
    padding: 12px 18px;
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

.btn-principal:hover {
    background: #1d4ed8;
}

.btn-secondaire {
    padding: 10px 14px;
    border: 1px solid #2563eb;
    background: #fff;
    color: #2563eb;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
}

.btn-secondaire:hover {
    background: #eff6ff;
}

.btn-supprimer {
    padding: 8px 12px;
    border: 1px solid #dc2626;
    background: #fff;
    color: #dc2626;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

.btn-supprimer:hover {
    background: #fef2f2;
}

.actions-bas {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.message-vide {
    text-align: center;
    padding: 20px 0;
}

@media (max-width: 700px) {
    .panier-ligne {
        flex-direction: column;
        align-items: flex-start;
    }

    .panier-actions {
        width: 100%;
    }
}
</style>
</head>
<body>

<header>
    <h1>L'Atlas des Saveurs</h1>
     <nav class="navigation">
            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
                echo '<a href="admin.php">Admin</a>';
            }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur') {
                echo '<a href="livraison.php">Livraison</a>';
             }
             if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'restaurateur') {
                echo '<a href="commande.php">commande</a>';
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
    <div class="panier-box">
        <h2>Mon panier</h2>

        <?php if (empty($panier)): ?>
            <div class="message-vide">
                <p>Votre panier est vide.</p>
                <p style="margin-top: 15px;">
                    <a class="btn-principal" href="presentation.php">Retour au menu</a>
                </p>
            </div>
        <?php else: ?>
            <p class="panier-resume">
                Nombre d’articles : <?= $nombreArticles ?>
            </p>

            <form method="POST" action="panier.php">
                <input type="hidden" name="action" value="modifier">

                <?php foreach ($panier as $platId => $quantite): ?>
                    <?php $plat = trouverPlatParId($platId); ?>

                    <?php if ($plat): ?>
                        <div class="panier-ligne">
                            <div>
                                <strong><?= htmlspecialchars($plat["nom"]) ?></strong><br>
                                <?= number_format((float)$plat["prix"], 2, ",", " ") ?> €
                            </div>

                            <div class="panier-actions">
                                <label for="qte-<?= $platId ?>">Qté :</label>
                                <input
                                    id="qte-<?= $platId ?>"
                                    type="number"
                                    name="quantites[<?= $platId ?>]"
                                    value="<?= (int)$quantite ?>"
                                    min="0"
                                    max="20"
                                >

                                <span>
                                    <?= number_format((float)$plat["prix"] * (int)$quantite, 2, ",", " ") ?> €
                                </span>

                           
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="total-box">
                    Total : <?= number_format((float)$total, 2, ",", " ") ?> €
                </div>

                <div class="actions-bas">
                    <button class="btn-secondaire" type="submit">Mettre à jour le panier</button>
                    <a class="btn-secondaire" href="presentation.php">Continuer mes achats</a>
                </div>
            </form>

            <?php foreach ($panier as $platId => $quantite): ?>
                <?php $plat = trouverPlatParId($platId); ?>
                <?php if ($plat): ?>
                    <form method="POST" action="panier.php" style="display:inline-block; margin-top:10px; margin-right:10px;">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="plat_id" value="<?= $platId ?>">
                        <button class="btn-supprimer" type="submit">
                            Supprimer <?= htmlspecialchars($plat["nom"]) ?>
                        </button>
                    </form>
                <?php endif; ?>
            <?php endforeach; ?>

            <form method="POST" action="valider_commande.php" style="margin-top:20px;">
                <button class="btn-principal" type="submit">Valider la commande</button>
            </form>
        <?php endif; ?>
    </div>
</main>

</body>
</html>