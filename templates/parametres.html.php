<div class="gestion-administrateur">
    <img src="assets/img/logo-admin.png" alt="icone logo admin">
    <h1>Paramètres du profil</h1>
    <div class="actions">
        <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>

<div class="container-profile">
    <div class="content-block">
        <div class="content">
            <?php if ($messageAvatar) : ?>
                <div class="message"><?= e($messageAvatar) ?></div>
            <?php endif; ?>
            <p><strong>NOM : </strong><?= e($nomUtilisateur) ?></p>
            <p><strong>PRENOM : </strong><?= e($prenomUtilisateur) ?></p>
            <p><strong>Email :</strong> <?= e($emailUtilisateur) ?></p>
            <p><strong>Mot de passe : </strong>*******</p>
            <form method="post" enctype="multipart/form-data" class="avatar-upload">
                <?= csrf_field() ?>
                <label class="custom-file-upload">
                    <input type="file" name="avatar" id="avatar" accept="image/*">
                    Sélectionner un avatar
                </label>
                <div class="avatar">
                    <img id="avatarPreview" class="avatar-preview" src="uploads/<?= e($avatar) ?>" alt="Avatar de l'utilisateur">
                </div>
                <button type="submit" name="modifier_avatar">Enregistrer l'avatar</button>
            </form>
            <form method="post" class="avatar-delete">
                <?= csrf_field() ?>
                <button type="submit" name="supprimer_avatar">Supprimer l'avatar</button>
            </form>
        </div>
    </div>

    <div class="form-block">
        <div class="container-modifier-mdp">
            <?php if ($messageMdp) : ?>
                <div class="message"><?= e($messageMdp) ?></div>
            <?php endif; ?>
            <?php if ($messageSuccee) : ?>
                <div class="message_succee"><?= e($messageSuccee) ?></div>
            <?php endif; ?>


            <form method="post">
                <?= csrf_field() ?>
                <h2>Changer de mot de passe</h2>
                <br>
                <label for="ancien_mdp">Ancien mot de passe :</label>
                <div class="group">
                    <input type="password" name="ancien_mdp" id="ancien_mdp" required>
                    <div class="mdp-icon">
                        <i data-feather="eye" class="eye"></i>
                        <i data-feather="eye-off" class="eye-off" style="display: none;"></i>
                    </div>
                </div>

                <label for="nouveau_mdp">Nouveau mot de passe :</label>
                <div class="group">
                    <input type="password" name="nouveau_mdp" id="nouveau_mdp" required>
                    <div class="mdp-icon">
                        <i data-feather="eye" class="eye"></i>
                        <i data-feather="eye-off" class="eye-off" style="display: none;"></i>
                    </div>
                </div>

                <label for="confirmer_mdp">Confirmer le nouveau mot de passe :</label>
                <div class="group">
                    <input type="password" name="confirmer_mdp" id="confirmer_mdp" required>
                    <div class="mdp-icon">
                        <i data-feather="eye" class="eye"></i>
                        <i data-feather="eye-off" class="eye-off" style="display: none;"></i>
                    </div>
                </div>

                <button type="submit" name="modifier_mdp">Modifier le mot de passe</button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/feather-icons"></script>