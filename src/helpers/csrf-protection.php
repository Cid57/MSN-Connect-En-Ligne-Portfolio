<?php
/**
 * Protection CSRF (Cross-Site Request Forgery)
 * Génère et valide des tokens CSRF pour sécuriser les formulaires
 */

class CsrfProtection
{
    private static $tokenName = 'csrf_token';
    private static $tokenLifetime = 3600; // 1 heure en secondes

    /**
     * Initialise la session si nécessaire
     */
    private static function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Génère un nouveau token CSRF
     *
     * @return string Le token généré
     */
    public static function generateToken()
    {
        self::initSession();

        // Générer un token aléatoire sécurisé
        $token = bin2hex(random_bytes(32));

        // Stocker le token et son timestamp dans la session
        $_SESSION[self::$tokenName] = $token;
        $_SESSION[self::$tokenName . '_time'] = time();

        return $token;
    }

    /**
     * Récupère le token CSRF actuel ou en génère un nouveau
     *
     * @return string Le token CSRF
     */
    public static function getToken()
    {
        self::initSession();

        // Si le token n'existe pas ou a expiré, en générer un nouveau
        if (!isset($_SESSION[self::$tokenName]) || self::isTokenExpired()) {
            return self::generateToken();
        }

        return $_SESSION[self::$tokenName];
    }

    /**
     * Vérifie si le token a expiré
     *
     * @return bool
     */
    private static function isTokenExpired()
    {
        if (!isset($_SESSION[self::$tokenName . '_time'])) {
            return true;
        }

        $tokenAge = time() - $_SESSION[self::$tokenName . '_time'];
        return $tokenAge > self::$tokenLifetime;
    }

    /**
     * Valide un token CSRF
     *
     * @param string $token Le token à valider
     * @return bool True si valide, False sinon
     */
    public static function validateToken($token)
    {
        self::initSession();

        // Vérifier que le token existe en session
        if (!isset($_SESSION[self::$tokenName])) {
            return false;
        }

        // Vérifier que le token n'a pas expiré
        if (self::isTokenExpired()) {
            self::destroyToken();
            return false;
        }

        // Comparer les tokens de manière sécurisée (timing attack safe)
        return hash_equals($_SESSION[self::$tokenName], $token);
    }

    /**
     * Valide le token CSRF depuis la requête POST
     *
     * @param string $fieldName Nom du champ contenant le token (par défaut: csrf_token)
     * @return bool True si valide, False sinon
     */
    public static function validateRequest($fieldName = null)
    {
        if ($fieldName === null) {
            $fieldName = self::$tokenName;
        }

        // Vérifier si le token est présent dans POST
        if (!isset($_POST[$fieldName])) {
            return false;
        }

        return self::validateToken($_POST[$fieldName]);
    }

    /**
     * Détruit le token CSRF actuel
     */
    public static function destroyToken()
    {
        self::initSession();

        unset($_SESSION[self::$tokenName]);
        unset($_SESSION[self::$tokenName . '_time']);
    }

    /**
     * Génère le HTML d'un champ input hidden contenant le token CSRF
     *
     * @param string $fieldName Nom du champ (par défaut: csrf_token)
     * @return string Le HTML du champ input
     */
    public static function getHiddenInput($fieldName = null)
    {
        if ($fieldName === null) {
            $fieldName = self::$tokenName;
        }

        $token = self::getToken();
        return '<input type="hidden" name="' . htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Vérifie la validité du token et arrête l'exécution si invalide
     *
     * @param string $fieldName Nom du champ (par défaut: csrf_token)
     * @param string $errorMessage Message d'erreur personnalisé
     */
    public static function requireValidToken($fieldName = null, $errorMessage = null)
    {
        if (!self::validateRequest($fieldName)) {
            if ($errorMessage === null) {
                $errorMessage = "Token de sécurité invalide ou expiré. Veuillez réessayer.";
            }

            http_response_code(403);
            die($errorMessage);
        }
    }

    /**
     * Configure la durée de vie du token
     *
     * @param int $seconds Durée en secondes
     */
    public static function setTokenLifetime($seconds)
    {
        self::$tokenLifetime = (int)$seconds;
    }
}

/**
 * Fonctions helper pour faciliter l'utilisation
 */

/**
 * Génère un champ input hidden avec le token CSRF
 *
 * @return string
 */
function csrf_field()
{
    return CsrfProtection::getHiddenInput();
}

/**
 * Récupère le token CSRF actuel
 *
 * @return string
 */
function csrf_token()
{
    return CsrfProtection::getToken();
}

/**
 * Valide le token CSRF de la requête
 *
 * @return bool
 */
function csrf_validate()
{
    return CsrfProtection::validateRequest();
}
