<?php

header('Content-Type: application/json; charset=utf-8');

/* Chargement des données */
$chemin = __DIR__ . '/data/plats.json';
if (!file_exists($chemin)) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Fichier plats.json introuvable']);
    exit;
}

$plats = json_decode(file_get_contents($chemin), true);
if (!is_array($plats)) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Données invalides']);
    exit;
}

/* Lecture des paramètres GET */
$types      = isset($_GET['types'])     ? (array) $_GET['types']     : [];
$saveurs    = isset($_GET['saveurs'])   ? (array) $_GET['saveurs']   : [];
$sansGluten  = isset($_GET['sansGluten'])  && $_GET['sansGluten']  === '1';
$sansLactose = isset($_GET['sansLactose']) && $_GET['sansLactose'] === '1';
$vegetarien  = isset($_GET['vegetarien'])  && $_GET['vegetarien']  === '1';
$tri         = isset($_GET['tri'])      ? trim($_GET['tri'])         : '';
// On met en minuscule pour une recherche insensible à la casse
$recherche   = isset($_GET['recherche']) ? mb_strtolower(trim($_GET['recherche'])) : '';

/* ---- Filtrage ---- */
$resultats = array_filter($plats, function ($plat) use (
    $types, $saveurs, $sansGluten, $sansLactose, $vegetarien, $recherche
) {
    /* Filtre par type */
    if (!empty($types) && !in_array($plat['type'] ?? '', $types, true)) {
        return false;
    }

    /* Filtre par saveur */
    if (!empty($saveurs) && !in_array($plat['saveur'] ?? '', $saveurs, true)) {
        return false;
    }

    /* Filtres diététiques */
    if ($sansGluten  && !($plat['sansGluten']  ?? false)) return false;
    if ($sansLactose && !($plat['sansLactose'] ?? false)) return false;
    if ($vegetarien  && !($plat['vegetarien']  ?? false)) return false;

    /* Recherche textuelle (nom + description) */
    if ($recherche !== '') {
        $haystack = mb_strtolower(($plat['nom'] ?? '') . ' ' . ($plat['description'] ?? ''));
        if (mb_strpos($haystack, $recherche) === false) {
            return false;
        }
    }

    return true;
});

// array_filter conserve les clés d'origine, on réindexe pour avoir un tableau propre
$resultats = array_values($resultats);

/* ---- Tri ---- */
switch ($tri) {
    case 'prix_asc':
        usort($resultats, fn($a, $b) => $a['prix'] <=> $b['prix']);
        break;
    case 'prix_desc':
        usort($resultats, fn($a, $b) => $b['prix'] <=> $a['prix']);
        break;
    case 'popularite':
        // Tri décroissant par nombre de commandes
        usort($resultats, fn($a, $b) => ($b['nbCommandes'] ?? 0) <=> ($a['nbCommandes'] ?? 0));
        break;
    case 'nom_asc':
        usort($resultats, fn($a, $b) => strcmp($a['nom'] ?? '', $b['nom'] ?? ''));
        break;
    /* Par défaut : ordre du fichier */
}

echo json_encode([
    'total'   => count($resultats),
    'plats'   => $resultats,
]);