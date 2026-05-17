// Compteur login (max 12)
document.getElementById("login").addEventListener("input", function() {
    var reste = 12 - this.value.length;
    var message = document.getElementById("msg-login");
 
    if (reste === 0) {
        message.textContent = "Maximum 12 caractères atteint !";
        message.style.color = "red";
    } else {
        message.textContent = reste + " caractère(s) restant(s)";
        message.style.color = "orange";
    }
});
 
// Compteur mot de passe (min 8)
document.getElementById("password").addEventListener("input", function() {
    var manque = 8 - this.value.length;
    var message = document.getElementById("msg-mdp");
 
    if (manque > 0) {
        message.textContent = "Il manque " + manque + " caractère(s)";
        message.style.color = "red";
    } else {
        message.textContent = "✓ Mot de passe valide";
        message.style.color = "green";
    }
});