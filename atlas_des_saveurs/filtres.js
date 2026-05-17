"use strict";

/* État local : on stocke les plats chargés depuis le serveur */
let platsCourants = Array.from(document.querySelectorAll('.carte-produit')).map(carte => ({
    _element: carte,
    nom:      carte.querySelector('h3')?.textContent.trim() ?? '',
    prix:     parseFloat(carte.querySelector('.prix')?.textContent.replace(',', '.').replace(/[^\d.]/g, '') ?? '0'),
    nbCommandes: 0, // pas dispo côté HTML, mis à jour via l'API
}));

/* Éléments DOM */
const grilleEl      = document.querySelector('.grille-produits');
const compteurEl    = document.getElementById('compteur-plats');
const inputRecherche = document.getElementById('recherche-plats');
const selectTri     = document.getElementById('select-tri');

/* Cases à cocher */
const cbTypes    = document.querySelectorAll('input[name="type"]');
const cbSaveurs  = document.querySelectorAll('input[name="flavor"]');
const cbGluten   = document.getElementById('filtre-gluten');
const cbLactose  = document.getElementById('filtre-lactose');
const cbVege     = document.getElementById('filtre-vegetarien');

const btnReset   = document.querySelector('.bouton-reinitialiser');

/* Debounce — évite les appels trop fréquents pendant la frappe */
function debounce(fn, delai = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delai);
    };
}

/* Construction des paramètres de requête à partir des filtres */
function collecterFiltres() {
    const params = new URLSearchParams();

    cbTypes.forEach(cb => {
        if (cb.checked) params.append('types[]', cb.value);
    });

    cbSaveurs.forEach(cb => {
        if (cb.checked) params.append('saveurs[]', cb.value);
    });

    /* Filtres diététiques, on envoie uniquement si coché */
    if (cbGluten  && cbGluten.checked)  params.set('sansGluten',  '1');
    if (cbLactose && cbLactose.checked) params.set('sansLactose', '1');
    if (cbVege    && cbVege.checked)    params.set('vegetarien',  '1');

    const q = inputRecherche ? inputRecherche.value.trim() : '';
    if (q) params.set('recherche', q);

    return params;
}

/* Affichage d'un indicateur de chargement */
function afficherChargement() {
    if (!grilleEl) return;
    grilleEl.innerHTML = '<p class="message-filtre">Chargement…</p>';
}

/* Rendu d'un tableau de plats (reçus en JSON depuis l'API) */
function afficherPlats(plats) {
    if (!grilleEl) return;

    if (plats.length === 0) {
        grilleEl.innerHTML = '<p class="message-filtre">Aucun plat ne correspond à vos critères.</p>';
        if (compteurEl) compteurEl.textContent = '0 plat';
        return;
    }

    if (compteurEl) {
        compteurEl.textContent = `${plats.length} plat${plats.length > 1 ? 's' : ''}`;
    }

    // On reconstruit toute la grille depuis les données JSON
    grilleEl.innerHTML = plats.map(plat => `
        <article class="carte-produit" data-id="${plat.id}" data-prix="${plat.prix}" data-nb="${plat.nbCommandes ?? 0}">
            <img
                src="${echapper(plat.image ?? 'images/default.jpg')}"
                alt="${echapper(plat.nom ?? 'Plat')}"
                class="image-produit"
                onerror="this.src='images/default.jpg'"
            >
            <div class="infos-produit">
                <span class="categorie">${echapper(plat.categorie ?? '')}</span>
                <h3>${echapper(plat.nom ?? 'Plat inconnu')}</h3>
                <p class="description">${echapper(plat.description ?? '')}</p>
                <div class="prix">${formatPrix(plat.prix)} €</div>
                <form method="POST" action="panier.php">
                    <input type="hidden" name="action" value="ajouter">
                    <input type="hidden" name="plat_id" value="${plat.id}">
                    <input type="number" name="quantite" value="1" min="1" max="20">
                    <button type="submit">Ajouter au panier</button>
                </form>
            </div>
        </article>
    `).join('');

    platsCourants = plats;
}

/* Tri côté client (sur les données déjà affichées) */
function trierEtAfficher() {
    if (!selectTri || platsCourants.length === 0) return;
    const valeur = selectTri.value;

    // On copie pour ne pas modifier le tableau d'origine
    const copies = [...platsCourants];

    switch (valeur) {
        case 'prix_asc':
            copies.sort((a, b) => (a.prix ?? 0) - (b.prix ?? 0));
            break;
        case 'prix_desc':
            copies.sort((a, b) => (b.prix ?? 0) - (a.prix ?? 0));
            break;
        case 'popularite':
            copies.sort((a, b) => (b.nbCommandes ?? 0) - (a.nbCommandes ?? 0));
            break;
        case 'nom_asc':
            copies.sort((a, b) => (a.nom ?? '').localeCompare(b.nom ?? '', 'fr'));
            break;
        default:
            /* pas de tri → ordre tel que reçu du serveur */
    }

    afficherPlats(copies);
}

/* Requête asynchrone vers api_plats.php */
async function fetchPlats() {
    const params = collecterFiltres();
    if (selectTri) params.set('tri', selectTri.value);

    afficherChargement();

    try {
        const reponse = await fetch(`api_plats.php?${params.toString()}`);

        if (!reponse.ok) throw new Error(`Erreur HTTP ${reponse.status}`);

        const donnees = await reponse.json();

        if (donnees.erreur) throw new Error(donnees.erreur);

        platsCourants = donnees.plats ?? [];
        afficherPlats(platsCourants);

    } catch (err) {
        console.error('Erreur lors du chargement des plats :', err);
        if (grilleEl) {
            grilleEl.innerHTML = '<p class="message-filtre message-erreur">Une erreur est survenue. Veuillez réessayer.</p>';
        }
    }
}

/* Utilitaires */

// Échappe les caractères HTML pour éviter les injections XSS
function echapper(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}

function formatPrix(valeur) {
    return Number(valeur).toFixed(2).replace('.', ',');
}

/* Attache des écouteurs d'événements */

const fetchDebounce = debounce(fetchPlats, 350);

cbTypes.forEach(cb   => cb.addEventListener('change', fetchDebounce));
cbSaveurs.forEach(cb => cb.addEventListener('change', fetchDebounce));
if (cbGluten)  cbGluten.addEventListener('change',  fetchDebounce);
if (cbLactose) cbLactose.addEventListener('change', fetchDebounce);
if (cbVege)    cbVege.addEventListener('change',    fetchDebounce);

if (inputRecherche) {
    inputRecherche.addEventListener('input', fetchDebounce);
}

/* Le tri est appliqué côté client, pas besoin de refaire un appel API */
if (selectTri) {
    selectTri.addEventListener('change', trierEtAfficher);
}

/* Réinitialise tous les filtres et recharge les plats */
if (btnReset) {
    btnReset.addEventListener('click', () => {
        cbTypes.forEach(cb   => (cb.checked = false));
        cbSaveurs.forEach(cb => (cb.checked = false));
        if (cbGluten)  cbGluten.checked  = false;
        if (cbLactose) cbLactose.checked = false;
        if (cbVege)    cbVege.checked    = false;
        if (inputRecherche) inputRecherche.value = '';
        if (selectTri) selectTri.value = '';
        fetchPlats();
    });
}