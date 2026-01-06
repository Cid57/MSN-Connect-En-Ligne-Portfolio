# 💬 MSN Connect - Application de Messagerie Moderne

> Application de messagerie instantanée inspirée de MSN Messenger, construite avec Laravel 12, Vue.js 3 et Tailwind CSS

[![Laravel](https://img.shields.io/badge/Laravel-12.44-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

## ✨ Fonctionnalités

- 🔐 **Authentification sécurisée** - Inscription, connexion, reset de mot de passe
- 💬 **Messagerie en temps réel** - Conversations privées et groupes
- 👥 **Gestion des contacts** - Ajout, suppression, statuts personnalisés
- 🎨 **Interface moderne** - Design responsive avec Tailwind CSS
- 📱 **Progressive Web App** - Fonctionne comme une app native
- 🔔 **Notifications en temps réel** - WebSockets avec Laravel Echo
- 📎 **Partage de fichiers** - Upload sécurisé d'images et documents
- 👔 **Panel administrateur** - Gestion complète des utilisateurs et espaces

## 🚀 Stack Technique

### Backend
- **Laravel 12.44** - Framework PHP moderne
- **PHP 8.2** - Dernière version avec types stricts
- **MySQL 8** - Base de données relationnelle
- **Redis** - Cache et sessions
- **Laravel Echo + Pusher** - WebSockets temps réel

### Frontend
- **Vue.js 3.5** - Framework JavaScript réactif
- **Tailwind CSS 4.0** - Framework CSS utility-first
- **Vite 7** - Build tool ultra-rapide
- **Axios** - HTTP client

### DevOps
- **Docker** - Containerisation
- **GitHub Actions** - CI/CD
- **PHPUnit** - Tests unitaires et fonctionnels

## 📋 Prérequis

- PHP >= 8.2
- Composer >= 2.8
- Node.js >= 20.x
- MySQL >= 8.0
- Redis >= 7.0 (optionnel)

## 🛠️ Installation

### 1. Cloner le repository

```bash
git clone https://github.com/Cid57/MSN-Connect-En-Ligne-Portfolio.git
cd MSN-Connect-En-Ligne-Portfolio
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Configurez votre `.env` :
```env
APP_NAME="MSN Connect"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=msn_connect
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=eu
```

### 5. Créer la base de données

```bash
mysql -u root -p
CREATE DATABASE msn_connect;
EXIT;
```

### 6. Exécuter les migrations

```bash
php artisan migrate --seed
```

### 7. Compiler les assets

```bash
# Développement (avec hot reload)
npm run dev

# Production
npm run build
```

### 8. Lancer le serveur

```bash
php artisan serve
```

Accédez à l'application : http://localhost:8000

## 🐳 Installation avec Docker

```bash
# Installer Laravel Sail
php artisan sail:install

# Démarrer les containers
./vendor/bin/sail up -d

# Exécuter les migrations
./vendor/bin/sail artisan migrate --seed

# Compiler les assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Accédez à l'application : http://localhost

## 📁 Structure du Projet

```
msn-connect/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controllers de l'application
│   │   └── Middleware/      # Middleware personnalisés
│   ├── Models/              # Models Eloquent
│   ├── Services/            # Logique métier
│   └── Events/              # Events & Listeners
│
├── database/
│   ├── migrations/          # Migrations de schéma
│   ├── seeders/             # Seeders de données
│   └── factories/           # Model factories
│
├── resources/
│   ├── views/               # Templates Blade
│   ├── js/
│   │   ├── components/      # Composants Vue.js
│   │   ├── pages/           # Pages Vue.js
│   │   └── app.js           # Point d'entrée JS
│   └── css/
│       └── app.css          # Styles Tailwind
│
├── routes/
│   ├── web.php              # Routes web
│   ├── api.php              # Routes API
│   └── channels.php         # Broadcasting channels
│
├── public/
│   ├── assets/              # Images et fichiers statiques
│   └── uploads/             # Fichiers uploadés
│
└── tests/
    ├── Feature/             # Tests fonctionnels
    └── Unit/                # Tests unitaires
```

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Avec couverture de code
php artisan test --coverage

# Tests spécifiques
php artisan test --filter=UserTest
```

## 📝 Commandes Artisan Utiles

```bash
# Créer un nouveau controller
php artisan make:controller MessageController

# Créer un model avec migration
php artisan make:model Channel -m

# Créer un composant Vue
php artisan make:component ChatMessage

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser l'application
php artisan optimize
```

## 🔐 Sécurité

- ✅ Protection CSRF sur tous les formulaires
- ✅ Validation stricte des inputs
- ✅ Hash des mots de passe avec Bcrypt
- ✅ Rate limiting sur les endpoints sensibles
- ✅ Upload sécurisé avec validation MIME
- ✅ Sanitization XSS automatique
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ HTTPS enforced en production

## 📊 Roadmap

- [x] Migration vers Laravel 12
- [x] Installation Tailwind CSS 4.0
- [x] Installation Vue.js 3.5
- [ ] Créer les migrations de base de données
- [ ] Implémenter les Models Eloquent
- [ ] Installer Laravel Breeze pour l'auth
- [ ] Créer l'API REST
- [ ] Configurer WebSockets (Laravel Echo)
- [ ] Docker setup complet
- [ ] Tests unitaires et fonctionnels
- [ ] CI/CD avec GitHub Actions
- [ ] Documentation API (OpenAPI)
- [ ] Mode hors-ligne (PWA)
- [ ] Notifications push
- [ ] Appels vidéo (WebRTC)

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📜 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👨‍💻 Auteur

**Cindy Singer**

- GitHub: [@Cid57](https://github.com/Cid57)
- Email: contact@example.com

## 🙏 Remerciements

- Laravel Team pour le framework extraordinaire
- Vue.js Team pour le framework frontend
- Tailwind Labs pour Tailwind CSS
- Claude Code (Anthropic) pour l'assistance au développement

---

<p align="center">Fait avec ❤️ et Laravel</p>
