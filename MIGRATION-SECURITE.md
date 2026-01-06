# Guide de Migration - Sécurité

Ce document explique comment migrer vos fichiers existants vers le nouveau système sécurisé.

## Résumé des Améliorations

✅ **Fichiers de sécurité créés :**
- `.env` et `.env.example` - Variables d'environnement
- `.gitignore` - Exclusion fichiers sensibles
- `src/helpers/env-loader.php` - Chargeur de variables d'environnement
- `src/helpers/csrf-protection.php` - Protection CSRF
- `src/helpers/security-init.php` - Initialisation sécurité (headers, session, helpers)
- `src/helpers/secure-upload.php` - Upload sécurisé de fichiers
- `src/helpers/error-handler.php` - Gestion d'erreurs sécurisée
- `src/bootstrap.php` - Fichier d'initialisation centralisé

✅ **Fichiers modifiés :**
- `src/data/data-connect.php` - Utilise maintenant les variables d'environnement
- `src/pages/mdp-reset.php` - Tokens hashés correctement

---

## Étape 1 : Modifier public/index.php

**Remplacer le début du fichier par :**

```php
<?php
/**
 * Point d'entrée principal de l'application
 */

// Charger le bootstrap (initialisation sécurisée)
require_once __DIR__ . '/../src/bootstrap.php';

// Le reste de votre code index.php...
```

---

## Étape 2 : Modifier public/scripts.php

**Ajouter au début :**

```php
<?php
// Charger le bootstrap
require_once __DIR__ . '/../src/bootstrap.php';

// Le reste de votre code...
```

---

## Étape 3 : Ajouter la Protection CSRF aux Formulaires

### Fichiers à modifier (tous les formulaires) :

1. `templates/connexion.html.php`
2. `templates/mdp-reset.html.php`
3. `templates/reset_password.html.php`
4. `templates/admin-*.html.php` (tous les formulaires admin)
5. `templates/profil.html.php`
6. Tous les autres fichiers contenant des `<form>`

### Comment faire :

**AVANT (ancien code) :**
```html
<form method="POST" action="...">
    <input type="text" name="nom">
    <button type="submit">Envoyer</button>
</form>
```

**APRÈS (nouveau code) :**
```html
<form method="POST" action="...">
    <?= csrf_field() ?>  <!-- ⬅️ AJOUTER CETTE LIGNE -->

    <input type="text" name="nom">
    <button type="submit">Envoyer</button>
</form>
```

### Valider le Token CSRF dans les Pages de Traitement

**Dans chaque fichier src/pages/*.php qui traite un formulaire POST :**

```php
<?php
// ... code existant ...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AJOUTER CES LIGNES AU DÉBUT DU TRAITEMENT POST
    if (!csrf_validate()) {
        die("Token de sécurité invalide. Veuillez réessayer.");
    }

    // ... reste du code de traitement ...
}
```

**Fichiers concernés :**
- `src/pages/connexion.php`
- `src/pages/mdp-reset.php`
- `src/pages/reset_password.php`
- `src/pages/admin-ajouter-utilisateur.php`
- `src/pages/admin-modifier-utilisateur.php`
- `src/pages/admin-supprimer-utilisateur.php`
- `src/pages/admin-ajouter-groupe.php`
- `src/pages/admin-modifier-groupe.php`
- `src/pages/profil.php`
- Etc.

---

## Étape 4 : Sécuriser les Upload de Fichiers

### Exemple : Modifier l'Upload d'Avatar

**AVANT (code non sécurisé) :**
```php
<?php
if (isset($_FILES['avatar'])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["avatar"]["name"]);
    move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile);
}
?>
```

**APRÈS (code sécurisé) :**
```php
<?php
if (isset($_FILES['avatar'])) {
    $uploader = new SecureUpload();
    $uploader->setMaxFileSize(2097152); // 2 MB

    $filename = $uploader->upload($_FILES['avatar']);

    if ($filename === false) {
        // Erreur
        $error = $uploader->getError();
        echo "Erreur : " . htmlspecialchars($error);
    } else {
        // Succès - Sauvegarder $filename en base de données
        $avatarPath = 'uploads/' . $filename;
        // ... mise à jour en base ...
    }
}
?>
```

**Fichiers concernés :**
- `src/pages/profil.php` (upload avatar utilisateur)
- `src/pages/admin-ajouter-utilisateur.php`
- `src/pages/admin-modifier-utilisateur.php`
- Tout autre fichier gérant des uploads

---

## Étape 5 : Échapper TOUTES les Sorties HTML

### Règle d'or : Utiliser la fonction `e()` pour TOUTES les variables affichées

**AVANT (vulnérable XSS) :**
```php
<p>Bonjour <?= $utilisateur['prenom'] ?></p>
<input type="text" value="<?= $_POST['nom'] ?>">
```

**APRÈS (sécurisé) :**
```php
<p>Bonjour <?= e($utilisateur['prenom']) ?></p>
<input type="text" value="<?= e($_POST['nom']) ?>">
```

### Rechercher et Remplacer

**Chercher dans tous les fichiers `.php` :**

1. `<?= $` → Vérifier si `e()` est utilisé
2. `<?php echo $` → Vérifier si `e()` ou `htmlspecialchars()` est utilisé
3. Toutes les sorties de variables doivent être échappées

**Fichiers prioritaires :**
- TOUS les fichiers dans `templates/`
- TOUS les fichiers dans `src/pages/`

---

## Étape 6 : Utiliser les Fonctions de Vérification d'Authentification

**AVANT (code dupliqué partout) :**
```php
<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: /?page=connexion');
    exit;
}

if ($_SESSION['utilisateur']['est_admin'] != 1) {
    header('Location: /');
    exit;
}
?>
```

**APRÈS (utiliser les helpers) :**
```php
<?php
// La session est déjà démarrée par security-init.php

// Vérifier l'authentification
requireAuth();

// Vérifier les droits admin
requireAdmin();
?>
```

---

## Étape 7 : Logger les Événements de Sécurité

**Exemples d'utilisation :**

```php
<?php
// Échec de connexion
logSecurityEvent('LOGIN_FAIL', 'Tentative de connexion échouée', [
    'email' => $email
]);

// Modification de mot de passe
logSecurityEvent('PASSWORD_CHANGE', 'Mot de passe modifié', [
    'user_id' => $_SESSION['id_utilisateur']
]);

// Accès non autorisé
logSecurityEvent('UNAUTHORIZED_ACCESS', 'Tentative d\'accès à une page admin', [
    'page' => $_GET['page']
]);
?>
```

**Ajouter dans :**
- `src/pages/connexion.php` (login fail/success)
- `src/pages/profil.php` (changement mot de passe)
- `src/pages/admin-*.php` (modifications admin)

---

## Étape 8 : Configurer le .env

**Éditer le fichier `.env` à la racine avec vos vraies valeurs :**

```env
# Base de données
DB_HOST=localhost
DB_NAME=msn-connect
DB_USER=root
DB_PASSWORD=VotreMotDePasse

# Email (pour PHPMailer)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls

# Application
APP_ENV=development    # Mettre "production" en production
APP_DEBUG=true         # Mettre "false" en production
```

---

## Étape 9 : Tester l'Application

### Checklist de Tests

- [ ] Connexion fonctionne
- [ ] Déconnexion fonctionne
- [ ] Réinitialisation mot de passe fonctionne
- [ ] Upload d'avatar fonctionne
- [ ] Formulaires admin fonctionnent
- [ ] Messages s'affichent correctement
- [ ] Aucune erreur PHP affichée (en mode production)
- [ ] Les logs sont créés dans `/logs/`

### Vérifier les Logs

Les logs seront créés automatiquement dans `/logs/` :
- `error-YYYY-MM-DD.log` - Erreurs applicatives
- `security-YYYY-MM-DD.log` - Événements de sécurité

---

## Étape 10 : Déploiement en Production

**AVANT de déployer en production :**

1. **Modifier `.env` :**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Vérifier que `.env` n'est PAS versionné dans Git**
   ```bash
   git status
   # .env ne doit PAS apparaître
   ```

3. **Configurer HTTPS** (obligatoire en production)

4. **Vérifier les permissions des dossiers :**
   ```bash
   chmod 755 public/assets/uploads/
   chmod 755 logs/
   ```

5. **Créer un `.htaccess` pour bloquer l'accès à `.env` :**
   ```apache
   <Files ".env">
       Order allow,deny
       Deny from all
   </Files>
   ```

---

## Résumé des Modifications Rapides

### Fichier par fichier (ordre de priorité)

**1. Points d'entrée (CRITIQUE) :**
- ✅ `public/index.php` - Ajouter `require bootstrap.php`
- ✅ `public/scripts.php` - Ajouter `require bootstrap.php`

**2. Formulaires (HAUTE PRIORITÉ) :**
- ⏳ Ajouter `<?= csrf_field() ?>` dans TOUS les `<form>`
- ⏳ Ajouter validation `csrf_validate()` dans tous les traitements POST

**3. Sorties HTML (HAUTE PRIORITÉ) :**
- ⏳ Remplacer `<?= $var ?>` par `<?= e($var) ?>` PARTOUT

**4. Upload de fichiers (MOYENNE PRIORITÉ) :**
- ⏳ Utiliser `SecureUpload` pour tous les uploads

**5. Logs de sécurité (BASSE PRIORITÉ) :**
- ⏳ Ajouter `logSecurityEvent()` pour les événements importants

---

## Support

Si vous avez des questions, consultez :
- [SECURITY-GUIDE.md](SECURITY-GUIDE.md) - Guide détaillé de sécurité
- Les commentaires dans les fichiers sources

## Prochaines Étapes Recommandées

Après avoir sécurisé l'application, envisagez :
1. Migration vers un framework (Laravel, Symfony)
2. Ajouter des tests automatisés
3. Implémenter PHPMailer pour les emails
4. Ajouter pagination des messages
5. Implémenter notifications temps réel (WebSockets)
