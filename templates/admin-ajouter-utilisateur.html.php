<div class="container">
    <!-- Votre barre latérale -->
    <div class="sidebar">
        <!-- Contenu de la barre latérale -->
    </div>

    <div class="main-content">
        <!-- Banderole "gestion administrateur" -->
        <div class="gestion-administrateur">
            <img class="icon" src="assets/img/ajouter-user.png" alt="Icone ajouter user">
            <h1>Ajouter un utilisateur</h1>
            <div class="actions">
                <a href="/?page=administrateur"><img class="action-icon" src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
                <a href="/"><img class="action-icon" src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
            </div>
        </div>
        <!-- Fin de banderole -->

        <!-- Message d'ajout d'un utilisateur -->
        <div class="content">
            <?php if (!empty($success)) : ?>
                <p class="success"><?= e($success) ?></p>
            <?php endif; ?>

            <?php if (!empty($errors)) : ?>
                <div class="errors">
                    <?php foreach ($errors as $error) : ?>
                        <p class="error"><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="user-form">
                <?= csrf_field() ?>
                <!-- prénom -->
                <div class="form-group">
                    <label for="inscriptionPrenom">Prénom</label>
                    <input type="text" id="inscriptionPrenom" name="inscription_prenom" placeholder="Entrez le prénom ici" value="<?= e($_POST['inscription_prenom'] ?? '') ?>">
                </div>

                <!-- nom  -->
                <div class="form-group">
                    <label for="inscriptionNom">Nom</label>
                    <input type="text" id="inscriptionNom" name="inscription_nom" placeholder="Entrez le nom ici" value="<?= e($_POST['inscription_nom'] ?? '') ?>">
                </div>

                <!-- email -->
                <div class="form-group">
                    <label for="inscriptionEmail">E-mail</label>
                    <input type="email" id="inscriptionEmail" name="inscription_email" placeholder="Entrez l'email ici" value="<?= e($_POST['inscription_email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="inscriptionAdmin">Administrateur ?</label>
                    <input type="checkbox" id="inscriptionAdmin" name="inscription_admin" <?= isset($_POST['inscription_admin']) ? 'checked' : '' ?> />
                </div>

                <!-- Bouton d'inscription -->
                <div class="form-group">
                    <input type="submit" id="inscriptionAdminBouton" name="inscription_admin_bouton" value="Inscrire la personne">
                </div>

                <!-- Bouton réactiver une personne -->
                <div class="form-group reactiver-personne">
                    <a href="/?page=reactiver-utilisateur" class="btn-reactiver">Réactiver une personne</a>
                </div>
            </form>
        </div>
    </div>
</div>