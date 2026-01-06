<!-- Banderole "liste de contact" -->
<div class="gestion-administrateur">
    <img src="assets/img/logo-admin.png" alt="icone logo admin">
    <h1>Liste des Contacts</h1>
    <div class="actions">
        <a href="/?page=parametres"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img width="44" height="46" src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin de banderole -->

<div class="container-contact">
    <div class="recherche-barre">
        <input type="text" id="recherche-contact" placeholder="Rechercher un contact...">
        <button type="button" id="recherche-button">Rechercher</button>
    </div>
    <ul class="contact-list">
        <?php
        $idUtilisateur = $_SESSION['id_utilisateur'];

        foreach ($utilisateurs as $utilisateur) :
            if ($utilisateur['id_utilisateur'] != $idUtilisateur) : ?>
                <li>
                    <div class="contact-details">
                        <strong><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong>
                        <span><?= htmlspecialchars($utilisateur['email']) ?></span>
                    </div>
                    <div class="contact-actions">
                        <!-- Lien vers le script de création ou d'ouverture de la conversation -->
                        <a href="/?page=conversation&id_destinataire=<?= $utilisateur['id_utilisateur'] ?>">Envoyer un message</a>
                    </div>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>