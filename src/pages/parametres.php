<?php
requireAuth();

// Récupérer l'ID de l'utilisateur connecté
$idUtilisateur = $_SESSION['id_utilisateur'];

// Si l'utilisateur est connecté, récupérer ses informations depuis la base de données
if ($idUtilisateur) {
    $query = $dbh->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
    $query->execute(['id_utilisateur' => $idUtilisateur]);
    $utilisateur = $query->fetch();

    // Récupérer les informations de l'utilisateur, ou définir des valeurs par défaut
    $nomUtilisateur = $utilisateur['nom'] ?? '';
    $prenomUtilisateur = $utilisateur['prenom'] ?? '';
    $emailUtilisateur = $utilisateur['email'] ?? '';
    $avatar = $utilisateur['avatar'] ?? 'default-avatar.png'; // Utiliser un avatar par défaut si non défini
}

// Initialiser les messages pour la modification de l'avatar et du mot de passe
$messageAvatar = '';
$messageMdp = '';
$messageSuccee = '';

// Fonction pour valider un mot de passe fort (majuscule, minuscule, chiffre, caractère spécial, au moins 8 caractères)
function validerMotDePasse($motDePasse)
{
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $motDePasse);
}

// Si le formulaire de modification de mot de passe est soumis
if (isset($_POST['modifier_mdp'])) {
    if (!csrf_validate()) {
        $messageMdp = "Token de sécurité invalide.";
        logSecurityEvent('CSRF_FAIL', 'Tentative de CSRF sur modification de mot de passe');
    } else {
        $ancienMdp = $_POST['ancien_mdp'];
        $nouveauMdp = $_POST['nouveau_mdp'];
        $confirmerMdp = $_POST['confirmer_mdp'];

        // Vérifier que l'ancien mot de passe est correct
        if (password_verify($ancienMdp, $utilisateur['mot_de_passe'])) {
            // Vérifier que les nouveaux mots de passe correspondent
            if ($nouveauMdp === $confirmerMdp) {
                // Valider le nouveau mot de passe avec les critères de sécurité
                if (validerMotDePasse($nouveauMdp)) {
                    // Hacher le nouveau mot de passe et mettre à jour la base de données
                    $nouveauMdpHash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
                    $updateQuery = $dbh->prepare("UPDATE utilisateur SET mot_de_passe = :nouveau_mdp WHERE id_utilisateur = :id_utilisateur");
                    $updateQuery->execute([
                        'nouveau_mdp' => $nouveauMdpHash,
                        'id_utilisateur' => $idUtilisateur
                    ]);
                    $messageSuccee = "Mot de passe modifié avec succès.";
                    logSecurityEvent('PASSWORD_CHANGE', 'Mot de passe modifié avec succès');
                } else {
                    // Le nouveau mot de passe ne respecte pas les critères de sécurité
                    $messageMdp = "Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                }
            } else {
                // Les nouveaux mots de passe ne correspondent pas
                $messageMdp = "Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            // L'ancien mot de passe est incorrect
            $messageMdp = "L'ancien mot de passe est incorrect.";
            logSecurityEvent('PASSWORD_CHANGE_FAILED', 'Tentative de modification de mot de passe avec ancien mot de passe incorrect');
        }
    }
}

// Si le formulaire de modification d'avatar est soumis et que l'upload est valide
if (isset($_POST['modifier_avatar']) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    if (!csrf_validate()) {
        $messageAvatar = "Token de sécurité invalide.";
        logSecurityEvent('CSRF_FAIL', 'Tentative de CSRF sur modification d\'avatar');
    } else {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileType = $_FILES['avatar']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Extensions autorisées et taille maximale de 5MB
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions) && $fileSize <= 5000000) { // Taille max de 5MB
            // Déplacer l'image téléchargée dans le dossier 'uploads'
            $uploadFileDir = './uploads/';
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            // Enregistrer le nouvel avatar dans la base de données
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                try {
                    $updateQuery = $dbh->prepare("UPDATE utilisateur SET avatar = :avatar WHERE id_utilisateur = :id_utilisateur");
                    $updateQuery->execute([
                        'avatar' => $newFileName,
                        'id_utilisateur' => $idUtilisateur
                    ]);
                    $messageAvatar = "Avatar modifié avec succès.";
                    $avatar = $newFileName;
                    logSecurityEvent('AVATAR_UPLOAD', 'Avatar modifié avec succès');
                } catch (PDOException $e) {
                    $messageAvatar = "Erreur lors de la mise à jour de l'avatar : " . $e->getMessage();
                    logSecurityEvent('AVATAR_UPLOAD_FAILED', 'Erreur lors de la mise à jour de l\'avatar');
                }
            } else {
                // Erreur lors du téléchargement de l'avatar
                $messageAvatar = "Une erreur est survenue lors du téléchargement de l'avatar.";
                logSecurityEvent('AVATAR_UPLOAD_FAILED', 'Erreur lors du déplacement du fichier');
            }
        } else {
            // Extension ou taille du fichier non autorisées
            $messageAvatar = "Seuls les fichiers avec les extensions suivantes sont autorisés : " . implode(', ', $allowedfileExtensions) . " et de taille maximale 5MB.";
            logSecurityEvent('AVATAR_UPLOAD_INVALID_FILE', 'Extension ou taille du fichier non autorisée');
        }
    }
}

// Si le formulaire de suppression de l'avatar est soumis
if (isset($_POST['supprimer_avatar'])) {
    if (!csrf_validate()) {
        $messageAvatar = "Token de sécurité invalide.";
        logSecurityEvent('CSRF_FAIL', 'Tentative de CSRF sur suppression d\'avatar');
    } else {
        // Supprimer l'avatar de l'utilisateur et utiliser l'avatar par défaut
        $updateQuery = $dbh->prepare("UPDATE utilisateur SET avatar = NULL WHERE id_utilisateur = :id_utilisateur");
        $updateQuery->execute(['id_utilisateur' => $idUtilisateur]);
        $avatar = 'default-avatar.png'; // Utiliser l'avatar par défaut
        $messageAvatar = "Avatar supprimé avec succès.";
        logSecurityEvent('AVATAR_DELETE', 'Avatar supprimé avec succès');
    }
}
