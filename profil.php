<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: connexion.php");
    exit();
}

$user = $_SESSION["user"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Atlas des Saveurs</title>
    <link rel="stylesheet" href="profil.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <h1>L'Atlas des Saveurs</h1>
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <a href="profil.php">Mon Profil</a>
            <a href="panier.php">Panier</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <main>
        <div class="profil-container">
            <h2>Mon Profil</h2>

            <?php if (($user['role'] ?? '') === 'livreur'): ?>
                <p><a href="livraison.php" class="btn-voir-commandes">Mes livraisons</a></p>
            <?php endif; ?>

            <?php if (($user['role'] ?? '') === 'restaurateur'): ?>
                <p><a href="restaurateur.php" class="btn-voir-commandes">Gérer les commandes</a></p>
            <?php endif; ?>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <p><a href="admin.php" class="btn-voir-commandes">Panel administrateur</a></p>
            <?php endif; ?>

            <section class="profil-section">
                <h3>
                    📋 Informations personnelles
                    <a href="#" class="edit-icon" title="Modifier">✏️</a>
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <strong>Nom :</strong>
                        <span><?= htmlspecialchars($user["nom"] ?? "") ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Prénom :</strong>
                        <span><?= htmlspecialchars($user["prenom"] ?? "") ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Email :</strong>
                        <span><?= htmlspecialchars($user["email"] ?? "") ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Téléphone :</strong>
                        <span><?= htmlspecialchars($user["telephone"] ?? "Non renseigné") ?></span>
                    </div>

                    <div class="info-item full-width">
                        <strong>Adresse :</strong>
                        <span><?= htmlspecialchars($user["adresse"] ?? "Non renseignée") ?></span>
                    </div>

                    <div class="info-item full-width">
                        <strong>Informations complémentaires :</strong>
                        <span><?= htmlspecialchars($user["commentaire"] ?? "Aucune information complémentaire") ?></span>
                    </div>
                </div>
            </section>

            <section class="profil-section">
                <h3>📦 Mes commandes</h3>

                <a href="commandes.php" class="btn-voir-commandes">
                    Voir toutes mes commandes
                </a>

                <div class="commandes-list">
                    <div class="commande-card">
                        <div class="commande-header">
                            <span class="commande-numero">Commande récente</span>
                            <span class="commande-date">Voir historique complet</span>
                        </div>
                        <div class="commande-details">
                            <p><strong>Accès :</strong> Consultez toutes vos commandes dans la page dédiée.</p>
                            <p><strong>Suivi :</strong> Vous y retrouverez les statuts, le paiement et la notation.</p>
                            <a href="commandes.php" class="btn-noter">📦 Ouvrir mes commandes</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="profil-section">
            <h3>⭐ Mon compte fidélité</h3>

            <div class="fidelite-niveau">
                <p class="niveau-titre">🥈 Niveau Argent</p>
                <p>Vous bénéficiez de <strong>5% de réduction</strong> sur toutes vos commandes et d'une livraison offerte à partir de 25 €.</p>
            </div>

            <div class="fidelite-points">
                <p><strong>Vos points :</strong> 320 / 500 pour atteindre le niveau 🥇 Or</p>
                <div class="barre-fond">
                    <div class="barre-remplie"></div>
                </div>
                <p class="texte-progression">Il vous manque 180 points pour passer au niveau supérieur.</p>
            </div>

            <div class="fidelite-avantages">
                <h4>Vos avantages actuels :</h4>
                <div class="avantages-liste">
                    <div class="avantage-item">
                        <span>🚚</span>
                        <p>Livraison offerte dès 25 €</p>
                    </div>
                    <div class="avantage-item">
                        <span>🏷️</span>
                        <p>5% de réduction permanente</p>
                    </div>
                    <div class="avantage-item">
                        <span>🎂</span>
                        <p>Dessert offert pour votre anniversaire</p>
                    </div>
                </div>
            </div>

            <div class="fidelite-historique">
                <h4>Historique des points :</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>28/01/2026</td>
                            <td>Commande validée</td>
                            <td class="points-plus">+ 33 pts</td>
                        </tr>
                        <tr>
                            <td>15/01/2026</td>
                            <td>Commande validée</td>
                            <td class="points-plus">+ 28 pts</td>
                        </tr>
                        <tr>
                            <td>02/01/2026</td>
                            <td>Commande validée</td>
                            <td class="points-plus">+ 45 pts</td>
                        </tr>
                        <tr>
                            <td>20/12/2025</td>
                            <td>Bon de réduction utilisé</td>
                            <td class="points-moins">− 100 pts</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
    </footer>

</body>
</html>