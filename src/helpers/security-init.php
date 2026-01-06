<?php
/**
 * Initialisation des fonctionnalités de sécurité
 * À inclure au début de chaque page
 */

// Charger la protection CSRF
require_once __DIR__ . '/csrf-protection.php';

// Démarrer la session de manière sécurisée
if (session_status() === PHP_SESSION_NONE) {
    // Configuration sécurisée de la session
    ini_set('session.cookie_httponly', 1); // Empêcher l'accès JavaScript aux cookies de session
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0); // HTTPS uniquement si disponible
    ini_set('session.use_strict_mode', 1); // Refuser les ID de session non initialisés
    ini_set('session.cookie_samesite', 'Strict'); // Protection CSRF au niveau cookie

    session_start();

    // Régénérer l'ID de session périodiquement (toutes les 30 minutes)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Fonction pour échapper les sorties HTML de manière sécurisée
function e($string, $encoding = 'UTF-8')
{
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, $encoding);
}

// Fonction pour vérifier si l'utilisateur est authentifié
function isAuthenticated()
{
    return isset($_SESSION['utilisateur']);
}

// Fonction pour vérifier si l'utilisateur est administrateur
function isAdmin()
{
    return isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['est_admin'] == 1;
}

// Fonction pour exiger une authentification
function requireAuth($redirectUrl = '/public/index.php?page=connexion')
{
    if (!isAuthenticated()) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Fonction pour exiger des droits administrateur
function requireAdmin($redirectUrl = '/public/index.php')
{
    if (!isAdmin()) {
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Protection contre le clickjacking
header('X-Frame-Options: DENY');

// Protection XSS
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Politique de sécurité du contenu (CSP) - À adapter selon vos besoins
$csp = "default-src 'self'; ";
$csp .= "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; ";
$csp .= "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; ";
$csp .= "font-src 'self' https://fonts.gstatic.com; ";
$csp .= "img-src 'self' data: https:; ";
$csp .= "connect-src 'self'; ";
header("Content-Security-Policy: $csp");

// Politique de référent
header('Referrer-Policy: strict-origin-when-cross-origin');

// Protection contre les attaques de type MIME
header('X-Content-Type-Options: nosniff');
