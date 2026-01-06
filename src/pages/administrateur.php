<?php
// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

// Vérifier si l'utilisateur a des droits d'administrateur, sinon le rediriger vers la page d'accueil
if ($_SESSION['est_admin'] == 0) {
    header('Location: /');
    exit;
}
