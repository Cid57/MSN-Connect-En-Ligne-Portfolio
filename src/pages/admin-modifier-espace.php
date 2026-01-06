<?php

requireAdmin();

// Mettre à jour le nom du groupe si le formulaire est soumis
if (!empty($_POST['submit_button'])) {

    $errors = [];

    // Validation CSRF
    if (!csrf_validate()) {
        $errors['csrf'] = "Token invalide.";
        logSecurityEvent('CSRF_FAIL', 'Tentative de modification d\'espace avec token invalide');
    } else {
        $id_channel = $_POST['id_channel'];
        $nom_du_channel = $_POST['nom_du_channel'];

        $query = $dbh->prepare("UPDATE channel SET nom_du_channel = :nom_du_channel WHERE id_channel = :id_channel AND est_groupe = 1 AND est_actif = 1");
        $query->execute([
            'nom_du_channel' => $nom_du_channel,
            'id_channel' => $id_channel
        ]);

        logSecurityEvent('SPACE_UPDATED', "Espace modifié - ID: {$id_channel}, Nouveau nom: {$nom_du_channel}");

        // Stocker le message de confirmation dans la session
        $_SESSION['message'] = "L'espace a été modifié avec succès.";
    }
}

// Récupérer la liste des groupes actifs
$query = $dbh->query("SELECT id_channel, nom_du_channel FROM channel WHERE est_groupe = 1 AND est_actif = 1");
$channels = $query->fetchAll();

// Récupérer le message de confirmation de la session s'il existe
$message = $_SESSION['message'] ?? '';
// Supprimer le message après l'avoir récupéré
unset($_SESSION['message']);
