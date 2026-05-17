<?php
session_start();
require_once("includes/functions.php");

/* Sécurité : client connecté obligatoire */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header("Location: connexion.php");
    exit();
}

$user = $_SESSION['user'];

/* Récupération de l'id commande depuis l'URL */
$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($commande_id === 0) {
    header("Location: commandes.php");
    exit();
}

/* Chargement des commandes */
$commandes = lireJSON("data/commandes.json");

/* Recherche de la commande */
$commande = null;
$indexCommande = null;
foreach ($commandes as $i => $c) {
    if ((int)$c['id'] === $commande_id) {
        $commande = $c;
        $indexCommande = $i;
        break;
    }
}

/* Vérifications de sécurité */
if ($commande === null) {
    header("Location: commandes.php");
    exit();
}

// La commande doit appartenir au client connecté
if (($commande['user_id'] ?? null) != $user['id']) {
    header("Location: commandes.php");
    exit();
}

// La commande doit être en mode livraison et avoir le statut "Livrée"
$statut     = strtolower($commande['statut_commande'] ?? '');
$mode       = $commande['mode_recuperation'] ?? '';
$dejaNotee  = isset($commande['note_livraison']) && $commande['note_livraison'] !== null;

if ($statut !== 'livrée' || $mode !== 'livraison') {
    header("Location: commandes.php");
    exit();
}

if ($dejaNotee) {
    header("Location: commandes.php");
    exit();
}

/* Traitement du formulaire de notation */
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noteLivraison = isset($_POST['livraison']) ? (int)$_POST['livraison'] : 0;
    $noteQualite   = isset($_POST['qualite'])   ? (int)$_POST['qualite']   : 0;
    $commentaire   = trim($_POST['commentaire'] ?? '');

    if ($noteLivraison < 1 || $noteLivraison > 5 || $noteQualite < 1 || $noteQualite > 5) {
        $erreur = "Veuillez attribuer une note à la livraison et à la qualité des produits.";
    } else {
        /* Sauvegarde de la note dans le JSON */
        $commandes[$indexCommande]['note_livraison'] = $noteLivraison;
        $commandes[$indexCommande]['note_qualite']   = $noteQualite;
        $commandes[$indexCommande]['commentaire_note'] = $commentaire;
        $commandes[$indexCommande]['date_notation']  = date('Y-m-d H:i:s');

        ecrireJSON("data/commandes.json", $commandes);

        header("Location: commandes.php?note=ok");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notation de la commande - L'Atlas Des Saveurs</title>
  <link rel="stylesheet" href="notation.css" />
  <script src="modeSombre.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <h1>L'Atlas Des Saveurs</h1>
  </header>

  <main>
    <h2>Noter votre commande #<?= htmlspecialchars($commande_id) ?></h2>

    <?php if ($erreur): ?>
      <p style="color:red; text-align:center;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" action="notation.php?id=<?= $commande_id ?>">

      <!-- Notation de la livraison (étoiles) -->
      <div class="notation-groupe">
        <label>Livraison :</label>
        <div class="stars">
          <input type="radio" id="livraison-5" name="livraison" value="5" />
          <label for="livraison-5" class="star" title="Excellent">&#9733;</label>
          <input type="radio" id="livraison-4" name="livraison" value="4" />
          <label for="livraison-4" class="star" title="Très bien">&#9733;</label>
          <input type="radio" id="livraison-3" name="livraison" value="3" />
          <label for="livraison-3" class="star" title="Bien">&#9733;</label>
          <input type="radio" id="livraison-2" name="livraison" value="2" />
          <label for="livraison-2" class="star" title="Moyen">&#9733;</label>
          <input type="radio" id="livraison-1" name="livraison" value="1" />
          <label for="livraison-1" class="star" title="Mauvais">&#9733;</label>
        </div>
      </div>

      <!-- Notation de la qualité (étoiles) -->
      <div class="notation-groupe">
        <label>Qualité des produits :</label>
        <div class="stars">
          <input type="radio" id="qualite-5" name="qualite" value="5" />
          <label for="qualite-5" class="star" title="Excellent">&#9733;</label>
          <input type="radio" id="qualite-4" name="qualite" value="4" />
          <label for="qualite-4" class="star" title="Très bien">&#9733;</label>
          <input type="radio" id="qualite-3" name="qualite" value="3" />
          <label for="qualite-3" class="star" title="Bien">&#9733;</label>
          <input type="radio" id="qualite-2" name="qualite" value="2" />
          <label for="qualite-2" class="star" title="Moyen">&#9733;</label>
          <input type="radio" id="qualite-1" name="qualite" value="1" />
          <label for="qualite-1" class="star" title="Mauvais">&#9733;</label>
        </div>
      </div>

      <!-- Commentaire libre -->
      <div class="notation-groupe">
        <label for="commentaire">Commentaires (optionnel) :</label>
        <textarea id="commentaire" name="commentaire" placeholder="Votre avis..."></textarea>
      </div>

      <button type="submit">Envoyer</button>

    </form>
  </main>
</body>
</html>