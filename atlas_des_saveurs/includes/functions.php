<?php

function lireJSON($fichier) {
    if (!file_exists($fichier)) {
        return [];
    }

    $contenu = file_get_contents($fichier);
    $data = json_decode($contenu, true);

    return is_array($data) ? $data : [];
}

function ecrireJSON($fichier, $data) {
    file_put_contents(
        $fichier,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function trouverUtilisateurParLogin($login, $password) {
    $users = lireJSON("data/users.json");

    foreach ($users as $user) {
        if (
            isset($user["login"], $user["password"]) &&
            $user["login"] === $login &&
            $user["password"] === $password
        ) {
            return $user;
        }
    }

    return null;
}

function trouverPlatParId($id) {
    $plats = lireJSON("data/plats.json");

    foreach ($plats as $plat) {
        if (isset($plat["id"]) && (int)$plat["id"] === (int)$id) {
            return $plat;
        }
    }

    return null;
}

function calculerTotalPanier($panier) {
    $total = 0;

    foreach ($panier as $idPlat => $quantite) {
        $plat = trouverPlatParId($idPlat);
        if ($plat) {
            $total += (float)$plat["prix"] * (int)$quantite;
        }
    }

    return $total;
}

function genererTransactionId() {
    return substr(strtoupper(md5(uniqid((string)rand(), true))), 0, 12);
}

function trouverCommandeParTransaction($transaction) {
    $commandes = lireJSON("data/commandes.json");

    foreach ($commandes as $commande) {
        if (isset($commande["transaction"]) && $commande["transaction"] === $transaction) {
            return $commande;
        }
    }

    return null;
}

function mettreAJourStatutCommande($transaction, $nouveauStatutPaiement, $nouveauStatutCommande) {
    $commandes = lireJSON("data/commandes.json");

    foreach ($commandes as &$commande) {
        if (isset($commande["transaction"]) && $commande["transaction"] === $transaction) {
            $commande["statut_paiement"] = $nouveauStatutPaiement;
            $commande["statut_commande"] = $nouveauStatutCommande;
            $commande["date_paiement"] = date("Y-m-d H:i:s");
            break;
        }
    }

    ecrireJSON("data/commandes.json", $commandes);
}
?>