<?php
// Démarrer la session si aucune session n'est en cours
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté à partir de la session
$idUtilisateur = $_SESSION['id_utilisateur'];

// Préparer une requête pour récupérer les informations de l'utilisateur et son statut
$query = $dbh->prepare("SELECT * FROM utilisateur 
                        INNER JOIN statut_utilisateur ON utilisateur.id_statut_utilisateur = statut_utilisateur.id_statut_utilisateur 
                        WHERE id_utilisateur = :id_utilisateur");
// Exécuter la requête avec l'ID de l'utilisateur connecté
$query->execute(['id_utilisateur' => $idUtilisateur]);
// Récupérer les résultats de la requête
$utilisateur = $query->fetch();

// Récupérer les informations spécifiques de l'utilisateur ou utiliser une valeur par défaut
$prenomUtilisateur = $utilisateur['prenom'] ?? '';
$nomUtilisateur = $utilisateur['nom'] ?? '';
$avatarUtilisateur = $utilisateur['avatar'] ?? 'default-avatar.png'; // Utilise un avatar par défaut si aucun n'est défini
$estAdmin = $utilisateur['est_admin'] ?? 0; // Récupère le statut d'administrateur
$dateCreationUtilisateur = $utilisateur['date_de_creation'] ?? '';
$statutUtilisateur = ['nom' => $utilisateur['nom_statut'] ?? '', 'disponible' => $utilisateur['est_disponible'] ?? 0];

// Préparer une requête pour récupérer les discussions privées de l'utilisateur
$query = $dbh->prepare("SELECT channel.*, utilisateur.prenom AS prenom_destinataire, utilisateur.nom AS nom_destinataire, utilisateur.est_actif 
                        FROM channel 
                        INNER JOIN acces AS mon_acces ON channel.id_channel = mon_acces.id_channel 
                        INNER JOIN acces AS destinataire_acces ON mon_acces.id_channel = destinataire_acces.id_channel
                        INNER JOIN utilisateur ON destinataire_acces.id_utilisateur = utilisateur.id_utilisateur  
                        WHERE mon_acces.id_utilisateur = :id_utilisateur 
                        AND destinataire_acces.id_utilisateur != :id_utilisateur 
                        AND est_groupe = 0 
                        AND channel.est_actif = 1 
                        ORDER BY date_heure_dernier_message DESC 
                        LIMIT 5");
// Exécuter la requête pour récupérer les discussions privées de l'utilisateur
$query->execute(['id_utilisateur' => $idUtilisateur]);
$discussions = $query->fetchAll();

// Préparer une requête pour récupérer les groupes auxquels l'utilisateur appartient
$query = $dbh->prepare("SELECT channel.* FROM channel 
                        INNER JOIN acces ON channel.id_channel = acces.id_channel 
                        WHERE acces.id_utilisateur = :id_utilisateur 
                        AND est_groupe = 1 
                        AND est_actif = 1 
                        ORDER BY date_heure_dernier_message DESC 
                        LIMIT 5");
// Exécuter la requête pour récupérer les groupes
$query->execute(['id_utilisateur' => $idUtilisateur]);
$groupes = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Encodage des caractères et paramétrage pour les appareils mobiles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Liens vers les polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Sedan+SC&display=swap" rel="stylesheet">

    <!-- Liens vers les fichiers CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/mobile.css">

    <!-- Inclure un fichier CSS spécifique si le fichier existe -->
    <?php if (file_exists("assets/css/$page.css")) : ?>
        <link rel="stylesheet" href="assets/css/<?= ($page) ?>.css">
    <?php endif; ?>

    <!-- Scripts JS pour la page -->
    <script defer src="assets/js/main.js"></script>
    <?php if (file_exists("assets/js/$page.js")) : ?>
        <script defer src="assets/js/<?= ($page) ?>.js"></script>
    <?php endif; ?>

    <title>MSNConnect</title>
    <meta name="description" content="La renaissance de MSN.">
</head>

<body>
    <div class="container">
        <header>
            <nav>
                <div class="logo-container">
                    <!-- Logo du site -->
                    <img src="/assets/img/msn.png" alt="logo-msn">
                    <a href="/">
                        <h1>MSNConnect</h1>
                    </a>
                </div>

                <div class="menu-list">
                    <!-- Afficher l'avatar de l'utilisateur et son nom -->
                    <a href="/?page=parametres">
                        <img src="uploads/<?= ($avatarUtilisateur) ?>" alt="Avatar de l'utilisateur" class="user-avatar">
                    </a>

                    <a href="/?page=parametres">
                        <h4><?= ("$prenomUtilisateur $nomUtilisateur") ?></h4>
                    </a>

                    <!-- Afficher un lien vers la page admin si l'utilisateur est administrateur -->
                    <?php if ($estAdmin) : ?>
                        <a href="/?page=administrateur" class="btn"><img src="assets/img/reglages.png" alt="reglage-admin"></a>
                    <?php endif; ?>

                    <!-- Lien de déconnexion -->
                    <a href="scripts.php?script=deconnexion"><img src="/assets/img/se-deconnecter.png" alt="logo-deconnexion"></a>
                </div>
            </nav>
        </header>

        <main>
            <div class="sidebar-left">
                <div class="top-block">
                    <h4>Message privé</h4>

                    <!-- Bouton pour accéder à la liste de contacts -->
                    <div class="logo-personne">
                        <button class="recherche-personne">
                            <a href="/?page=liste-de-contact" class="btn">
                                <img src="assets/img/contacts.png" alt="">Liste des contacts</a>
                        </button>
                    </div>

                    <!-- Boucle pour afficher les discussions privées de l'utilisateur -->
                    <?php foreach ($discussions as $discussion) : ?>
                        <div class="message-prive-container">
                            <a href="/index.php?page=conversation&id_channel=<?= ($discussion['id_channel']) ?>" class="message-prive-link"><?= ($discussion['prenom_destinataire'] . ' ' . $discussion['nom_destinataire']) ?></a>
                        </div>
                    <?php endforeach; ?>

                </div>
                <div class="bottom-block">
                    <h4>Espaces groupe</h4>

                    <!-- Bouton pour accéder à la liste des groupes -->
                    <div class="logo-personne">
                        <button class="recherche-personne">
                            <a href="/?page=liste-de-groupe" class="btn">
                                <img src="assets/img/liste-de-groupe.png" alt="">Liste des groupes</a>
                        </button>
                    </div>

                    <!-- Boucle pour afficher les groupes auxquels l'utilisateur appartient -->
                    <?php foreach ($groupes as $groupe) : ?>
                        <div class="message-groupe-container">
                            <a href="/index.php?page=conversation&id_channel=<?= ($groupe['id_channel']) ?>" class="message-groupe-link"><?= ($groupe['nom_du_channel'] ?? '') ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <!-- Inclure le contenu spécifique de la page -->
                <?php require "../templates/$page.html.php"; ?>
            </div>
        </main>
    </div>
</body>

</html>