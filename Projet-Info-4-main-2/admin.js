document.addEventListener("DOMContentLoaded", () => {

    const msg = document.getElementById("message-admin");

    function afficherMessage(texte, ok = true) {
        msg.textContent = texte;
        msg.style.color = ok ? "green" : "red";
        setTimeout(() => msg.textContent = "", 3000);
    }

    // --- Bloquer / Débloquer ---
    document.querySelectorAll(".bouton-bloquer").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id    = btn.dataset.id;
            const actif = btn.dataset.actif === "1";

            const fd = new FormData();
            fd.append("action", "toggle_actif");
            fd.append("id", id);
            fd.append("actif", actif ? "0" : "1");

            const res  = await fetch("admin_action.php", { method: "POST", body: fd });
            const json = await res.json();

            if (json.ok) {
                const nouvelActif = !actif;
                // Mettre à jour le bouton
                btn.dataset.actif   = nouvelActif ? "1" : "0";
                btn.textContent     = nouvelActif ? "Bloquer" : "Débloquer";
                // Mettre à jour le badge statut
                const badge = document.getElementById(`statut-${id}`);
                badge.textContent   = nouvelActif ? "Actif" : "Bloqué";
                badge.className     = "badge " + (nouvelActif ? "badge-actif" : "badge-bloque");

                afficherMessage(json.msg);
            } else {
                afficherMessage(json.msg, false);
            }
        });
    });

    // --- Changer le rôle ---
    document.querySelectorAll(".bouton-role").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id      = btn.dataset.id;
            const select  = document.querySelector(`.select-role[data-id="${id}"]`);
            const nouveau = select.value;

            const fd = new FormData();
            fd.append("action", "change_role");
            fd.append("id", id);
            fd.append("role", nouveau);

            const res  = await fetch("admin_action.php", { method: "POST", body: fd });
            const json = await res.json();

            if (json.ok) {
                const badge = document.getElementById(`role-${id}`);
                badge.textContent = nouveau;
                badge.className   = `badge role-${nouveau}`;
                afficherMessage(json.msg);
            } else {
                afficherMessage(json.msg, false);
            }
        });
    });
});