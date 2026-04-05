<?php
require_once("includes/functions.php");

$message = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $users = lireJSON("data/users.json");

    $nom = trim($_POST["nom"] ?? "");
    $prenom = trim($_POST["prenom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telephone = trim($_POST["telephone"] ?? "");
    $adresse = trim($_POST["adresse"] ?? "");
    $codePostal = trim($_POST["code_postal"] ?? "");
    $ville = trim($_POST["ville"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $passwordConfirm = trim($_POST["password_confirm"] ?? "");
    $commentaires = trim($_POST["commentaires"] ?? "");

    if ($password !== $passwordConfirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $nouvelId = count($users) > 0 ? (max(array_column($users, "id")) + 1) : 1;

        /* login généré simplement à partir de l'email */
        $login = strstr($email, "@", true);
        if ($login === false || $login === "") {
            $login = "client" . $nouvelId;
        }

        /* vérifier que le login n'existe pas déjà */
        foreach ($users as $user) {
            if (($user["login"] ?? "") === $login) {
                $login = $login . $nouvelId;
                break;
            }
        }

        $users[] = [
            "id" => $nouvelId,
            "login" => $login,
            "password" => $password,
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "telephone" => $telephone,
            "adresse" => $adresse . ", " . $codePostal . " " . $ville,
            "interphone" => "",
            "etage" => "",
            "commentaire" => $commentaires,
            "role" => "client",
            "actif" => true
        ];

        ecrireJSON("data/users.json", $users);
        $message = "Inscription réussie. Votre login est : " . $login;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Atlas des Saveurs</title>
    <link rel="stylesheet" href="inscription.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <h1>L'Atlas des Saveurs</h1>
    <nav>
        <a href="accueil.php">Accueil</a>
        <a href="presentation.php">Menu</a>
        <a href="profil.php">Mon Profil</a>
        <a href="inscription.php">Inscription</a>
        <a href="connexion.php">Connexion</a>
        <a href="panier.php">Panier</a>
    </nav>
</header>

<main>
    <div class="conteneur-formulaire">
        <h2>Inscription</h2>

        <div class="message-info">
            <p>Créez un compte pour profiter de nos services : retrouver vos commandes, bénéficier de remises exclusives et plus encore !</p>
        </div>

        <?php if (!empty($erreur)): ?>
            <p style="color:red; margin-bottom:15px; text-align:center;">
                <?= htmlspecialchars($erreur) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <p style="color:green; margin-bottom:15px; text-align:center;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="inscription.php">
            <div class="groupe-formulaire">
                <label for="nom">Nom <span class="obligatoire">*</span></label>
                <input type="text" id="nom" name="nom" placeholder="Dupont" required>
            </div>

            <div class="groupe-formulaire">
                <label for="prenom">Prénom <span class="obligatoire">*</span></label>
                <input type="text" id="prenom" name="prenom" placeholder="Jean" required>
            </div>

            <div class="groupe-formulaire">
                <label for="email">Adresse email <span class="obligatoire">*</span></label>
                <input type="email" id="email" name="email" placeholder="votre.email@exemple.com" required>
            </div>

            <div class="groupe-formulaire">
                <label for="telephone">Numéro de téléphone <span class="obligatoire">*</span></label>
                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    placeholder="0612345678"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    title="Veuillez entrer exactement 10 chiffres"
                    required
                >
            </div>

            <div class="groupe-formulaire">
                <label for="adresse">Adresse de livraison <span class="obligatoire">*</span></label>
                <input type="text" id="adresse" name="adresse" placeholder="12 rue de la République" required>
            </div>

            <div class="groupe-formulaire">
                <label for="code_postal">Code postal <span class="obligatoire">*</span></label>
                <input
                    type="text"
                    id="code_postal"
                    name="code_postal"
                    placeholder="75001"
                    pattern="[0-9]{5}"
                    maxlength="5"
                    title="Veuillez entrer exactement 5 chiffres"
                    required
                >
            </div>

            <div class="groupe-formulaire">
                <label for="ville">Ville <span class="obligatoire">*</span></label>
                <input type="text" id="ville" name="ville" placeholder="Paris" required>
            </div>

            <div class="groupe-formulaire">
                <label for="password">Mot de passe <span class="obligatoire">*</span></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 caractères"
                    minlength="8"
                    title="Le mot de passe doit contenir au moins 8 caractères"
                    required
                >
            </div>

            <div class="groupe-formulaire">
                <label for="password_confirm">Confirmer le mot de passe <span class="obligatoire">*</span></label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    placeholder="Confirmer votre mot de passe"
                    minlength="8"
                    required
                >
            </div>

            <div class="groupe-formulaire">
                <label for="commentaires">Informations complémentaires</label>
                <textarea id="commentaires" name="commentaires" placeholder="Allergies, préférences de livraison..."></textarea>
            </div>

            <div class="groupe-formulaire">
                <button type="submit">S'inscrire</button>
            </div>

            <div class="liens-formulaire">
                <p>Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a></p>
            </div>
        </form>
    </div>
</main>

<footer>
    <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
</footer>
</body>
</html>