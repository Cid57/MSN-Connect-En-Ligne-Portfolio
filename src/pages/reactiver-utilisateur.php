<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est administrateur
requireAdmin();

$message = '';

// Si la méthode de requête est POST et que le bouton de soumission est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['submit_button'])) {
    // Valider le token CSRF
    if (!csrf_validate()) {
        die("Token de sécurité invalide.");
    }

    // Vérifier que le tableau des utilisateurs sélectionnés n'est pas vide et est bien un tableau
    if (!empty($_POST['utilisateur']) && is_array($_POST['utilisateur'])) {
        // Créer des placeholders pour les IDs des utilisateurs sélectionnés
        $ids_placeholders = implode(',', array_fill(0, count($_POST['utilisateur']), '?'));

        // Préparer la requête pour réactiver les utilisateurs sélectionnés
        $query = $dbh->prepare("UPDATE utilisateur SET est_actif = 1 WHERE id_utilisateur IN ($ids_placeholders)");
        // Exécuter la requête en utilisant les IDs des utilisateurs sélectionnés
        $query->execute($_POST['utilisateur']);

        // Logger l'événement de sécurité
        logSecurityEvent('Réactivation d\'utilisateurs', 'Les utilisateurs : ' . implode(', ', $_POST['utilisateur']) . ' ont été réactivés');

        // Stocker un message de confirmation dans la session et rediriger vers la même page
        $_SESSION['message'] = "Les utilisateurs sélectionnés ont été réactivés avec succès.";
        header('Location: /?page=reactiver-utilisateur');
        exit;
    } else {
        // Si aucun utilisateur n'a été sélectionné, afficher un message d'erreur
        $message = "Veuillez sélectionner au moins un utilisateur.";
    }
}

// Récupérer la liste des utilisateurs désactivés (est_actif = 0)
$query = $dbh->query("SELECT id_utilisateur, prenom, nom, email FROM utilisateur WHERE est_actif = 0");
$utilisateurs = $query->fetchAll(); // Récupérer tous les utilisateurs désactivés

// Afficher le message de confirmation ou d'erreur si existant
$message = $message ?: ($_SESSION['message'] ?? '');
// Supprimer le message de la session après l'avoir récupéré pour qu'il ne soit affiché qu'une seule fois
unset($_SESSION['message']);
