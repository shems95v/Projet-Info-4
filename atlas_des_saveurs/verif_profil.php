<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user"])) { echo json_encode(["error" => "non connecté"]); exit(); }

$data = json_decode(file_get_contents("data/users.json"), true);
$idUser = $_SESSION["user"]["id"];
$champ = $_POST["champ"] ?? "";
$valeur = $_POST["valeur"] ?? "";

$erreur = "";
foreach ($data as $u) {
    if ($u["id"] == $idUser) continue;
    if ($champ === "email" && $u["email"] === $valeur) $erreur = "Email déjà utilisé";
    if ($champ === "login" && $u["login"] === $valeur) $erreur = "Login déjà utilisé";
    if ($champ === "telephone" && $u["telephone"] === $valeur) $erreur = "Numéro déjà utilisé";
}
echo json_encode(["erreur" => $erreur]);