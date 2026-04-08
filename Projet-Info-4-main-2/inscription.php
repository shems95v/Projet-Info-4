<?php 

$verife_email="";
$verife_mdp="";
$verife_tel="";
$verife_log = "";

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$code_postal = $_POST['code_postal'] ?? '';
$ville = $_POST['ville'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$login = $_POST['login'] ?? '';
$commentaire = $_POST['commentaire'] ??'';

if(!file_exists("data/users.json")){
    $message[] = "Fichier JSON manquant";
    $backdata = array();
}
else {
    $backdata =json_decode(file_get_contents("data/users.json"),true);
}
if($_SERVER["REQUEST_METHOD"] === "POST"){
if($password_confirm != $password){
    $verife_mdp = "mdp different";}
foreach($backdata as $key){
    if($key["email"] == $email){
        $verife_email = "email deja utilisé";
        break;
    }
    if($key["login"] == $login){
        $verife_log = "login deja utilisé";
        break;
    }
    if($key["telephone"] == $telephone){
        $verife_tel = "numero deja utilisé";
        break;
    }
}
if(!empty($telephone) && substr($telephone, 0, 2) != "06" && substr($telephone, 0, 2) != "07"){
    $verife_tel = "Faux numero";
}
}



if(empty($verife_email) && empty($verife_tel) && empty($verife_mdp) && empty($verife_log)){
    if(!empty($nom) && !empty($prenom) && !empty($telephone) && !empty($email) && !empty($adresse) and !empty($code_postal) && !empty($ville) && !empty($password) && !empty($password_confirm )&& !empty($login)){
        $backdata[] = array(
            'id' => uniqid(),
            'role' => 'client',
            'login' => $login,
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $adresse,
            'code_postal' => $code_postal,
            'ville' => $ville,
            'password' => $password,
            'actif'=> true,
            'commentaire' => $commentaire

            
        );
        file_put_contents("data/users.json",json_encode($backdata,JSON_PRETTY_PRINT));
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
            <a href="accueil.php">Accueil</a>
            <a href="presentation.php">Menu</a>
            <a href="profil.php">Mon Profil</a>
            <a href="inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        </nav>
    </header>

    <main>
        <div class="conteneur-formulaire">
    <h2>Inscription</h2>
    
    <div class="message-info">
        <p>Créez un compte pour profiter de nos services : retrouver vos commandes, bénéficier de remises exclusives et plus encore !</p>
    </div>

    <form action="inscription.php" method="POST">
        
        <div class="groupe-formulaire">
            <label for="nom">Nom <span class="obligatoire">*</span></label>
            <input type="text" id="nom" name="nom" placeholder="Dupont" value="<?php echo htmlspecialchars($nom);?>" required>
        </div>

        <div class="groupe-formulaire">
            <label for="prenom">Prénom <span class="obligatoire">*</span></label>
            <input type="text" id="prenom" name="prenom" placeholder="Jean"  value="<?php echo htmlspecialchars($prenom);?>" required>
        </div>
        <div class="groupe-formulaire">
            <label for="prenom">Login <span class="obligatoire">*</span></label>
            <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($login);?>" placeholder="Jean" required>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife_log."</p>"; ?>
        </div>
        <div class="groupe-formulaire">
            <label for="email">Adresse email <span class="obligatoire">*</span></label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email);?>" placeholder="votre.email@exemple.com"
             title="Veuillez entrer un email"
            required>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife_email."</p>"; ?>
        </div>

        <div class="groupe-formulaire">
            <label for="telephone">Numéro de téléphone <span class="obligatoire">*</span></label>
            <input type="tel" 
                id="telephone" 
                name="telephone" 
                value="<?php echo htmlspecialchars($telephone); ?>"
                placeholder="0612345678"
                pattern="[0-9]{10}"
                maxlength="10"
                title="Veuillez entrer exactement 10 chiffres"
                required>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife_tel."</p>"; ?>
        </div>

        <div class="groupe-formulaire">
            <label for="adresse">Adresse de livraison <span class="obligatoire">*</span></label>
            <input type="text" id="adresse" name="adresse"  value="<?php echo htmlspecialchars($adresse);?>" placeholder="12 rue..." required>
        </div>

        <div class="groupe-formulaire">
            <label for="code_postal">Code postal <span class="obligatoire">*</span></label>
            <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($code_postal);?>" placeholder="75001" 
            pattern="[0-9]{5}"
            maxlength="5"
            title="Veuillez entrer exactement 5 chiffres"
            required>
        </div>

        <div class="groupe-formulaire">
            <label for="ville">Ville <span class="obligatoire">*</span></label>
            <input type="text" id="ville" name="ville" placeholder="Paris" value="<?php echo htmlspecialchars($ville);?>" required>
        </div>

        <div class="groupe-formulaire">
            <label for="password">Mot de passe <span class="obligatoire">*</span></label>
            <input type="password" id="password" name="password" 
            placeholder="Minimum 8 caractères"
            minlength="8"
            title="Le mot de passe doit contenir au moins 8 caractères"
            value="<?php $password;?>"
            required>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife_mdp."</p>"; ?>
        </div>

        <div class="groupe-formulaire">
            <label for="password_confirm">Confirmer le mot de passe <span class="obligatoire">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm"
            placeholder="Minimum 8 caractères"
            minlength="8"
            title="Le mot de passe doit contenir au moins 8 caractères"
            value="<?php $password_confirm;?>"
             required>
            <?php echo "<p style='color:red; margin-bottom:15px; text-align:center;'>".$verife_mdp."</p>"; ?>
        </div>
        <div class="groupe-formulaire">
                <label for="commentaire">Informations complémentaires</label>
                <textarea id="commentaire" name="commentaire"  value="<?php echo htmlspecialchars($commentaire);?>" placeholder="Allergies, préférences de livraison..."></textarea>
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
