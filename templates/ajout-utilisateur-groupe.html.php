<!-- Banderole "ajout utilisateur groupe" -->
<div class="ajout-utilisateur-groupe">
    <img src="assets\img\ajout-user-groupe.webp" alt="Icone ajouter un utilisateur" class="user-icon">
    <h1>Ajouter un utilisateur au groupe</h1>
    <div class="actions">
        <a href="/?page=admin-modifier-espace"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin banderole -->

<div class="form-container">
    <?php if ($message) : ?>
        <div class="message"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="recherche-barre">
        <input type="text" id="recherche-utilisateur" placeholder="Rechercher un utilisateur...">
        <button type="button" id="recherche-button">Rechercher</button>
    </div>

    <form method="post" class="form-group">
        <?= csrf_field() ?>
        <div class="user-list-container">
            <?php foreach ($utilisateurs as $utilisateur) : ?>
                <label class="user-checkbox">
                    <input type="checkbox" name="utilisateur[]" value="<?= e($utilisateur['id_utilisateur']) ?>">
                    <p><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) . ' (' . e($utilisateur['email']) . ')' ?></p>
                </label>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="id_channel" value="<?= e($id_channel) ?>">
        <div class="sticky-button-container">
            <input type="submit" name="submit_button" value="Ajouter les utilisateurs sélectionnés" class="submit-button">
        </div>
    </form>
</div>