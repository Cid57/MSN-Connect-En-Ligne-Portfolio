#!/usr/bin/env php
<?php
/**
 * Script de vérification de sécurité
 * Identifie les fichiers nécessitant une migration de sécurité
 *
 * Usage: php check-security.php
 */

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     MSN CONNECT - Vérification de Sécurité                    ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$issues = [];
$warnings = [];
$success = [];

// 1. Vérifier que .env existe
echo "🔍 Vérification du fichier .env...\n";
if (!file_exists(__DIR__ . '/.env')) {
    $issues[] = "❌ Fichier .env manquant (copier depuis .env.example)";
} else {
    $success[] = "✅ Fichier .env présent";
}

// 2. Vérifier que .gitignore existe
echo "🔍 Vérification du .gitignore...\n";
if (!file_exists(__DIR__ . '/.gitignore')) {
    $issues[] = "❌ Fichier .gitignore manquant";
} else {
    $success[] = "✅ Fichier .gitignore présent";
}

// 3. Vérifier les dossiers nécessaires
echo "🔍 Vérification des dossiers...\n";
$requiredDirs = [
    'logs' => __DIR__ . '/logs',
    'uploads' => __DIR__ . '/public/assets/uploads',
    'helpers' => __DIR__ . '/src/helpers',
];

foreach ($requiredDirs as $name => $path) {
    if (!is_dir($path)) {
        $warnings[] = "⚠️  Dossier $name manquant (sera créé automatiquement)";
        @mkdir($path, 0755, true);
    } else {
        $success[] = "✅ Dossier $name présent";
    }
}

// 4. Vérifier les fichiers de sécurité
echo "🔍 Vérification des fichiers de sécurité...\n";
$securityFiles = [
    'src/bootstrap.php',
    'src/helpers/env-loader.php',
    'src/helpers/csrf-protection.php',
    'src/helpers/security-init.php',
    'src/helpers/secure-upload.php',
    'src/helpers/error-handler.php',
];

foreach ($securityFiles as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        $issues[] = "❌ Fichier de sécurité manquant: $file";
    } else {
        $success[] = "✅ $file présent";
    }
}

// 5. Rechercher les formulaires sans CSRF
echo "\n🔍 Recherche des formulaires sans protection CSRF...\n";
$templates = glob(__DIR__ . '/templates/*.php');
$formsWithoutCsrf = [];

foreach ($templates as $template) {
    $content = file_get_contents($template);
    // Vérifier si le fichier contient <form et POST mais pas csrf_field
    if (preg_match('/<form[^>]*method\s*=\s*["\']post["\']/i', $content)) {
        if (!preg_match('/csrf_field|csrf_token/', $content)) {
            $formsWithoutCsrf[] = basename($template);
        }
    }
}

if (!empty($formsWithoutCsrf)) {
    foreach ($formsWithoutCsrf as $file) {
        $warnings[] = "⚠️  Formulaire sans CSRF: templates/$file";
    }
} else {
    $success[] = "✅ Tous les formulaires vérifiés ont un token CSRF";
}

// 6. Rechercher les sorties non échappées
echo "🔍 Recherche des sorties potentiellement non échappées (XSS)...\n";
$xssFiles = [];

foreach ($templates as $template) {
    $content = file_get_contents($template);
    // Rechercher <?= $ sans e()
    if (preg_match('/\<\?=\s*\$[a-zA-Z_]/i', $content)) {
        // Vérifier si ce n'est pas déjà échappé
        $lines = explode("\n", $content);
        $lineNum = 0;
        foreach ($lines as $line) {
            $lineNum++;
            if (preg_match('/\<\?=\s*\$/', $line) && !preg_match('/\<\?=\s*e\(/', $line)) {
                $xssFiles[] = basename($template) . ":$lineNum";
                break; // Une occurrence suffit par fichier
            }
        }
    }
}

if (!empty($xssFiles)) {
    foreach ($xssFiles as $file) {
        $warnings[] = "⚠️  Sortie potentiellement non échappée: templates/$file";
    }
} else {
    $success[] = "✅ Aucune sortie non échappée détectée";
}

// 7. Vérifier les pages de traitement POST
echo "🔍 Vérification des pages de traitement POST...\n";
$pages = glob(__DIR__ . '/src/pages/*.php');
$pagesWithoutCsrfValidation = [];

foreach ($pages as $page) {
    $content = file_get_contents($page);
    // Vérifier si la page traite des POST
    if (preg_match('/\$_POST/i', $content)) {
        // Vérifier si elle valide le CSRF
        if (!preg_match('/csrf_validate|csrf_field|requireValidToken/', $content)) {
            $pagesWithoutCsrfValidation[] = basename($page);
        }
    }
}

if (!empty($pagesWithoutCsrfValidation)) {
    foreach ($pagesWithoutCsrfValidation as $file) {
        $warnings[] = "⚠️  Page POST sans validation CSRF: src/pages/$file";
    }
} else {
    $success[] = "✅ Toutes les pages POST valident le CSRF";
}

// 8. Vérifier la configuration Apache
echo "🔍 Vérification de la configuration Apache...\n";
if (!file_exists(__DIR__ . '/.htaccess')) {
    $warnings[] = "⚠️  Fichier .htaccess manquant (recommandé pour Apache)";
} else {
    $success[] = "✅ Fichier .htaccess présent";
}

// Affichage des résultats
echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "                    RÉSULTATS DE L'ANALYSE                      \n";
echo "════════════════════════════════════════════════════════════════\n\n";

if (!empty($issues)) {
    echo "🚨 PROBLÈMES CRITIQUES (" . count($issues) . "):\n";
    foreach ($issues as $issue) {
        echo "   $issue\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   $warning\n";
    }
    echo "\n";
}

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . "):\n";
    foreach ($success as $s) {
        echo "   $s\n";
    }
    echo "\n";
}

// Score de sécurité
$totalChecks = count($issues) + count($warnings) + count($success);
$securityScore = round((count($success) / $totalChecks) * 100);

echo "════════════════════════════════════════════════════════════════\n";
echo "              SCORE DE SÉCURITÉ: $securityScore%                     \n";
echo "════════════════════════════════════════════════════════════════\n\n";

if ($securityScore >= 90) {
    echo "🎉 Excellent ! Votre application est bien sécurisée.\n";
} elseif ($securityScore >= 70) {
    echo "👍 Bon travail ! Il reste quelques améliorations à faire.\n";
} elseif ($securityScore >= 50) {
    echo "⚠️  Attention ! Plusieurs problèmes de sécurité nécessitent votre attention.\n";
} else {
    echo "🚨 URGENT ! Votre application présente des vulnérabilités critiques.\n";
}

echo "\n📖 Consultez MIGRATION-SECURITE.md pour les instructions de correction.\n\n";

// Code de sortie
exit(count($issues) > 0 ? 1 : 0);
