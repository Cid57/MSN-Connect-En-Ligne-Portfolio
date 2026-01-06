<!-- Banniere Gestion administrateur -->
<div class="gestion-administrateur">
    <img src="assets/img/discussion-de-groupe.png" alt="icone logo admin">
    <h1>Gestion administrateur</h1>
    <div class="actions">
        <a href="/?page=administrateur"><img src="https://img.icons8.com/3d-fluency/94/left.png" alt="left" /></a>
        <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign" /></a>
    </div>
</div>
<!-- --------------------------------------- -->

<div class="container-ajout-groupe">
    <div class="header">
        <img src="assets/img/bulle-bleu.png" alt="Icone ajouter espace">
        <h2>Ajouter un groupe</h2>
    </div>

    <!-- Formulaire -->
    <form method="post">
        <?= csrf_field() ?>
        <div>
            <label for="ajout-groupe">Nom de l'espace</label><br>
            <input type="text" id="ajout-groupe" name="ajout_groupe" placeholder="Entrez le nom du groupe">
            <!-- Affiche les message d'erreurs sous l'input de l'espace -->
            <?php if (!empty($errors['groupe'])) : ?>
                <p><?= e($errors['groupe']) ?></p>
            <?php endif; ?>
        </div>
        <br>
        <input type="submit" id="espaceBouton" name="groupe_bouton" value="Ajouter le groupe">
        <!-- Bouton réactiver un groupe -->
        <div class="reactiver-groupe">
            <a href="/?page=reactiver-espace" class="btn-reactiver">Réactiver un groupe</a>
        </div>
    </form>
    <!-- Fin du formulaire -->
</div>