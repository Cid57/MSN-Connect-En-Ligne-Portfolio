# Guide de Sécurité - MSN Connect

Ce document explique comment utiliser les fonctionnalités de sécurité implémentées dans l'application.

## Table des matières
1. [Variables d'environnement](#variables-denvironnement)
2. [Protection CSRF](#protection-csrf)
3. [Headers de sécurité](#headers-de-sécurité)
4. [Bonnes pratiques](#bonnes-pratiques)

---

## Variables d'environnement

### Configuration

Les informations sensibles (identifiants BD, clés API, etc.) sont stockées dans le fichier `.env` à la racine du projet.

**IMPORTANT:** Le fichier `.env` ne doit JAMAIS être versionné dans Git. Utilisez `.env.example` comme modèle.

### Utilisation

```php
// Charger les variables d'environnement
require_once __DIR__ . '/src/helpers/env-loader.php';
EnvLoader::load();

// Récupérer une variable
$dbHost = env('DB_HOST', 'localhost'); // localhost = valeur par défaut
```

### Variables disponibles

- `DB_HOST`: Hôte de la base de données
- `DB_NAME`: Nom de la base de données
- `DB_USER`: Utilisateur de la base de données
- `DB_PASSWORD`: Mot de passe de la base de données
- `APP_ENV`: Environnement (development/production)
- `APP_DEBUG`: Mode debug (true/false)
- `MAIL_*`: Configuration email (PHPMailer)

---

## Protection CSRF

### Qu'est-ce que le CSRF ?

Le CSRF (Cross-Site Request Forgery) est une attaque où un site malveillant force un utilisateur authentifié à exécuter des actions non désirées sur votre application.

### Utilisation dans les formulaires

**Méthode 1 : Fonction helper (recommandée)**

```php
<?php require_once __DIR__ . '/../src/helpers/csrf-protection.php'; ?>

<form method="POST" action="traitement.php">
    <?= csrf_field() ?>

    <input type="text" name="nom">
    <button type="submit">Envoyer</button>
</form>
```

**Méthode 2 : Classe CsrfProtection**

```php
<?php
require_once __DIR__ . '/../src/helpers/csrf-protection.php';
echo CsrfProtection::getHiddenInput();
?>
```

### Validation du token

**Dans le fichier de traitement :**

```php
<?php
require_once __DIR__ . '/../src/helpers/csrf-protection.php';

// Méthode 1 : Validation simple
if (!csrf_validate()) {
    die("Token CSRF invalide");
}

// Méthode 2 : Validation avec arrêt automatique
CsrfProtection::requireValidToken();

// Le code continue uniquement si le token est valide
// ... traitement du formulaire ...
?>
```

### Exemple complet

**Fichier : formulaire-exemple.php**
```php
<?php
require_once __DIR__ . '/../src/helpers/security-init.php';
requireAuth(); // Nécessite une authentification
?>
<!DOCTYPE html>
<html>
<body>
    <form method="POST" action="traitement-exemple.php">
        <?= csrf_field() ?>

        <label>Message :</label>
        <textarea name="message"></textarea>

        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
```

**Fichier : traitement-exemple.php**
```php
<?php
require_once __DIR__ . '/../src/helpers/security-init.php';
requireAuth();

// Valider le token CSRF
CsrfProtection::requireValidToken();

// Traiter les données
$message = $_POST['message'] ?? '';

// TOUJOURS échapper les sorties
echo "Message reçu : " . e($message);
?>
```

---

## Headers de sécurité

Les headers de sécurité HTTP sont automatiquement définis via `security-init.php`.

### Headers implémentés

| Header | Valeur | Description |
|--------|--------|-------------|
| `X-Frame-Options` | DENY | Empêche l'inclusion de la page dans une iframe (protection clickjacking) |
| `X-Content-Type-Options` | nosniff | Empêche le navigateur de deviner le type MIME |
| `X-XSS-Protection` | 1; mode=block | Active la protection XSS du navigateur |
| `Content-Security-Policy` | Personnalisée | Définit les sources autorisées pour les ressources |
| `Referrer-Policy` | strict-origin-when-cross-origin | Contrôle les informations de référent |

### Utilisation

Incluez simplement `security-init.php` au début de vos pages :

```php
<?php
require_once __DIR__ . '/../src/helpers/security-init.php';
?>
```

---

## Bonnes pratiques

### 1. Échapper TOUTES les sorties

```php
// ❌ MAL - Vulnérable XSS
echo $_POST['nom'];
echo $user['email'];

// ✅ BIEN - Sécurisé
echo e($_POST['nom']);
echo e($user['email']);
```

### 2. Valider les entrées

```php
// Validation d'email
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    die("Email invalide");
}

// Validation de nombre
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($id === false) {
    die("ID invalide");
}
```

### 3. Utiliser les requêtes préparées

```php
// ❌ MAL - Vulnérable SQL Injection
$query = "SELECT * FROM utilisateur WHERE id = " . $_GET['id'];

// ✅ BIEN - Sécurisé
$stmt = $dbh->prepare("SELECT * FROM utilisateur WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);
```

### 4. Vérifier les permissions

```php
<?php
require_once __DIR__ . '/../src/helpers/security-init.php';

// Vérifier l'authentification
requireAuth();

// Vérifier les droits admin
requireAdmin();
?>
```

### 5. Protéger les fichiers sensibles

Le fichier `.htaccess` doit empêcher l'accès direct aux fichiers sensibles :

```apache
# Bloquer l'accès aux fichiers .env
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

# Bloquer l'accès aux fichiers .git
RedirectMatch 404 /\.git
```

### 6. Upload de fichiers sécurisé

```php
// Vérifier le type MIME réel
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $_FILES['avatar']['tmp_name']);

// Whitelist des types autorisés
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mimeType, $allowedTypes)) {
    die("Type de fichier non autorisé");
}

// Générer un nom unique
$extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$filename = bin2hex(random_bytes(16)) . '.' . $extension;
```

---

## Checklist de sécurité

Avant de déployer en production, vérifiez :

- [ ] Fichier `.env` configuré et NON versionné
- [ ] `APP_ENV=production` et `APP_DEBUG=false` dans `.env`
- [ ] Tous les formulaires ont un token CSRF
- [ ] Toutes les sorties sont échappées avec `e()`
- [ ] Toutes les requêtes SQL utilisent des requêtes préparées
- [ ] Upload de fichiers validé et sécurisé
- [ ] Headers de sécurité HTTP actifs
- [ ] HTTPS activé en production
- [ ] Mots de passe hashés avec `password_hash()`
- [ ] Sessions configurées de manière sécurisée

---

## Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
