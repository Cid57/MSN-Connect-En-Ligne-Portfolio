<?php

// Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
if (!empty($_SESSION['id_utilisateur'])) {
    header('Location: /');
    exit;
}

// Vérifier si le formulaire de connexion a été soumis
if (isset($_POST['connexion-submit'])) {
    // Initialiser un tableau pour stocker les erreurs
    $errors = [];

    // SÉCURITÉ: Valider le token CSRF
    if (!csrf_validate()) {
        $errors['login_error'] = "Token de sécurité invalide. Veuillez réessayer.";
        logSecurityEvent('CSRF_FAIL', 'Tentative de connexion avec token CSRF invalide', [
            'email' => $_POST['email-connexion'] ?? 'non fourni'
        ]);
    } elseif (!empty($_POST['email-connexion']) && !empty($_POST['mdp-connexion'])) {
        // Vérifier que les champs email et mot de passe ne sont pas vides
        $emailConnexion = $_POST['email-connexion'];
        $mdpConnexion = $_POST['mdp-connexion'];

        // Vérifier que l'email est valide
        if (!filter_var($emailConnexion, FILTER_VALIDATE_EMAIL)) {
            $errors['email-connexion'] = 'Veuillez entrer une adresse email valide !';
        }

        // Si aucune erreur n'a été trouvée, continuer avec la vérification de l'utilisateur
        if (empty($errors)) {
            // Préparer et exécuter une requête pour trouver l'utilisateur par email
            $query = $dbh->prepare("SELECT * FROM utilisateur WHERE email = :email");
            $query->execute(['email' => $emailConnexion]);
            $utilisateur = $query->fetch();

            // Vérifier que l'utilisateur existe, que le mot de passe est correct, et que l'utilisateur est actif
            if ($utilisateur && password_verify($mdpConnexion, $utilisateur['mot_de_passe']) && $utilisateur['est_actif']) {
                // Initialiser la session utilisateur avec son id et son statut d'administrateur
                $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur']; // Id de l'utilisateur.
                $_SESSION['est_admin'] = $utilisateur['est_admin']; // Si l'utilisateur est admin (1 admin, 0 non admin)
                $_SESSION['utilisateur'] = $utilisateur; // Stocker les infos complètes

                // Logger l'événement de connexion réussie
                logSecurityEvent('LOGIN_SUCCESS', 'Connexion réussie', [
                    'user_id' => $utilisateur['id_utilisateur'],
                    'email' => $utilisateur['email']
                ]);

                // Rediriger l'utilisateur vers la page d'accueil après connexion réussie
                header("Location: /");
                exit;
            } else {
                // Si l'authentification échoue, ajouter un message d'erreur
                $errors['login_error'] = "Adresse email ou mot de passe incorrect.";

                // Logger l'échec de connexion
                logSecurityEvent('LOGIN_FAIL', 'Échec de connexion', [
                    'email' => $emailConnexion,
                    'reason' => !$utilisateur ? 'user_not_found' : (!$utilisateur['est_actif'] ? 'user_inactive' : 'wrong_password')
                ]);
            }
        }
    } else {
        // Si les champs ne sont pas remplis, ajouter un message d'erreur
        $errors['login_error'] = "Veuillez remplir tous les champs";
    }
}
