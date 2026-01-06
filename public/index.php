<?php
/**
 * Point d'entrée principal de l'application MSN Connect
 * Gère le routing et charge les pages demandées
 */

// Charger le bootstrap pour initialiser l'application de manière sécurisée
require_once __DIR__ . '/../src/bootstrap.php';

// Cette ligne récupère la valeur de la variable 'page' dans l'URL (par exemple, index.php?page=contact). Si 'page' n'est pas défini, elle utilise 'accueil' par défaut. La fonction filter_var nettoie cette valeur pour éviter des caractères spéciaux potentiellement dangereux.
$page = filter_var($_GET['page'] ?? 'accueil', FILTER_SANITIZE_SPECIAL_CHARS);


//Cette ligne crée le chemin du fichier de la page à partir de la variable 'page'. Par exemple, si page=contact, le chemin sera ../src/pages/contact.php.
$chemin = "../src/pages/$page.php";


//Cette partie détermine quel modèle de mise en page utiliser en fonction de la page demandée. Si la page est connexion ou mdp-reset, elle utilise layout-deconnecte. Sinon, elle utilise layout-connecte.
$pagesDeConnexion = ['connexion', 'mdp-reset', 'reset_password'];
$layout = in_array($page, $pagesDeConnexion) ? 'layout-deconnecte' : 'layout-connecte';



// Vérifie si le fichier de la page existe
if (file_exists($chemin)) {
    // Note: data-connect.php est déjà chargé via bootstrap.php
    require $chemin;  // Charge le fichier de la page à afficher
    require "../templates/$layout.html.php"; // Charge le modèle de mise en page
} else {
    // Si le fichier de la page n'existe pas, renvoie une erreur 404
    header('HTTP/1.1 404 Not Found');
    require '../templates/404.html.php';  // Affiche la page d'erreur 404
    exit;
}
