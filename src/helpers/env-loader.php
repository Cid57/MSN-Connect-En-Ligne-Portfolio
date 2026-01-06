<?php
/**
 * Chargeur de variables d'environnement
 * Charge les variables depuis le fichier .env
 */

class EnvLoader
{
    private static $loaded = false;
    private static $env = [];

    /**
     * Charge les variables d'environnement depuis le fichier .env
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(__DIR__, 2) . '/.env';
        }

        if (!file_exists($path)) {
            throw new Exception("Fichier .env introuvable : $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parser la ligne KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Enlever les guillemets si présents
                $value = trim($value, '"\'');

                // Stocker dans $_ENV, $_SERVER et notre tableau statique
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv("$key=$value");
                self::$env[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Récupère une variable d'environnement
     *
     * @param string $key La clé de la variable
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        // Vérifier dans l'ordre : ENV statique, $_ENV, $_SERVER, getenv()
        if (isset(self::$env[$key])) {
            return self::$env[$key];
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Récupère une variable d'environnement (alias)
     */
    public static function env($key, $default = null)
    {
        return self::get($key, $default);
    }
}

/**
 * Fonction helper pour récupérer une variable d'environnement
 *
 * @param string $key La clé de la variable
 * @param mixed $default Valeur par défaut
 * @return mixed
 */
function env($key, $default = null)
{
    return EnvLoader::env($key, $default);
}
