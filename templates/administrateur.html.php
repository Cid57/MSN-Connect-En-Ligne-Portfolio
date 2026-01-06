<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Administrateur</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Banderole "gestion administrateur" -->
    <div class="gestion-administrateur">
        <img src="assets/img/logo-admin.png" alt="icone logo admin">
        <h1>Gestion administrateur</h1>
        <div class="actions">
            <a href="/?page=parametres"><img src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
            <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
        </div>
    </div>
    <!-- Fin banderole -->

    <!-- Gestion des utilisateurs et espaces -->
    <div class="content">
        <div class="administrateur">
            <h2>Gestion des utilisateurs</h2>
            <div class="grid-container">
                <div class="grid-item">
                    <img src="assets/img/ajouter-user.png" alt="Icone ajouter user">
                    <a href="/?page=admin-ajouter-utilisateur"><input type="button" value="Ajouter utilisateur"></a>
                </div>
                <div class="grid-item">
                    <img src="assets/img/modifier-user.png" alt="Icone modifier user">
                    <a href="/?page=admin-modifier-utilisateurs"><input type="button" value="Modifier utilisateur"></a>
                </div>
                <div class="grid-item">
                    <img src="assets/img/supprimer-user.png" alt="Icone supprimer user">
                    <a href="/?page=admin-supprimer-utilisateur"><input type="button" value="Désactivé utilisateur"></a>
                </div>
            </div>
        </div>

        <div class="administrateur">
            <h2>Gestion des groupes</h2>
            <div class="grid-container">
                <div class="grid-item">
                    <img src="assets/img/bulle-bleu.png" alt="Icone ajouter espace">
                    <a href="/?page=admin-ajouter-espace"><input type="button" value="Ajouter groupe"></a>
                </div>
                <div class="grid-item">
                    <img src="assets/img/bulle-jaune.png" alt="Icone modifier espace">
                    <a href="/?page=admin-modifier-espace"><input type="button" value="Modifier groupe"></a>
                </div>
                <div class="grid-item">
                    <img src="assets/img/bulle-rouge.png" alt="Icone supprimer espace">
                    <a href="/?page=admin-supprimer-espace"><input type="button" value="Supprimer groupe"></a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>