<!-- Banderole "gestion administrateur" -->
<div class="gestion-administrateur">
    <img src="assets/img/discussion-de-groupe.png" alt="Icone réactiver un groupe" class="group-icon">
    <h1>Réactiver un groupe</h1>
    <div class="actions">
        <a href="/?page=admin-ajouter-espace"><img src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin banderole -->

<div class="container-reactiver">
    <?php if ($message) : ?>
        <div class="message"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" class="table-container">
        <?= csrf_field() ?>
        <?php foreach ($espaces as $espace) : ?>
            <label class="group-checkbox">
                <input type="checkbox" name="espace[]" value="<?= e($espace['id_channel']) ?>">
                <p><?= !empty($espace['nom_du_channel']) ? e($espace['nom_du_channel']) : 'Aucun nom' ?></p>
            </label>
        <?php endforeach; ?>
        <div class="sticky-button-container">
            <input type="submit" name="submit_button" value="Réactiver les groupes sélectionnés" class="submit-button">
        </div>
    </form>
</div>