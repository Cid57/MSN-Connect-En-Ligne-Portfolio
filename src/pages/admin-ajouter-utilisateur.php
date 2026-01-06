<?php
// Les sessions et sécurité sont gérées par bootstrap.php

// Vérification des permissions
requireAdmin();

$success = ''; // Variable pour les messages de succès
$errors = []; // Tableau pour stocker les erreurs

// Vérifier si le formulaire a été soumis
if (isset($_POST['inscription_admin_bouton'])) {
    // SÉCURITÉ: Valider le token CSRF
    if (!csrf_validate()) {
        $errors['csrf'] = "Token de sécurité invalide. Veuillez réessayer.";
        logSecurityEvent('CSRF_FAIL', 'Tentative d\'ajout utilisateur avec token CSRF invalide');
    } else {
        // Validation des champs
        $prenom = !empty($_POST['inscription_prenom']) ? trim($_POST['inscription_prenom']) : '';
        $nom = !empty($_POST['inscription_nom']) ? trim($_POST['inscription_nom']) : '';
        $email = !empty($_POST['inscription_email']) ? trim($_POST['inscription_email']) : '';

        if (empty($prenom)) {
            $errors['prenom'] = 'Le champ "prénom" est obligatoire.';
        }
        if (empty($nom)) {
            $errors['nom'] = 'Le champ "nom" est obligatoire.';
        }
        if (empty($email)) {
            $errors['email'] = 'Le champ "email" est obligatoire.';
        }

        // Validation de l'email
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide.';
        }

        // Vérifier si l'email existe déjà dans la base de données
        if (!empty($email) && !isset($errors['email'])) {
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors['email'] = "Cet email est déjà utilisé.";
            }
        }

        // Si aucune erreur, procéder à l'insertion
        if (empty($errors)) {
            try {
                // Insérer un nouvel utilisateur
                $stmt = $dbh->prepare("INSERT INTO utilisateur (prenom, nom, email, date_de_creation, est_admin, est_actif)
                    VALUES (:prenom, :nom, :email, NOW(), :est_admin, 1)");
                $stmt->execute([
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email,
                    'est_admin' => isset($_POST['inscription_admin']) ? 1 : 0,
                ]);

                // Générer un token pour la réinitialisation
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600);

                // Supprimer les anciens tokens pour cet utilisateur
                $dbh->prepare("DELETE FROM password_reset WHERE email = ?")->execute([$email]);

                // Insérer le nouveau token hashé
                $stmt = $dbh->prepare("INSERT INTO password_reset (email, token, expires) VALUES (?, ?, ?)");
                $stmt->execute([$email, password_hash($token, PASSWORD_DEFAULT), $expires]);

                // Envoyer un email avec le lien
                $reset_link = "http://localhost/?page=reset_password&token=$token&email=" . urlencode($email);

                // Configuration de l'email
                $to = $email;
                $subject = "Définir votre mot de passe MSN Connect";
                $headers = "From: contact@cindysinger.fr\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                $message = "Bonjour,\n\nUn compte a été créé pour vous. Cliquez sur le lien ci-dessous pour définir votre mot de passe :\n\n";
                $message .= $reset_link . "\n\nCe lien expirera dans une heure.\n\nCordialement,\nL'équipe MSN Connect.";

                // Envoi de l'email
                if (mail($to, $subject, $message, $headers)) {
                    $success = "Utilisateur créé avec succès. Un email a été envoyé à $email.";

                    // Logger l'événement
                    logSecurityEvent('USER_CREATED', 'Nouvel utilisateur créé par admin', [
                        'email' => $email,
                        'admin_id' => $_SESSION['id_utilisateur']
                    ]);
                } else {
                    $errors['mail'] = "Erreur lors de l'envoi de l'email.";
                }
            } catch (PDOException $e) {
                logError("Erreur création utilisateur", ['error' => $e->getMessage()]);
                $errors['db'] = "Erreur lors de l'enregistrement dans la base de données.";
            }
        }
    }
}
