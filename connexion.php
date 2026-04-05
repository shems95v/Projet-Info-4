<?php 
session_start();
if(!file_exists("users.json")){
    $message[] = "Fichier JSON manquant";
    $backdata = array();
}
else {
    $backdata =json_decode(file_get_contents("users.json"),true);
}
$verife = "";
$trouve = false;
if(!empty($_POST['motdepasse']) && !empty($_POST['login'] )  ){
    foreach($backdata as $key){
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



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Atlas des Saveurs</title>
    <link rel="stylesheet" href="connexion.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- header -->
    <header>
        <h1>L'Atlas des Saveurs</h1>
        <nav>
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <a href="profil.php">Mon Profil</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        </nav>
    </header>

    <main>
        <!-- formulaire de connexion -->
        <div class="conteneur-formulaire">
            <h2>Connexion</h2>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife."<p>";?>
            <form action = "connexion.php" method="POST">

                <div class="groupe-formulaire">
                    <label for="login">Login <span class="obligatoire">*</span></label>
                    <input type="text" 
                    id="login" 
                    name="login"
                    value="<?php $_POST['email'] ?? "";?>"
                    placeholder="votre.email@exemple.com" required>
                </div>

                <div class="groupe-formulaire">
                    <label for="motdepasse">Mot de passe <span class="obligatoire">*</span></label>
                    <input type="password" id="motdepasse" name="motdepasse" 
                    value="<?php $_POST['motdepasse'] ?? "";?>"
                    placeholder="Votre mot de passe" 
                    required>
                </div>

                <!-- bouton de soumission -->
                <div class="groupe-formulaire">
                    <button type="submit">Se connecter</button>
                </div>

            </form>

            <!-- lien vers la page inscription -->
            <div class="liens-formulaire">
                <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
    </footer>

</body>
</html>
