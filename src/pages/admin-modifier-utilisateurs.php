<?php

if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

if ($_SESSION['est_admin'] == 0) {
    header('Location: /');
    exit;
}

// Récupérer la liste des utilisateurs
$query = $dbh->query("SELECT * FROM utilisateur");
$utilisateurs = $query->fetchAll();
