<?php
/**
 * Fichier de bootstrap de l'application
 * Initialise tous les composants de sécurité et de base
 *
 * À inclure en premier dans TOUTES les pages PHP de l'application
 */

// Empêcher l'inclusion multiple
if (defined('APP_BOOTSTRAPPED')) {
    return;
}
define('APP_BOOTSTRAPPED', true);

// Définir le chemin racine de l'application
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// 1. Charger le gestionnaire d'erreurs (en premier pour capturer toutes les erreurs)
require_once SRC_PATH . '/helpers/error-handler.php';

// 2. Charger les variables d'environnement
require_once SRC_PATH . '/helpers/env-loader.php';
EnvLoader::load();

// 3. Initialiser la sécurité (session, headers, CSRF, etc.)
require_once SRC_PATH . '/helpers/security-init.php';

// 4. Charger les helpers utilitaires
require_once SRC_PATH . '/helpers/secure-upload.php';

// 5. Connexion à la base de données
require_once SRC_PATH . '/data/data-connect.php';

// L'application est maintenant prête à fonctionner de manière sécurisée
