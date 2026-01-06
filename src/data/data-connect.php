<?php
/**
 * Fichier de connexion à la base de données
 * Utilise les variables d'environnement pour la sécurité
 */

// Charger les variables d'environnement
require_once __DIR__ . '/../helpers/env-loader.php';
EnvLoader::load();

// Récupérer les informations de connexion depuis les variables d'environnement
$host = env('DB_HOST', 'localhost');
$user = env('DB_USER', 'root');
$password = env('DB_PASSWORD', '');
$dbName = env('DB_NAME', 'msn-connect');
$appEnv = env('APP_ENV', 'production');
$appDebug = filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);

try {
    // Configuration PDO avec options de sécurité renforcées
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lever des exceptions en cas d'erreur
        PDO::ATTR_EMULATE_PREPARES => false, // Désactiver l'émulation des requêtes préparées
        PDO::ATTR_PERSISTENT => false, // Pas de connexions persistantes
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    // Connexion à la base de données avec PDO
    $dbh = new PDO(
        "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
        $user,
        $password,
        $options
    );
} catch (PDOException $e) {
    // Gestion sécurisée des erreurs
    // Ne jamais afficher les détails de l'erreur en production
    if ($appDebug && $appEnv === 'development') {
        error_log("Erreur de connexion à la base de données : " . $e->getMessage());
        die("Erreur de connexion à la base de données. Consultez les logs pour plus de détails.");
    } else {
        // En production, logger l'erreur mais afficher un message générique
        error_log("Erreur de connexion BD : " . $e->getMessage());
        die("Une erreur technique est survenue. Veuillez réessayer ultérieurement.");
    }
}
