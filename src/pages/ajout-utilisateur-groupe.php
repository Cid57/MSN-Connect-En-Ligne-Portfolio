<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté et administrateur
if (empty($_SESSION['id_utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

if ($_SESSION['est_admin'] == 0) {
    header('Location: /');
    exit;
}

$message = ''; // Initialiser la variable pour les messages
$errors = []; // Initialiser le tableau des erreurs
$id_channel = $_GET['id_channel'] ?? null; // Récupérer l'ID du channel à partir de la requête GET, ou le définir à null

// Si la méthode de requête est POST et que le bouton de soumission est cliqué
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['submit_button'])) {
    // Valider le token CSRF
    if (!csrf_validate()) {
        $errors['csrf'] = "Token invalide.";
    } else {
        // Vérifier que le tableau des utilisateurs sélectionnés n'est pas vide et qu'il s'agit bien d'un tableau
        if (!empty($_POST['utilisateur']) && is_array($_POST['utilisateur'])) {
            $id_channel = filter_input(INPUT_POST, 'id_channel', FILTER_SANITIZE_NUMBER_INT); // Récupérer et valider l'ID du channel

            if (!$id_channel) {
                $errors['id_channel'] = "ID du channel invalide.";
            } else {
                // Préparer la requête pour insérer les utilisateurs dans le groupe avec leur accès
                $query = $dbh->prepare("INSERT INTO acces (id_utilisateur, id_channel, est_gestionnaire) VALUES (?, ?, 0)");
                // Exécuter la requête pour chaque utilisateur sélectionné
                foreach ($_POST['utilisateur'] as $id_utilisateur) {
                    $id_utilisateur = filter_var($id_utilisateur, FILTER_VALIDATE_INT);
                    if ($id_utilisateur !== false) {
                        $query->execute([$id_utilisateur, $id_channel]);
                    }
                }

                // Logger l'événement
                error_log("Ajout d'utilisateurs au groupe - Channel ID: $id_channel, Admin: " . $_SESSION['id_utilisateur']);

                // Stocker un message de succès dans la session et rediriger vers la même page
                $_SESSION['message'] = "Les utilisateurs sélectionnés ont été ajoutés avec succès au groupe.";
                header("Location: /?page=ajout-utilisateur-groupe&id_channel=$id_channel");
                exit;
            }
        } else {
            // Si aucun utilisateur n'a été sélectionné, afficher un message d'erreur
            $errors['utilisateurs'] = "Veuillez sélectionner au moins un utilisateur.";
        }
    }

    // Récupérer le premier message d'erreur s'il en existe
    $message = $errors ? reset($errors) : '';
}

// Récupérer la liste des utilisateurs qui ne sont pas encore dans le groupe
$query = $dbh->prepare("
    SELECT u.id_utilisateur, u.prenom, u.nom, u.email 
    FROM utilisateur u 
    WHERE u.est_actif = 1
    AND u.id_utilisateur NOT IN (
        SELECT a.id_utilisateur 
        FROM acces a 
        WHERE a.id_channel = ?
    )
");
$query->execute([$id_channel]); // Exécuter la requête avec l'ID du channel
$utilisateurs = $query->fetchAll(); // Récupérer tous les utilisateurs qui ne sont pas encore dans le groupe

// Afficher le message de succès ou d'erreur si existant
$message = $message ?: ($_SESSION['message'] ?? '');
// Supprimer le message de succès de la session pour qu'il ne s'affiche qu'une fois
unset($_SESSION['message']);
