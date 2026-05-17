<?php
session_start();

/* Chargement initial des plats (affichage sans JS) */
$plats = json_decode(file_get_contents("data/plats.json"), true);
if (!is_array($plats)) $plats = [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - L'Atlas des Saveurs</title>
    <link rel="stylesheet" href="presentation.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="modeSombre.js" defer></script>
</head>

<body>

<header>
    <h1>Menu Gourmand</h1>
    <button id="btn-dark-mode">Changer thème</button>

    <!-- Barre de recherche -->
    <div class="conteneur-recherche">
        <input
            type="search"
            id="recherche-plats"
            placeholder="Rechercher un plat…"
            aria-label="Rechercher un plat"
            autocomplete="off"
        >
    </div>

    <nav class="navigation">
        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
            <a href="admin.php">Admin</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'livreur'): ?>
            <a href="livraison.php">Livraison</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'restaurateur'): ?>
            <a href="commandes.php">Commandes</a>
        <?php endif; ?>
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        <?php else: ?>
            <a href="profil.php">Mon profil</a>
            <a href="panier.php">Panier</a>
            <a href="logout.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>

<main class="conteneur-principal">

    <!-- ===== Panneau de filtres ===== -->
    <aside class="filtres">
        <h3>Filtrer par</h3>

        <div class="groupe-filtre">
            <h4>Types de plats</h4>
            <label><input type="checkbox" name="type" value="entree"> Entrées</label>
            <label><input type="checkbox" name="type" value="plat"> Plats principaux</label>
            <label><input type="checkbox" name="type" value="dessert"> Desserts</label>
        </div>

        <div class="groupe-filtre">
            <h4>Saveurs</h4>
            <label><input type="checkbox" name="flavor" value="epice"> Épicé</label>
            <label><input type="checkbox" name="flavor" value="sucre-sale"> Sucré-Salé</label>
            <label><input type="checkbox" name="flavor" value="sucre"> Sucré</label>
            <label><input type="checkbox" name="flavor" value="frais"> Frais</label>
        </div>

        <div class="groupe-filtre">
            <h4>Régimes alimentaires</h4>
            <label><input type="checkbox" id="filtre-gluten">   Sans Gluten</label>
            <label><input type="checkbox" id="filtre-lactose">  Sans Lactose</label>
            <label><input type="checkbox" id="filtre-vegetarien"> Végétarien</label>
        </div>

        <!-- Tri -->
        <div class="groupe-filtre">
            <h4>Trier par</h4>
            <select id="select-tri" class="select-tri">
                <option value="">Par défaut</option>
                <option value="prix_asc">Prix croissant</option>
                <option value="prix_desc">Prix décroissant</option>
                <option value="popularite">Les plus commandés</option>
                <option value="nom_asc">Nom (A → Z)</option>
            </select>
        </div>

        <!-- Compteur de résultats -->
        <p class="compteur-resultats">
            <span id="compteur-plats"><?= count($plats) ?> plat<?= count($plats) > 1 ? 's' : '' ?></span>
        </p>

        <button class="bouton-reinitialiser">Réinitialiser les filtres</button>
    </aside>

    <!-- ===== Grille des produits ===== -->
    <section class="grille-produits">
        <?php if (empty($plats)): ?>
            <p class="message-filtre">Aucun plat disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($plats as $plat): ?>
                <article
                    class="carte-produit"
                    data-id="<?= (int)($plat['id'] ?? 0) ?>"
                    data-prix="<?= (float)($plat['prix'] ?? 0) ?>"
                    data-nb="<?= (int)($plat['nbCommandes'] ?? 0) ?>"
                >
                    <img
                        src="<?= htmlspecialchars($plat['image'] ?? 'images/default.jpg') ?>"
                        alt="<?= htmlspecialchars($plat['nom'] ?? 'Plat') ?>"
                        class="image-produit"
                        onerror="this.src='images/default.jpg'"
                    >
                    <div class="infos-produit">
                        <span class="categorie">
                            <?= htmlspecialchars($plat['categorie'] ?? '') ?>
                        </span>
                        <h3><?= htmlspecialchars($plat['nom'] ?? 'Plat inconnu') ?></h3>
                        <p class="description">
                            <?= htmlspecialchars($plat['description'] ?? '') ?>
                        </p>
                        <div class="prix">
                            <?= number_format((float)($plat['prix'] ?? 0), 2, ',', ' ') ?> €
                        </div>
                        <form method="POST" action="panier.php">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="plat_id" value="<?= (int)($plat['id'] ?? 0) ?>">
                            <input type="number" name="quantite" value="1" min="1" max="20">
                            <button type="submit">Ajouter au panier</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

<!-- Chargé en dernier pour que le DOM soit prêt -->
<script src="filtres.js"></script>

</body>
</html>