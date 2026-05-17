<?php 
session_start();

// Chargement des utilisateurs depuis le fichier JSON
if(!file_exists("data/users.json")){
    $backdata = array();
}
else {
    $backdata = json_decode(file_get_contents("data/users.json"), true);
}

$verife = "";
$trouve = false;

if(!empty($_POST['motdepasse']) && !empty($_POST['login'])){
    foreach($backdata as $key){
        // Vérification du login et du mot de passe
        if($key["login"] == $_POST["login"] && $key["password"] == $_POST["motdepasse"]){
            $trouve = true;
            $tab = $key;
            break;
        }
    }
    if(!$trouve) {
        $verife = "Login ou mot de passe incorrect";
    } else {
        $_SESSION['user'] = $tab;
        header("Location: accueil.php");
        exit(); 
    }
}

// On garde le login saisi pour le remettre dans le champ après erreur
$login_saisi = htmlspecialchars($_POST['login'] ?? "");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Atlas des Saveurs</title>
    <link rel="stylesheet" href="connexion.css">
    <script src="modeSombre.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <h1>L'Atlas des Saveurs</h1>
        <button id="btn-dark-mode">Changer thème</button>
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <a href="profil.php">Mon Profil</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        </nav>
    </header>

    <main>
        <div class="conteneur-formulaire">
            <h2>Connexion</h2>
            <!-- Message d'erreur si login/mdp incorrect -->
            <?php if(!empty($verife)): ?>
                <p style="color:red; margin-bottom:15px; text-align:center;"><?php echo $verife; ?></p>
            <?php endif; ?>

            <form action="connexion.php" method="POST">

                <div class="groupe-formulaire">
                    <label for="login">Login <span class="obligatoire">*</span></label>
                    <input type="text" 
                        id="login" 
                        name="login"
                        maxlength="12"
                        value="<?php echo $login_saisi; ?>"
                        placeholder="Votre login" required>
                    <small id="msg-login"></small>
                </div>
                
                <div class="groupe-formulaire">
                    <label for="motdepasse">Mot de passe <span class="obligatoire">*</span></label>
                    <input type="password" id="motdepasse" name="motdepasse" 
                        placeholder="Votre mot de passe" 
                        required>
                    <small id="msg-mdp"></small>
                    <!-- Bouton pour afficher/masquer le mot de passe -->
                    <button id="voirMdp" type="button" onclick="montrerPassword()">👁️</button>
                </div>

                <div class="groupe-formulaire">
                    <button type="submit">Se connecter</button>
                </div>

            </form>

            <div class="liens-formulaire">
                <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
    </footer>

    <script src="connexion.js"></script>
</body>
</html>