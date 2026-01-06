<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est administrateur
requireAdmin();

$message = ''; // Initialiser la variable pour les messages

// Si la méthode de la requête est POST et que le bouton de soumission est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['submit_button'])) {
    // Valider le token CSRF
    if (!csrf_validate()) {
        die("Token de sécurité invalide.");
    }

    // Vérifier que le tableau des groupes sélectionnés n'est pas vide et est bien un tableau
    if (!empty($_POST['espace']) && is_array($_POST['espace'])) {
        // Créer des placeholders pour les IDs des groupes sélectionnés
        $ids_placeholders = implode(',', array_fill(0, count($_POST['espace']), '?'));

        // Préparer la requête pour réactiver les groupes sélectionnés
        $query = $dbh->prepare("UPDATE channel SET est_actif = 1 WHERE id_channel IN ($ids_placeholders)");
        // Exécuter la requête en utilisant les IDs des groupes sélectionnés
        $query->execute($_POST['espace']);

        // Logger l'événement de sécurité
        logSecurityEvent('Réactivation de groupes', 'Les groupes : ' . implode(', ', $_POST['espace']) . ' ont été réactivés');

        // Stocker un message de confirmation dans la session et rediriger vers la même page
        $_SESSION['message'] = "Les groupes sélectionnés ont été réactivés avec succès.";
        header('Location: /?page=reactiver-espace');
        exit;
    } else {
        // Si aucun groupe n'a été sélectionné, afficher un message d'erreur
        $message = "Veuillez sélectionner au moins un groupe.";
    }
}

// Récupérer la liste des groupes désactivés
$query = $dbh->query("SELECT id_channel, nom_du_channel FROM channel WHERE est_actif = 0");
$espaces = $query->fetchAll(); // Récupérer tous les groupes désactivés

// Afficher le message de confirmation ou d'erreur si existant
$message = $message ?: ($_SESSION['message'] ?? '');
// Supprimer le message de la session après l'avoir récupéré pour qu'il ne soit affiché qu'une seule fois
unset($_SESSION['message']);
