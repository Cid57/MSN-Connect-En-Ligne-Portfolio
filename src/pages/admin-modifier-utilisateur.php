<?php
requireAdmin();

// Récupérer l'ID de l'utilisateur à modifier
$idUtilisateurModifier = $_GET['id'] ?? null;

if ($idUtilisateurModifier) {
    // Récupérer les informations de l'utilisateur à modifier
    $query = $dbh->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
    $query->execute(['id_utilisateur' => $idUtilisateurModifier]);
    $utilisateurModifier = $query->fetch();

    if (!$utilisateurModifier) {
        // Rediriger si l'utilisateur n'existe pas
        header('Location: /?page=administrateur');
        exit;
    }

    // Si le formulaire est soumis, traiter les modifications
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_validate()) {
            $errors['csrf'] = "Token de sécurité invalide.";
            logSecurityEvent('CSRF_FAIL', 'Tentative de CSRF sur admin-modifier-utilisateur');
        } else {
            $prenom = $_POST['prenom'];
            $nom = $_POST['nom'];
            $email = $_POST['email'];

            // Mettre à jour les informations de l'utilisateur
            $updateQuery = $dbh->prepare("UPDATE utilisateur SET prenom = :prenom, nom = :nom, email = :email WHERE id_utilisateur = :id_utilisateur");
            $updateQuery->execute([
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'id_utilisateur' => $idUtilisateurModifier,
            ]);

            logSecurityEvent('USER_MODIFICATION', 'Utilisateur modifié', ['user_id' => $idUtilisateurModifier]);
            // Rediriger après mise à jour
            header('Location: /?page=admin-modifier-utilisateurs');
            exit;
        }
    }
} else {
    // Rediriger si l'ID de l'utilisateur n'est pas fourni
    header('Location: /?page=administrateur');
    exit;
}
