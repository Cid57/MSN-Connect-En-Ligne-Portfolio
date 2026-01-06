<!-- Banderole "gestion administrateur" -->
<div class="gestion-administrateur">
    <img src="assets/img/modifier-user.png" alt="Modifier utilisateur" class="user-icon">
    <h1>Modifier un utilisateur</h1>
    <div>
        <a href="/?page=administrateur"><img src="https://img.icons8.com/3d-fluency/94/left.png" alt="left"></a>
        <a href="/"><img src="https://img.icons8.com/3d-fluency/94/delete-sign.png" alt="delete-sign"></a>
    </div>
</div>
<!-- Fin de banderole -->

<div class="container-modifier">
    <form action="/?page=admin-modifier-utilisateur&id=<?= $idUtilisateurModifier ?>" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Adresse mail</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($utilisateurModifier['prenom']) ?>" placeholder="Prénom">
                    </td>
                    <td>
                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($utilisateurModifier['nom']) ?>" placeholder="Nom">
                    </td>
                    <td>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($utilisateurModifier['email']) ?>" placeholder="Adresse mail">
                    </td>
                    <td>
                        <button type="submit" class="submit-button">Valider</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>