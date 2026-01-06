<!-- Banderole "gestion administrateur" -->
<div class="gestion-administrateur">
    <img src="assets/img/logo-admin.png" alt="icone logo admin">
    <h1>Supprimer un espace</h1>
    <div class="actions">
        <a href="/?page=administrateur"><img src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin de banderole -->

<div class="container-supprimer">
    <?php if ($message) : ?>
        <div class="message"> <?= e($message) ?></div>
    <?php endif; ?>
    <form method="post">
        <?= csrf_field() ?>
        <?php foreach ($espaces as $espace) : ?>
            <label class="checkbox">
                <input type="checkbox" name="espace[]" value="<?= e($espace['id_channel']) ?>">
                <p><?= e($espace['nom_du_channel']) ?></p>
            </label>
        <?php endforeach; ?>
        <input type="submit" name="submit_button" value="Supprimer les espaces sélectionnés" class="submit-button">
    </form>
</div>