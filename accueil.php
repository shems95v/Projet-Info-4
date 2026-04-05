<?php 
session_start();
?>

<?php if (isset($_SESSION['user']['role']) === 'admin'): ?>
    <a href="admin.php">Panel Admin</a>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'Atlas Des Saveurs</title>
    <link rel="stylesheet" href="accueil.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- header avec nav et barre de recherche -->
    <header>
        <h1>L'Atlas Des Saveurs</h1>
        <nav class="navigation">
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <a href="profil.php">Mon profil</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
            <a href="panier.php">Panier</a>
        </nav>
        <div class="conteneur-recherche">
            <input type="text" placeholder="Rechercher un plat, une saveur...">
            <button type="button">🔍</button>
        </div>
    </header>

    <main>

        <p class="intro">
            Bienvenue à <strong>L'Atlas Des Saveurs</strong> ! Plongez dans un voyage culinaire unique où chaque plat est préparé avec passion et authenticité. Nos plats sont conçus pour éveiller vos sens et ravir vos papilles.
        </p>

        <h4>Nos formules</h4>

        <!-- les deux colonnes de plats -->
        <div class="plats">

            <!-- colonne gauche : plat du jour -->
            <div class="colonne">
                <h3>Plat du jour</h3>
                <ul>
                    <li>
                        <a href="#" class="carte-plat carte-plat-grande carte-plat-katsu">
                            <span class="nom-plat">Katsu Curry</span>
                            <span class="prix-plat">14,50 €</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- colonne droite : plats populaires -->
            <div class="colonne">
                <h3>Souvent commandés</h3>
                <ul>
                    <li>
                        <a href="#" class="carte-plat carte-plat-gyozas">
                            <span class="nom-plat">Gyozas Maison</span>
                            <span class="prix-plat">8,00 €</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="carte-plat carte-plat-baklava">
                            <span class="nom-plat">Baklava au Miel</span>
                            <span class="prix-plat">7,00 €</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="carte-plat carte-plat-pastilla">
                            <span class="nom-plat">Pastilla au Poulet</span>
                            <span class="prix-plat">9,50 €</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <!-- bouton vers la page menu complète -->
        <div class="voir-menu">
            <a href="presentation.php">Voir tout le menu →</a>
        </div>

    </main>

    <footer>
        <p>&copy; 2026 L'Atlas Des Saveurs — Tous droits réservés</p>
    </footer>

</body>
</html>