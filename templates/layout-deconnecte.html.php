<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Sedan+SC&display=swap" rel="stylesheet">

    <!-- Importation de Feather Icons pour utiliser des icônes SVG -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Inclusion des fichiers CSS principaux pour styliser la page -->
    <link rel="stylesheet" href="assets/css/main.css?v=<?= filemtime('assets/css/main.css') ?>">
    <link rel="stylesheet" href="assets/css/mobile.css?v=<?= filemtime('assets/css/mobile.css') ?>"><?php // Cache buster basé sur la date de modification ?>

    <!-- Condition pour inclure un fichier CSS spécifique à la page si celui-ci existe -->
    <?php if (file_exists("assets/css/$page.css")) : ?>
        <link rel="stylesheet" href="assets/css/<?= e($page) ?>.css?v=<?= filemtime("assets/css/$page.css") ?>">
    <?php endif; ?>

    <!-- Inclusion des fichiers JavaScript principaux -->
    <script defer src="assets/js/main.js?v=<?= filemtime('assets/js/main.js') ?>"></script>
    <!-- Condition pour inclure un fichier JS spécifique à la page si celui-ci existe -->
    <?php if (file_exists("assets/js/$page.js")) : ?>
        <script defer src="assets/js/<?= e($page) ?>.js?v=<?= filemtime("assets/js/$page.js") ?>"></script>
    <?php endif; ?>

    <!-- Titre de la page affiché dans l'onglet du navigateur -->
    <title>MSNConnect</title>
    <!-- Meta description pour les moteurs de recherche -->
    <meta name="description" content="La renaissance de MSN.">
</head>

<body>
    <!-- Inclusion dynamique du contenu spécifique de la page -->
    <?php require  "../templates/$page.html.php"; ?>
</body>

</html>