<div class="password-reset-form">
    <div class="container">
        <h2>Réinitialisation du mot de passe</h2>

        <form method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <button type="submit" name="reset_password">Réinitialiser le mot de passe</button>
            </div>
        </form>

        <div class="button">
            <button> <a href="/?page=connexion">Revenir sur le formulaire de connexion</a></button>
        </div>

        <?php if (!empty($message)) : ?>
            <p class="success_message"><?= e($message); ?></p>
        <?php endif; ?>
    </div>
</div>