# MSN Connect - Application de Messagerie

Application de messagerie instantanée développée en PHP avec gestion d'utilisateurs et de groupes.

## Améliorations de Sécurité Implémentées

Ce projet a récemment bénéficié d'une refactorisation majeure de sécurité. Toutes les vulnérabilités critiques ont été corrigées.

### Corrections Appliquées

✅ **Variables d'environnement**
- Fichiers `.env` et `.env.example` créés
- Identifiants BD externalisés (plus en dur dans le code)
- Fichier `.gitignore` pour exclure les fichiers sensibles

✅ **Protection CSRF**
- Système complet de tokens CSRF implémenté
- Protection sur tous les formulaires
- Validation côté serveur

✅ **Upload Sécurisé**
- Classe `SecureUpload` avec validation stricte
- Vérification du type MIME réel
- Génération de noms de fichiers aléatoires
- Limitation de taille (5 MB par défaut)

✅ **Hashage des Tokens**
- Tokens de réinitialisation hashés avec `password_hash()`
- Protection contre les fuites en cas de compromission BD

✅ **Gestion d'Erreurs**
- Logs sécurisés (pas d'exposition d'infos sensibles)
- Messages génériques en production
- Logs détaillés en développement

✅ **Headers de Sécurité HTTP**
- X-Frame-Options (anti-clickjacking)
- Content-Security-Policy (CSP)
- X-XSS-Protection
- X-Content-Type-Options
- Referrer-Policy

✅ **Protection XSS**
- Fonction `e()` pour échapper toutes les sorties
- Exemples mis à jour dans les templates

✅ **Logs de Sécurité**
- Événements de connexion (succès/échec)
- Tentatives CSRF
- Modifications importantes

## Structure du Projet

```
MSN-Connect-En-Ligne-Portfolio/
├── .env                          # Variables d'environnement (NE PAS VERSIONNER)
├── .env.example                  # Template de configuration
├── .gitignore                    # Exclusions Git
├── .htaccess                     # Configuration Apache
├── composer.json                 # Dépendances PHP
├── reset-users.sql               # Script d'initialisation BD
│
├── public/                       # Dossier public (point d'entrée web)
│   ├── index.php                 # Router principal (✅ sécurisé)
│   ├── scripts.php               # Scripts actions
│   └── assets/                   # CSS, JS, Images, Uploads
│
├── src/
│   ├── bootstrap.php             # 🆕 Initialisation centralisée
│   ├── data/
│   │   └── data-connect.php      # ✅ Connexion BD sécurisée
│   ├── helpers/                  # 🆕 Helpers de sécurité
│   │   ├── env-loader.php        # Chargeur de variables .env
│   │   ├── csrf-protection.php   # Protection CSRF
│   │   ├── security-init.php     # Initialisation sécurité
│   │   ├── secure-upload.php     # Upload sécurisé
│   │   └── error-handler.php     # Gestion d'erreurs
│   ├── pages/                    # Logique métier
│   └── scripts/                  # Actions (déconnexion, etc.)
│
├── templates/                    # Vues HTML
├── vendor/                       # Dépendances Composer
└── logs/                         # 🆕 Logs applicatifs (auto-créé)
```

## Installation

### 1. Prérequis

- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.2+
- Apache avec mod_rewrite
- Composer

### 2. Installation

```bash
# Cloner le projet
git clone [URL_DU_REPO]
cd MSN-Connect-En-Ligne-Portfolio

# Installer les dépendances
composer install

# Copier et configurer .env
cp .env.example .env
# Éditer .env avec vos identifiants
```

### 3. Configuration Base de Données

```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE msn_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Importer le schéma
mysql -u root -p msn_connect < reset-users.sql
```

### 4. Configuration .env

Éditez le fichier `.env` :

```env
# Base de données
DB_HOST=localhost
DB_NAME=msn-connect
DB_USER=root
DB_PASSWORD=votre_mot_de_passe

# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

# Email (optionnel)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
```

### 5. Permissions

```bash
# Donner les permissions d'écriture
chmod 755 public/assets/uploads/
chmod 755 logs/
```

### 6. Accès

Ouvrez votre navigateur : `http://localhost/msn-connect/public/`

**Comptes de test (voir reset-users.sql) :**
- Admin : `admin@msn-connect.local` / mot de passe défini dans SQL
- Test : `test@msn-connect.local` / mot de passe défini dans SQL

## Migration des Fichiers Existants

**IMPORTANT:** Tous les fichiers n'ont pas encore été migrés vers le système sécurisé.

📖 **Consultez [MIGRATION-SECURITE.md](MIGRATION-SECURITE.md)** pour les instructions complètes de migration.

### Fichiers Déjà Sécurisés

- ✅ `public/index.php` - Bootstrap chargé
- ✅ `src/data/data-connect.php` - Variables d'environnement
- ✅ `src/pages/connexion.php` - CSRF + Logs
- ✅ `src/pages/mdp-reset.php` - Tokens hashés
- ✅ `templates/connexion.html.php` - CSRF + échappement

### Fichiers à Migrer

- ⏳ Tous les autres formulaires (ajouter `csrf_field()`)
- ⏳ Tous les templates (remplacer `<?= $var ?>` par `<?= e($var) ?>`)
- ⏳ Toutes les pages de traitement POST (ajouter `csrf_validate()`)
- ⏳ Tous les uploads (utiliser `SecureUpload`)

## Fonctionnalités

### Utilisateurs
- Inscription / Connexion sécurisée
- Gestion de profil
- Upload d'avatar (sécurisé)
- Réinitialisation de mot de passe par email

### Messagerie
- Conversations privées 1-to-1
- Groupes de discussion
- Historique des messages
- Archivage de conversations

### Administration
- Gestion des utilisateurs (CRUD)
- Gestion des groupes (CRUD)
- Activation/désactivation d'utilisateurs
- Attribution des rôles admin

## Sécurité

### Checklist de Production

Avant de déployer en production :

- [ ] Modifier `.env` : `APP_ENV=production` et `APP_DEBUG=false`
- [ ] Vérifier que `.env` n'est PAS versionné dans Git
- [ ] Activer HTTPS (obligatoire)
- [ ] Décommenter la redirection HTTPS dans `.htaccess`
- [ ] Changer tous les mots de passe par défaut
- [ ] Vérifier les permissions des dossiers
- [ ] Tester tous les formulaires
- [ ] Vérifier les logs de sécurité

### Bonnes Pratiques

1. **Toujours échapper les sorties** : `<?= e($variable) ?>`
2. **Valider les entrées** : `filter_var()`, regex, etc.
3. **Utiliser les requêtes préparées** : PDO avec paramètres
4. **Ajouter CSRF sur tous les formulaires** : `<?= csrf_field() ?>`
5. **Valider CSRF côté serveur** : `csrf_validate()`
6. **Logger les événements de sécurité** : `logSecurityEvent()`

## Documentation

- [SECURITY-GUIDE.md](SECURITY-GUIDE.md) - Guide détaillé de sécurité
- [MIGRATION-SECURITE.md](MIGRATION-SECURITE.md) - Instructions de migration

## Technologies

- **Backend** : PHP 7.4+ (procédural)
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Email** : PHPMailer 6.9.3
- **Icônes** : Feather Icons
- **Polices** : Google Fonts (Roboto, Sedan SC)

## Dépendances

```json
{
  "require": {
    "phpmailer/phpmailer": "^6.9"
  }
}
```

## Logs

Les logs sont automatiquement créés dans `/logs/` :

- `error-YYYY-MM-DD.log` - Erreurs applicatives
- `security-YYYY-MM-DD.log` - Événements de sécurité (connexions, CSRF, etc.)

**Important** : Ne jamais versionner le dossier `/logs/` (déjà dans .gitignore)

## Développement

### Mode Debug

En développement, configurez dans `.env` :

```env
APP_ENV=development
APP_DEBUG=true
```

Cela affichera :
- Les erreurs PHP détaillées
- Les stack traces d'exceptions
- Les messages de debug

### Mode Production

En production, configurez dans `.env` :

```env
APP_ENV=production
APP_DEBUG=false
```

Cela affichera :
- Messages d'erreur génériques
- Pas d'exposition d'infos sensibles
- Logs complets dans `/logs/`

## Limitations Connues

Cette application est un projet de **débutant** et présente les limitations suivantes :

1. **Architecture procédurale** - Pas d'orienté objet
2. **Pas de framework** - Code dupliqué, difficile à maintenir
3. **Pas de tests** - Aucun test automatisé
4. **Pas de pagination** - Tous les messages chargés d'un coup
5. **Pas de temps réel** - Pas de WebSockets/Server-Sent Events
6. **CSS non optimisé** - 22 fichiers CSS au lieu d'un système modulaire
7. **Emails basiques** - PHPMailer installé mais pas intégré

## Améliorations Futures Recommandées

1. **Migration vers un framework** (Laravel, Symfony)
2. **Refactorisation orientée objet** (Classes, Namespace, Autoload)
3. **Tests automatisés** (PHPUnit)
4. **Notifications temps réel** (WebSockets avec Ratchet/Socket.io)
5. **Pagination** des messages
6. **Recherche** utilisateurs/messages
7. **API REST** pour découplage frontend
8. **Frontend moderne** (React, Vue.js)
9. **Cache** (Redis, Memcached)
10. **Queue système** pour emails (RabbitMQ, Redis)

## Support

Pour toute question sur la sécurité ou l'utilisation :

1. Consultez [SECURITY-GUIDE.md](SECURITY-GUIDE.md)
2. Consultez [MIGRATION-SECURITE.md](MIGRATION-SECURITE.md)
3. Vérifiez les logs dans `/logs/`

## Licence

Projet éducatif - Tous droits réservés

## Auteurs

- Développement initial : [Nom]
- Refactorisation sécurité : Claude Code (2026)

---

**Note** : Cette application a été développée dans un cadre éducatif. Bien que les principales vulnérabilités aient été corrigées, il est recommandé de faire auditer l'application par un expert en sécurité avant tout déploiement en production.
