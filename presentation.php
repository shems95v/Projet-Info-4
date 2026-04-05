<?php
session_start();

/* Charger les plats */
$plats = json_decode(file_get_contents("data/plats.json"), true);
if (!is_array($plats)) {
    $plats = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - L'Atlas des Saveurs</title>
<link rel="stylesheet" href="presentation.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <h1>Menu Gourmand</h1>

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

<main class="conteneur-principal">

<aside class="filtres">
            
            <h3>Filtrer par</h3>
            
            <div class="groupe-filtre">
                <h4>Types de plats</h4>
                <label><input type="checkbox" name="type" value="entree"> Entrées</label>
                <label><input type="checkbox" name="type" value="plat"> Plats</label>
                <label><input type="checkbox" name="type" value="dessert"> Desserts</label>
            </div>

            <div class="groupe-filtre">
                <h4>Saveurs</h4>
                <label><input type="checkbox" name="flavor" value="epice"> Épicé</label>
                <label><input type="checkbox" name="flavor" value="sucre"> Sucré-Salé</label>
                <label><input type="checkbox" name="flavor" value="frais"> Frais</label>
            </div>

            <div class="groupe-filtre">
                <h4>Allergènes (Sans)</h4>
                <label><input type="checkbox" name="allergen" value="gluten"> Sans Gluten</label>
                <label><input type="checkbox" name="allergen" value="lactose"> Sans Lactose</label>
            </div>
            
            <button class="bouton-reinitialiser">Réinitialiser</button>
        </aside>

    <section class="grille-produits">
        <?php if (empty($plats)): ?>
            <p>Aucun plat disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($plats as $plat): ?>
                <article class="carte-produit">

                    <img
                        src="<?= htmlspecialchars($plat['image'] ?? 'images/default.jpg') ?>"
                        alt="<?= htmlspecialchars($plat['nom'] ?? 'Plat') ?>"
                        class="image-produit"
                    >

                    <div class="infos-produit">
                        <span class="categorie">
                            <?= htmlspecialchars($plat['categorie'] ?? 'Catégorie') ?>
                        </span>

                        <h3><?= htmlspecialchars($plat['nom'] ?? 'Plat inconnu') ?></h3>

                        <p class="description">
                            <?= htmlspecialchars($plat['description'] ?? 'Description indisponible') ?>
                        </p>

                        <div class="prix">
                            <?= number_format((float)($plat['prix'] ?? 0), 2, ",", " ") ?> €
                        </div>

                        <form method="POST" action="panier.php">
                            <input type="hidden" name="action" value="ajouter">
                            <input type="hidden" name="plat_id" value="<?= htmlspecialchars($plat['id'] ?? 0) ?>">
                            <input type="number" name="quantite" value="1" min="1" max="20">
                            <button type="submit">Ajouter au panier</button>
                        </form>
                    </div>

                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

</body>
</html>