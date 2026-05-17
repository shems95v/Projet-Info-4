# 🍽️ L'Atlas des Saveurs — Projet Creative-Yumland

Projet web réalisé dans le cadre du cours d'Informatique 4 — préING2 CY Tech (2025-2026).

## 📖 Description

Site web d'un restaurant de cuisine du monde permettant de gérer la chaîne complète d'une commande : du choix des plats par le client jusqu'à la livraison, en passant par le paiement via l'API CYBank et le traitement côté restaurateur.

## 👥 Profils utilisateurs

| Rôle | Description |
|------|-------------|
| **Client** | Consulte la carte, commande en ligne, suit et note ses livraisons |
| **Administrateur** | Gère les comptes utilisateurs (bloquer, changer le rôle) |
| **Restaurateur** | Gère les statuts des commandes et assigne les livreurs |
| **Livreur** | Consulte et valide ses livraisons depuis smartphone |

## 🗂️ Structure du projet

```
atlas_des_saveurs/
│
├── data/                           # Fichiers de données JSON
│   ├── users.json                  # Utilisateurs
│   ├── commandes.json              # Commandes
│   └── plats.json                  # Catalogue des plats
│
├── includes/
│   └── functions.php               # Fonctions PHP réutilisables
│
├── Images/                         # Photos des plats
│
├── accueil.php / accueil.css       # Page d'accueil
├── presentation.php / .css         # Catalogue avec filtres
├── inscription.php / .css / .js    # Formulaire d'inscription
├── connexion.php / .css / .js      # Formulaire de connexion
├── profil.php / .css / .js         # Page de profil utilisateur
├── panier.php / .css               # Panier
├── valider_commande.php            # Validation de commande
├── paiement.php                    # Paiement via API CYBank
├── paiement_supplement.php         # Paiement additionnel
├── retour_paiement.php             # Callback CYBank
├── retour_paiement_supplement.php  # Callback paiement additionnel
├── commandes.php / .css            # Historique commandes (client)
├── modifier_commande.php / .css    # Modification d'une commande payée
├── notation.php / .css             # Notation d'une livraison
├── admin.php / .css / .js          # Panel administrateur
├── admin_action.php                # Actions admin (AJAX)
├── restaurateur.php / .css         # Interface restaurateur
├── livraison.php / .css            # Interface livreur
├── maj_commande.php                # Mise à jour statut commande
├── api_plats.php                   # API asynchrone filtres/tri
├── auth.php                        # Vérification session et rôle
├── getapikey.php                   # Clé API CYBank
├── logout.php                      # Déconnexion
├── modeSombre.js                   # Gestion thème clair/sombre
└── filtres.js                      # Filtres asynchrones (présentation)
```

## ⚙️ Technologies utilisées

- **HTML / CSS** — Structure et charte graphique (mode clair/sombre)
- **PHP** — Génération dynamique des pages, logique serveur, gestion des sessions
- **JavaScript / Fetch API** — Filtres asynchrones, validation formulaires, mise à jour DOM sans rechargement
- **JSON** — Stockage des données (pas de base de données)
- **API CYBank** — Simulation de paiement en ligne

## 🚀 Installation et lancement

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/<votre-repo>/atlas-des-saveurs.git
   ```

2. Placer le dossier `atlas_des_saveurs/` dans le répertoire de votre serveur local :
   - XAMPP → `htdocs/`
   - WAMP → `www/`
   - MAMP → `htdocs/`

3. Démarrer Apache (PHP 7.4+ recommandé).

4. Accéder au site :
   ```
   http://localhost/atlas_des_saveurs/accueil.php
   ```

> ✅ Aucune base de données requise — tout est stocké en JSON dans `data/`.

## 🔐 Comptes de test

Tous les comptes ont le mot de passe **`1234`**.

| Login | Rôle |
|-------|------|
| `admin1` | Administrateur |
| `admin2` | Administrateur |
| `client1` | Client |
| `livreur1` | Livreur |
| `resto1` | Restaurateur |

## 📦 Fonctionnalités implémentées

**Côté client**
- Inscription et connexion sécurisées avec validation côté client (JS)
- Catalogue de plats avec filtres asynchrones (type, saveur, sans gluten/lactose, végétarien, tri par prix/popularité/nom)
- Panier avec ajout de plats et quantités
- Validation de commande (livraison immédiate ou différée, livraison ou emporter)
- Paiement via l'API CYBank
- Modification d'une commande déjà payée mais pas encore en préparation (avec paiement supplémentaire si besoin, ou ticket de réduction)
- Historique des commandes avec suivi de statut
- Notation des livraisons (une seule fois par commande)
- Modification du profil en asynchrone

**Côté restaurateur**
- Visualisation des commandes par statut (Payée, En préparation, Prête, En livraison, Livrée)
- Changement de statut et assignation à un livreur

**Côté livreur**
- Affichage des informations de livraison (adresse, interphone, étage, téléphone)
- Lien Google Maps direct vers l'adresse
- Bouton "Livraison terminée"

**Côté admin**
- Liste des utilisateurs avec badges de rôle et de statut
- Blocage/déblocage en asynchrone (AJAX)
- Changement de rôle en asynchrone (AJAX)

**Global**
- Mode sombre / clair avec sauvegarde en cookie
- Vérification des rôles à chaque page (`auth.php`)
- Déconnexion automatique si compte bloqué

## 📅 Phases du projet

| Phase | Contenu |
|-------|---------|
| **Phase 1** | HTML + CSS statique, charte graphique |
| **Phase 2** | PHP dynamique, inscription, connexion, commandes, paiement CYBank |
| **Phase 3** | JavaScript, requêtes asynchrones, filtres, profil modifiable, mode sombre |
| **Phase 4** | Bonnes pratiques, soutenance finale |

## ✍️ Auteurs

Projet réalisé par : *(à compléter)*

Encadrants : C. Le Breton & R. Grignon — CY Tech 2025-2026
