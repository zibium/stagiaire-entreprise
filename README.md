# JobBoard - Plateforme de Stages

Une plateforme moderne de mise en relation entre stagiaires et entreprises, développée en PHP avec une architecture MVC propre.

## 🚀 Fonctionnalités

### Authentification
- **Pages de connexion spécialisées** : Interfaces dédiées pour entreprises et stagiaires
- **Inscription sécurisée** : Validation côté client et serveur
- **Gestion des sessions** : Sessions sécurisées avec protection CSRF
- **"Se souvenir de moi"** : Fonctionnalité de connexion persistante

### Architecture
- **MVC Pattern** : Séparation claire des responsabilités
- **PSR-4 Autoloading** : Chargement automatique des classes via Composer
- **Services Layer** : Services réutilisables pour la logique métier
- **Middleware** : Gestion de l'authentification et de la sécurité

### Sécurité
- **Protection CSRF** : Tokens anti-falsification de requête
- **Hachage des mots de passe** : Utilisation de `password_hash()`
- **Validation des données** : Validation stricte côté serveur
- **Logging des tentatives** : Traçabilité des connexions

## 🛠️ Technologies Utilisées

- **PHP 8.1+** : Langage principal
- **Bootstrap 5** : Framework CSS pour l'interface
- **Font Awesome** : Icônes
- **PDO** : Accès à la base de données
- **Composer** : Gestionnaire de dépendances

## 📁 Structure du Projet

```
Dev1/
├── config/                 # Configuration de l'application
│   ├── config.php          # Configuration générale
│   └── database.php        # Configuration base de données
├── public/                 # Point d'entrée web
│   ├── index.php           # Routeur principal
│   ├── css/                # Feuilles de style
│   └── js/                 # Scripts JavaScript
├── src/
│   ├── controllers/        # Contrôleurs MVC
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── EntrepriseController.php
│   │   └── StagiaireController.php
│   ├── models/             # Modèles de données
│   │   ├── User.php
│   │   ├── OffreStage.php
│   │   └── ...
│   ├── services/           # Services métier
│   │   ├── ConfigService.php
│   │   ├── ValidationService.php
│   │   └── LogService.php
│   ├── middleware/         # Middleware
│   ├── utils/              # Utilitaires
│   └── views/              # Vues (templates)
│       ├── auth/           # Pages d'authentification
│       ├── entreprise/     # Pages entreprise
│       └── stagiaire/      # Pages stagiaire
├── logs/                   # Fichiers de log
├── uploads/                # Fichiers uploadés
└── vendor/                 # Dépendances Composer
```

## 🔧 Installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-repo]
   cd Dev1
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configuration**
   - Copier `.env.example` vers `.env`
   - Configurer la base de données dans `config/database.php`

4. **Base de données**
   ```bash
   # Importer le schéma
   mysql -u username -p database_name < database/schema.sql
   ```

5. **Serveur de développement**
   ```bash
   php -S localhost:8000 -t public/
   ```

## 🌐 Routes Disponibles

### Authentification
- `GET /` : Page d'accueil avec sélection du type d'utilisateur
- `GET /auth/login` : Page de sélection de connexion
- `GET /auth/login-entreprise` : Connexion entreprise
- `GET /auth/login-stagiaire` : Connexion stagiaire
- `GET /auth/register` : Inscription
- `POST /auth/login` : Traitement de la connexion
- `POST /auth/register` : Traitement de l'inscription
- `GET /auth/logout` : Déconnexion

### Dashboards
- `GET /entreprise/dashboard` : Tableau de bord entreprise
- `GET /stagiaire/dashboard` : Tableau de bord stagiaire

## 🎨 Interface Utilisateur

### Pages de Connexion
- **Design responsive** : Compatible mobile et desktop
- **Thèmes spécialisés** : Vert pour entreprises, bleu pour stagiaires
- **UX optimisée** : Visibilité des mots de passe, validation en temps réel
- **Accessibilité** : Icônes et labels clairs

### Fonctionnalités UX
- **Messages d'erreur contextuels** : Feedback utilisateur immédiat
- **Validation côté client** : Vérification en temps réel
- **Loading states** : Indicateurs de chargement
- **Navigation intuitive** : Liens et boutons bien placés

## 🔒 Sécurité

### Mesures Implémentées
- **Protection CSRF** : Tous les formulaires protégés
- **Validation stricte** : Données validées côté serveur
- **Sessions sécurisées** : Configuration optimale
- **Logging** : Traçabilité des actions sensibles
- **Hachage sécurisé** : Mots de passe avec `PASSWORD_DEFAULT`

## 📊 Services Disponibles

### ConfigService
- Gestion centralisée de la configuration
- Support des clés imbriquées (`app.name`)
- Valeurs par défaut

### ValidationService
- Validation d'emails, mots de passe, fichiers
- Messages d'erreur personnalisables
- Règles réutilisables

### LogService
- Logging multi-niveaux (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- Logging des exceptions
- Traçabilité des connexions et inscriptions

## 🚀 Améliorations Récentes

1. **Architecture modernisée** :
   - Autoloading PSR-4 avec Composer
   - Services layer pour la logique métier
   - Configuration centralisée

2. **Interface utilisateur** :
   - Pages de connexion spécialisées
   - Design Bootstrap 5 moderne
   - UX optimisée avec validation temps réel

3. **Sécurité renforcée** :
   - Protection CSRF complète
   - Validation stricte des données
   - Logging des actions sensibles

4. **Maintenabilité** :
   - Code organisé et documenté
   - Services réutilisables
   - Configuration flexible

## 📝 Développement

### Bonnes Pratiques
- **PSR-4** : Autoloading standard
- **Separation of Concerns** : Chaque classe a une responsabilité
- **DRY** : Code réutilisable via les services
- **Security First** : Sécurité intégrée dès la conception

### Tests
```bash
# Lancer les tests
vendor/bin/phpunit
```

## 📞 Support

Pour toute question ou problème, consultez les logs dans le dossier `logs/` ou contactez l'équipe de développement.