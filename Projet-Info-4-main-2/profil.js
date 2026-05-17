document.addEventListener("DOMContentLoaded", () => {
    const btnEdit = document.getElementById("btn-edit");
    const view    = document.getElementById("info-view");
    const edit    = document.getElementById("info-edit");
    if (!btnEdit || !view || !edit) return;

    // --- Helpers affichage d'erreur ---
    function afficherErreur(inputName, message) {
        const input = edit.querySelector(`[name="${inputName}"]`);
        let span = input.parentElement.querySelector(".erreur-js");
        if (!span) {
            span = document.createElement("p");
            span.className = "erreur-js";
            span.style.cssText = "color:red;margin:4px 0 0;font-size:.85em;";
            input.after(span);
        }
        span.textContent = message;
    }

    function clearErreur(inputName) {
        const input = edit.querySelector(`[name="${inputName}"]`);
        const span = input?.parentElement.querySelector(".erreur-js");
        if (span) span.textContent = "";
    }

    function clearToutesErreurs() {
        edit.querySelectorAll(".erreur-js").forEach(e => e.textContent = "");
        // Enlève aussi les erreurs PHP résiduelles
        edit.querySelectorAll("p[style*='color:red']").forEach(e => e.textContent = "");
    }

    // --- Ouvrir le formulaire ---
    btnEdit.addEventListener("click", (e) => {
        e.preventDefault();
        clearToutesErreurs();
        view.style.display = "none";
        edit.style.display = "block";
    });

    // --- Vérif doublon asynchrone (email / login / tel) ---
    async function verifDoublon(champ, valeur) {
        const fd = new FormData();
        fd.append("champ", champ);
        fd.append("valeur", valeur);
        const res = await fetch("verif_profil.php", { method: "POST", body: fd });
        const json = await res.json();
        return json.erreur ?? "";
    }

    // --- Validation locale synchrone ---
    function validerLocalement() {
        let ok = true;

        const nom = edit.querySelector('[name="nom2"]').value.trim();
        if (!nom) { afficherErreur("nom2", "Le nom est obligatoire."); ok = false; }
        else clearErreur("nom2");

        const prenom = edit.querySelector('[name="prenom2"]').value.trim();
        if (!prenom) { afficherErreur("prenom2", "Le prénom est obligatoire."); ok = false; }
        else clearErreur("prenom2");

        const login = edit.querySelector('[name="login"]').value.trim();
        if (login.length < 3) { afficherErreur("login", "Login trop court (min 3 caractères)."); ok = false; }
        else clearErreur("login");

        const email = edit.querySelector('[name="email2"]').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            afficherErreur("email2", "Email invalide."); ok = false;
        } else clearErreur("email2");

        const tel = edit.querySelector('[name="telephone2"]').value.trim();
        if (tel && !/^0[67]\d{8}$/.test(tel)) {
            afficherErreur("telephone2", "Numéro invalide (ex: 06XXXXXXXX)."); ok = false;
        } else clearErreur("telephone2");

        return ok;
    }

    // --- Soumission ---
    edit.addEventListener("submit", async (e) => {
        e.preventDefault();
        clearToutesErreurs();

        // 1. Vérifs locales
        if (!validerLocalement()) return;

        // 2. Vérifs doublons serveur (en parallèle)
        const login = edit.querySelector('[name="login"]').value.trim();
        const email = edit.querySelector('[name="email2"]').value.trim();
        const tel   = edit.querySelector('[name="telephone2"]').value.trim();

        const [errLogin, errEmail, errTel] = await Promise.all([
            verifDoublon("login", login),
            verifDoublon("email", email),
            tel ? verifDoublon("telephone", tel) : Promise.resolve("")
        ]);

        let hasDoublon = false;
        if (errLogin) { afficherErreur("login",     errLogin); hasDoublon = true; }
        if (errEmail) { afficherErreur("email2",    errEmail); hasDoublon = true; }
        if (errTel)   { afficherErreur("telephone2", errTel);  hasDoublon = true; }
        if (hasDoublon) return;

        // 3. Tout est OK → envoi réel
        const response = await fetch("profil.php", {
            method: "POST",
            body: new FormData(edit)
        });

        if (response.ok) {
            // Mise à jour de l'affichage
            document.getElementById("nom-affichage").textContent     = edit.querySelector('[name="nom2"]').value;
            document.getElementById("prenom-affichage").textContent  = edit.querySelector('[name="prenom2"]').value;
            document.getElementById("login-affichage").textContent   = login;
            document.getElementById("email-affichage").textContent   = email;
            document.getElementById("tel-affichage").textContent     = tel || "Non renseigné";
            document.getElementById("adresse-affichage").textContent = edit.querySelector('[name="adresse2"]').value || "Non renseignée";
            document.getElementById("com-affichage").textContent     = edit.querySelector('[name="commentaire2"]').value || "Aucune information complémentaire";

            view.style.display = "block";
            edit.style.display = "none";
            alert("✅ Profil mis à jour !");
        }
    });
});