<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

// Récupérer tous les utilisateurs actifs sauf l'utilisateur connecté
$stmt = $dbh->prepare("SELECT id_utilisateur, nom, prenom, email FROM utilisateur WHERE id_utilisateur <> :id_utilisateur AND est_actif = 1");
$stmt->execute([
    'id_utilisateur' => $_SESSION['id_utilisateur']
]);
$utilisateurs = $stmt->fetchAll();
