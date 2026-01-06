<!-- Banderole "gestion administrateur" -->
<div class="gestion-administrateur">
    <img src="assets/img/supprimer-user.png" alt="Icone supprimer un utilisateur" class="user-icon">
    <h1>Désactiver un utilisateur</h1>
    <div class="actions">
        <a href="/?page=administrateur"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin banderole -->

<div class="container-supprimer">
    <?php if ($message) : ?>
        <div class="message"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="recherche-barre">
        <input type="text" id="recherche-utilisateur" placeholder="Rechercher un utilisateur...">
        <button type="button" id="recherche-button">Rechercher</button>
    </div>

    <form method="post" class="table-container">
        <?= csrf_field() ?>
        <?php foreach ($utilisateurs as $utilisateur) : ?>
            <label class="user-checkbox">
                <input type="checkbox" name="utilisateur[]" value="<?= e($utilisateur['id_utilisateur']) ?>">
                <p><?= e($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?> (<?= e($utilisateur['email']) ?>)</p>
            </label>
        <?php endforeach; ?>
        <div class="sticky-button-container">
            <input type="submit" name="submit_button" value="Désactiver les utilisateurs sélectionnés" class="submit-button">
        </div>
    </form>
</div>