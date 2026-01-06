<?php
// Vérifier que les paramètres nécessaires sont passés dans l'URL
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = trim($_GET['email']);
    $token = trim($_GET['token']);

    // Vérifier si le token est valide et non expiré
    $stmt = $dbh->prepare("SELECT * FROM password_reset WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($token, $user['token']) && strtotime($user['expires']) > time()) {
        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!csrf_validate()) {
                $error = "Token de sécurité invalide.";
                logSecurityEvent('CSRF_FAIL', 'Tentative de CSRF sur reset_password');
            } elseif (isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
                $new_password = trim($_POST['new_password']);
                $confirm_password = trim($_POST['confirm_password']);

                // Validation des mots de passe
                if (empty($new_password) || empty($confirm_password)) {
                    $error = "Veuillez remplir tous les champs.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "Les mots de passe ne correspondent pas.";
                } elseif (strlen($new_password) < 8) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères.";
                } else {
                    // Mettre à jour le mot de passe dans la base de données
                    $stmt = $dbh->prepare("UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE email = :email");
                    $stmt->execute([
                        'mot_de_passe' => password_hash($new_password, PASSWORD_DEFAULT),
                        'email' => $email,
                    ]);

                    // Supprimer le token après réinitialisation
                    $stmt = $dbh->prepare("DELETE FROM password_reset WHERE email = :email");
                    $stmt->execute(['email' => $email]);

                    // Rediriger l'utilisateur vers la page de connexion
                    $_SESSION['success'] = "Votre mot de passe a été mis à jour avec succès.";
                    logSecurityEvent('PASSWORD_RESET_SUCCESS', 'Mot de passe réinitialisé avec succès');
                    header('Location: /?page=connexion');
                    exit;
                }
            }
        }
    } else {
        $error = "Le lien est invalide ou a expiré.";
        logSecurityEvent('PASSWORD_RESET_INVALID_TOKEN', 'Tentative de réinitialisation avec token invalide ou expiré');
    }
} else {
    $error = "Paramètres invalides.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>

<body>
    <h1>Réinitialisation du mot de passe</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!isset($error) || empty($error)): ?>
        <form method="POST">
            <label for="new_password">Nouveau mot de passe :</label><br>
            <input type="password" id="new_password" name="new_password" required><br><br>

            <label for="confirm_password">Confirmer le mot de passe :</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <button type="submit">Réinitialiser le mot de passe</button>
        </form>
    <?php endif; ?>
</body>

</html>