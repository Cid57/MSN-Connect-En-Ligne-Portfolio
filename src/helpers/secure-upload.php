<?php
/**
 * Gestionnaire sécurisé d'upload de fichiers
 * Valide et sécurise les uploads de fichiers (avatars, images, etc.)
 */

class SecureUpload
{
    // Configuration par défaut
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5242880; // 5 MB en octets
    private $uploadDirectory = '';
    private $error = '';

    /**
     * Constructeur
     *
     * @param string $uploadDirectory Répertoire de destination
     */
    public function __construct($uploadDirectory = null)
    {
        if ($uploadDirectory === null) {
            $uploadDirectory = dirname(__DIR__, 2) . '/public/assets/uploads/';
        }

        $this->uploadDirectory = rtrim($uploadDirectory, '/') . '/';

        // Créer le répertoire s'il n'existe pas
        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }
    }

    /**
     * Configure les types MIME autorisés
     *
     * @param array $mimeTypes
     * @return self
     */
    public function setAllowedMimeTypes(array $mimeTypes)
    {
        $this->allowedMimeTypes = $mimeTypes;
        return $this;
    }

    /**
     * Configure les extensions autorisées
     *
     * @param array $extensions
     * @return self
     */
    public function setAllowedExtensions(array $extensions)
    {
        $this->allowedExtensions = $extensions;
        return $this;
    }

    /**
     * Configure la taille maximale du fichier
     *
     * @param int $bytes Taille en octets
     * @return self
     */
    public function setMaxFileSize($bytes)
    {
        $this->maxFileSize = (int)$bytes;
        return $this;
    }

    /**
     * Configure le répertoire d'upload
     *
     * @param string $directory
     * @return self
     */
    public function setUploadDirectory($directory)
    {
        $this->uploadDirectory = rtrim($directory, '/') . '/';

        if (!is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0755, true);
        }

        return $this;
    }

    /**
     * Récupère le dernier message d'erreur
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Valide un fichier uploadé
     *
     * @param array $file Tableau $_FILES['fieldname']
     * @return bool
     */
    private function validate($file)
    {
        // Vérifier qu'il n'y a pas d'erreur d'upload
        if (!isset($file['error']) || is_array($file['error'])) {
            $this->error = "Paramètres invalides";
            return false;
        }

        // Vérifier le code d'erreur
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->error = "Aucun fichier envoyé";
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->error = "Le fichier dépasse la taille maximale autorisée";
                return false;
            default:
                $this->error = "Erreur inconnue lors de l'upload";
                return false;
        }

        // Vérifier la taille du fichier
        if ($file['size'] > $this->maxFileSize) {
            $this->error = "Le fichier est trop volumineux (max: " . $this->formatBytes($this->maxFileSize) . ")";
            return false;
        }

        // Vérifier que le fichier a été uploadé via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = "Fichier non uploadé via HTTP POST";
            return false;
        }

        // Vérifier le type MIME réel du fichier (pas celui déclaré par le navigateur)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $this->error = "Type de fichier non autorisé (types acceptés: " . implode(', ', $this->allowedMimeTypes) . ")";
            return false;
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            $this->error = "Extension de fichier non autorisée (extensions acceptées: " . implode(', ', $this->allowedExtensions) . ")";
            return false;
        }

        // Pour les images, vérifier qu'il s'agit bien d'une image valide
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                $this->error = "Le fichier n'est pas une image valide";
                return false;
            }
        }

        return true;
    }

    /**
     * Génère un nom de fichier sécurisé et unique
     *
     * @param string $originalName Nom original du fichier
     * @return string Nouveau nom de fichier
     */
    private function generateSecureFilename($originalName)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Générer un nom aléatoire sécurisé
        $randomName = bin2hex(random_bytes(16));

        // Ajouter un timestamp pour garantir l'unicité
        $timestamp = time();

        return $randomName . '_' . $timestamp . '.' . $extension;
    }

    /**
     * Upload un fichier de manière sécurisée
     *
     * @param array $file Tableau $_FILES['fieldname']
     * @param string $customFilename Nom de fichier personnalisé (optionnel)
     * @return string|false Nom du fichier uploadé ou false en cas d'erreur
     */
    public function upload($file, $customFilename = null)
    {
        // Réinitialiser l'erreur
        $this->error = '';

        // Valider le fichier
        if (!$this->validate($file)) {
            return false;
        }

        // Générer le nom de fichier
        if ($customFilename !== null) {
            // Sécuriser le nom personnalisé
            $filename = $this->sanitizeFilename($customFilename);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $filename . '.' . $extension;
        } else {
            $filename = $this->generateSecureFilename($file['name']);
        }

        // Chemin complet de destination
        $destination = $this->uploadDirectory . $filename;

        // Vérifier que le fichier n'existe pas déjà
        if (file_exists($destination)) {
            $filename = $this->generateSecureFilename($file['name']);
            $destination = $this->uploadDirectory . $filename;
        }

        // Déplacer le fichier uploadé
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->error = "Impossible de déplacer le fichier uploadé";
            return false;
        }

        // Définir les permissions appropriées
        chmod($destination, 0644);

        return $filename;
    }

    /**
     * Supprime un fichier uploadé
     *
     * @param string $filename Nom du fichier à supprimer
     * @return bool
     */
    public function delete($filename)
    {
        // Sécuriser le nom de fichier (empêcher directory traversal)
        $filename = basename($filename);
        $filepath = $this->uploadDirectory . $filename;

        // Vérifier que le fichier existe
        if (!file_exists($filepath)) {
            $this->error = "Le fichier n'existe pas";
            return false;
        }

        // Vérifier qu'il s'agit bien d'un fichier (pas un répertoire)
        if (!is_file($filepath)) {
            $this->error = "Ceci n'est pas un fichier";
            return false;
        }

        // Supprimer le fichier
        if (!unlink($filepath)) {
            $this->error = "Impossible de supprimer le fichier";
            return false;
        }

        return true;
    }

    /**
     * Nettoie un nom de fichier
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename($filename)
    {
        // Retirer l'extension
        $filename = pathinfo($filename, PATHINFO_FILENAME);

        // Remplacer les caractères spéciaux
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // Limiter la longueur
        $filename = substr($filename, 0, 50);

        return $filename;
    }

    /**
     * Formate une taille en octets en format lisible
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

/**
 * Fonction helper pour uploader un avatar
 *
 * @param array $file $_FILES['avatar']
 * @return string|false Nom du fichier ou false
 */
function uploadAvatar($file)
{
    $uploader = new SecureUpload();
    $uploader->setMaxFileSize(2097152); // 2 MB pour les avatars

    return $uploader->upload($file);
}
