<?php
/**
 * Gestionnaire d'erreurs sécurisé
 * Gère les erreurs et exceptions de manière sécurisée
 * Log les erreurs sans exposer d'informations sensibles
 */

// Charger les variables d'environnement
require_once __DIR__ . '/env-loader.php';
EnvLoader::load();

// Configuration
$appEnv = env('APP_ENV', 'production');
$appDebug = filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
$logDirectory = dirname(__DIR__, 2) . '/logs/';

// Créer le répertoire de logs s'il n'existe pas
if (!is_dir($logDirectory)) {
    mkdir($logDirectory, 0755, true);
}

// Fichiers de logs
$errorLogFile = $logDirectory . 'error-' . date('Y-m-d') . '.log';
$securityLogFile = $logDirectory . 'security-' . date('Y-m-d') . '.log';

/**
 * Gestionnaire d'erreurs personnalisé
 */
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    global $appDebug, $appEnv, $errorLogFile;

    // Ne pas traiter les erreurs supprimées avec @
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Déterminer le niveau d'erreur
    $errorType = 'ERREUR';
    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
            $errorType = 'ERREUR FATALE';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $errorType = 'AVERTISSEMENT';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $errorType = 'NOTICE';
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $errorType = 'DEPRECATED';
            break;
    }

    // Formatter le message de log
    $logMessage = sprintf(
        "[%s] [%s] %s dans %s ligne %d\n",
        date('Y-m-d H:i:s'),
        $errorType,
        $errstr,
        $errfile,
        $errline
    );

    // Logger l'erreur
    error_log($logMessage, 3, $errorLogFile);

    // En mode développement avec debug, afficher l'erreur
    if ($appDebug && $appEnv === 'development') {
        echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
        echo "<strong style='color: #f44336;'>[$errorType]</strong> ";
        echo "<strong>$errstr</strong><br>";
        echo "Fichier: <code>$errfile</code><br>";
        echo "Ligne: <strong>$errline</strong>";
        echo "</div>";
    }

    // En production, ne pas afficher les détails
    // L'erreur est déjà loggée

    // Ne pas exécuter le gestionnaire d'erreurs PHP interne
    return true;
}

/**
 * Gestionnaire d'exceptions personnalisé
 */
function customExceptionHandler($exception)
{
    global $appDebug, $appEnv, $errorLogFile;

    // Formatter le message de log
    $logMessage = sprintf(
        "[%s] [EXCEPTION] %s dans %s ligne %d\nTrace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    // Logger l'exception
    error_log($logMessage, 3, $errorLogFile);

    // En mode développement avec debug, afficher l'exception
    if ($appDebug && $appEnv === 'development') {
        echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px; border-radius: 4px;'>";
        echo "<strong style='color: #f44336;'>[EXCEPTION]</strong> ";
        echo "<strong>" . htmlspecialchars($exception->getMessage()) . "</strong><br>";
        echo "Fichier: <code>" . htmlspecialchars($exception->getFile()) . "</code><br>";
        echo "Ligne: <strong>" . $exception->getLine() . "</strong><br>";
        echo "<details><summary>Stack Trace</summary><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre></details>";
        echo "</div>";
    } else {
        // En production, afficher un message générique
        http_response_code(500);
        echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Erreur - MSN Connect</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .error-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .error-icon { font-size: 48px; color: #f44336; margin-bottom: 20px; }
        h1 { color: #333; margin: 0 0 10px; }
        p { color: #666; line-height: 1.6; }
        a { color: #2196F3; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class='error-container'>
        <div class='error-icon'>⚠️</div>
        <h1>Une erreur est survenue</h1>
        <p>Nous rencontrons actuellement des difficultés techniques. Veuillez réessayer ultérieurement.</p>
        <p><a href='/'>Retour à l'accueil</a></p>
    </div>
</body>
</html>";
    }
}

/**
 * Gestionnaire d'erreurs fatales
 */
function customShutdownHandler()
{
    $error = error_get_last();

    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        customErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

/**
 * Logger un événement de sécurité
 *
 * @param string $event Type d'événement (LOGIN_FAIL, CSRF_FAIL, etc.)
 * @param string $message Message descriptif
 * @param array $context Contexte additionnel
 */
function logSecurityEvent($event, $message, $context = [])
{
    global $securityLogFile;

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $userId = $_SESSION['id_utilisateur'] ?? 'GUEST';

    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'message' => $message,
        'ip' => $ip,
        'user_id' => $userId,
        'user_agent' => $userAgent,
        'context' => $context
    ];

    $logMessage = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    error_log($logMessage, 3, $securityLogFile);
}

/**
 * Logger une erreur applicative
 *
 * @param string $message Message d'erreur
 * @param array $context Contexte additionnel
 */
function logError($message, $context = [])
{
    global $errorLogFile;

    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context
    ];

    $logMessage = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    error_log($logMessage, 3, $errorLogFile);
}

// Enregistrer les gestionnaires
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');
register_shutdown_function('customShutdownHandler');

// Configuration de l'affichage des erreurs selon l'environnement
if ($appDebug && $appEnv === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);
}
