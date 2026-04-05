<?php 

$verife_email="";
$verife_mdp="";
$verife_tel="";

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$code_postal = $_POST['code_postal'] ?? '';
$ville = $_POST['ville'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';


if(!file_exists("inscription.json")){
    $message[] = "Fichier JSON manquant";
    $backdata = array();
}
else {
    $backdata =json_decode(file_get_contents("inscription.json"),true);
}

if($password_confirm != $password){
    $verife_mdp = "mdp different";}
foreach($backdata as $key){
    if($key["email"] == $email){
        $verife_email = "email deja utiliser";
    }
    else if($key["telephone"] == $telephone){
        $verife_tel = "numero deja utiliser";
    }
}
if(!empty($telephone) && substr($telephone, 0, 2) != "06" && substr($telephone, 0, 2) != "07"){
    $verife_tel = "Faux numero";
}




if(empty($verife_email) && empty($verife_tel) && empty($verife_mdp)){
    if(!empty($nom) && !empty($prenom) && !empty($telephone) && !empty($email) && !empty($adresse) and !empty($code_postal) && !empty($ville) && !empty($password) && !empty($password_confirm )){
        $backdata[] = array(
            'id' => uniqid(),
            'role' => 'client',
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $adresse,
            'code_postal' => $code_postal,
            'ville' => $ville,
            'password' => $password,
            'actif'=> 'actif'

            
        );
        file_put_contents("inscription.json",json_encode($backdata,JSON_PRETTY_PRINT));
        header("Location: connexion.php");
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
</head>
<body>
    <header>
        <h1>🍽️ Atlas des Saveurs</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="produits.php">Menu</a>
            <a href="profil.php">Mon Profil</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Inscription</h2>
            
            <div class="info-message">
                <p>Créez un compte pour profiter de nos services : retrouver vos commandes, bénéficier de remises exclusives et plus encore !</p>
            </div>

            <form action="inscription.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Dupont" value="<?php $_POST['nom'] ?? "";?>" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" placeholder="Jean" value="<?php $prenom?>"required>
                </div>

                <div class="form-group">
                    <label for="email">Adresse email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php $email;?>" placeholder="votre.email@exemple.com" required>
                    <?php echo "<p>".$verife_email."<p>";?>
                </div>

                <div class="form-group">
                    <label for="telephone">Numéro de téléphone <span class="required">*</span></label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        value="<?php $telephone;?>"
                        placeholder="0612345678"
                        pattern="[0-9]{10}"
                        maxlength="10"
                        title="Veuillez entrer exactement 10 chiffres"
                        required
                    >
                    <?php echo "<p>".$verife_tel."<p>";?>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse de livraison <span class="required">*</span></label>
                    <input type="text" id="adresse" name="adresse" value="<?php $adresse;?>"placeholder="12 rue de la République" required>
                </div>

                <div class="form-group">
                    <label for="code_postal">Code postal <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="code_postal" 
                        name="code_postal" 
                        value="<?php $code_postal;?>"
                        placeholder="75001"
                        pattern="[0-9]{5}"
                        maxlength="5"
                        title="Veuillez entrer exactement 5 chiffres"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="ville">Ville <span class="required">*</span></label>
                    <input type="text" id="ville" name="ville" value="<?php $ville;?>"placeholder="Paris" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe <span class="required">*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        value="<?php $password;?>"
                        placeholder="Minimum 8 caractères"
                        minlength="8"
                        title="Le mot de passe doit contenir au moins 8 caractères"
                        required
                    >
                    <?php echo "<p>".$verife_mdp."<p>";?>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        value="<?php $password_confirm;?>"
                        placeholder="Confirmer votre mot de passe"
                        minlength="8"
                        required
                    >
                    <?php echo "<p>".$verife_mdp."<p>";?>
                </div>

                <div class="form-group">
                    <button type="submit">S'inscrire</button>
                </div>

                <div class="form-links">
                    <p>Vous avez déjà un compte ? <a href="connexion.html">Se connecter</a></p>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Atlas des Saveurs - Tous droits réservés</p>
    </footer>
</body>
</html>
