<?php

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion'); // Rediriger vers la page de connexion si non connecté
    exit; // Arrêter l'exécution du script après la redirection
}

// Vérifier si l'ID du channel est présent dans l'URL, sinon rediriger vers la page d'accueil
if (empty($_GET['id_channel'])) {
    header('Location: /'); // Rediriger vers la page d'accueil si l'ID du channel est manquant
    exit; // Arrêter l'exécution du script après la redirection
}

// Préparer la requête pour désactiver le channel spécifié en le mettant à inactif (est_actif = 0)
$query = $dbh->prepare("UPDATE channel SET est_actif = 0 WHERE id_channel = :id_channel");

// Exécuter la requête avec l'ID du channel passé dans l'URL via $_GET
$query->execute([
    'id_channel' => $_GET['id_channel'], // Lier l'ID du channel à la requête préparée
]);
