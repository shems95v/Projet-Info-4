<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notation de la commande - L'Atlas Des Saveurs</title>
  <link rel="stylesheet" href="notation.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- header -->
  <header>
    <h1>L'Atlas Des Saveurs</h1>
  </header>

  <main>
    <h2>Noter votre commande</h2>

    <form>

      <!-- notation de la livraison (étoiles) -->
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

      <!-- notation de la qualité (étoiles) -->
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

      <!-- commentaire libre -->
      <div class="notation-groupe">
        <label for="commentaire">Commentaires (optionnel) :</label>
        <textarea id="commentaire" name="commentaire" placeholder="Votre avis..."></textarea>
      </div>

      <button type="submit">Envoyer</button>

    </form>
  </main>
</body>
</html>