<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

$idUtilisateur = $_SESSION['id_utilisateur'];

// Récupérer les groupes actifs auxquels l'utilisateur a accès
$stmt = $dbh->prepare("SELECT channel.id_channel, channel.nom_du_channel 
                       FROM channel 
                       INNER JOIN acces ON channel.id_channel = acces.id_channel 
                       WHERE acces.id_utilisateur = :id_utilisateur 
                       AND channel.est_actif = 1 
                       AND channel.nom_du_channel IS NOT NULL");
$stmt->execute(['id_utilisateur' => $idUtilisateur]);
$channels = $stmt->fetchAll();
