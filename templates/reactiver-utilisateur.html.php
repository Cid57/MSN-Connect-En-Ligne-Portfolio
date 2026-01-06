<div class="container">
    <!-- Votre barre latérale -->
    <div class="sidebar">
        <!-- Contenu de la barre latérale -->
    </div>

    <div class="main-content">
        <!-- Banderole "gestion administrateur" -->
        <div class="gestion-administrateur">
            <img class="user-icon" src="assets/img/ajouter-user.png" alt="Icone utilisateur">
            <h1>Réactiver un utilisateur</h1>
            <div class="actions">
                <a href="/?page=admin-ajouter-utilisateur"><img class="action-icon" src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
                <a href="/"><img class="action-icon" src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
            </div>
        </div>
        <!-- Fin de banderole -->

        <div class="content">
            <?php if ($message) : ?>
                <div class="message"><?= e($message) ?></div>
            <?php endif; ?>

            <form method="post" class="user-form">
                <?= csrf_field() ?>
                <?php foreach ($utilisateurs as $utilisateur) : ?>
                    <label class="user-checkbox">
                        <input type="checkbox" name="utilisateur[]" value="<?= e($utilisateur['id_utilisateur']) ?>">
                        <p><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) . ' (' . e($utilisateur['email']) . ')' ?></p>
                    </label>
                <?php endforeach; ?>
                <div class="sticky-button-container">
                    <input type="submit" name="submit_button" value="Réactiver les utilisateurs sélectionnés" class="submit-button">
                </div>
            </form>
        </div>
    </div>
</div>