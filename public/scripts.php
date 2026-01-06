<?php

session_start();

require '../src/data/data-connect.php';  // Inclut les données de connexion

// Nettoie et récupère la variable 'script' des paramètres GET
$script = filter_var($_GET['script'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Construit le chemin vers le fichier du script à afficher
$chemin = "../src/scripts/$script.php";

// Vérifie si le fichier du script existe
if(file_exists($chemin)){
    require $chemin;  // Charge le fichier du script à afficher
} else {
    // Si le fichier du script n'existe pas, redirige vers l'accueil
    header('Location: /');
    exit;
}