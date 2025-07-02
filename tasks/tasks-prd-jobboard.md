## Fichiers pertinents

- `config/database.php` - Configuration de la base de données MySQL/MariaDB
- `config/config.php` - Configuration générale de l'application
- `src/models/User.php` - Modèle pour la gestion des utilisateurs (stagiaires, entreprises, admin)
- `src/models/ProfilStagiaire.php` - Modèle pour les profils des stagiaires
- `src/models/ProfilEntreprise.php` - Modèle pour les profils des entreprises
- `src/models/OffreStage.php` - Modèle pour les offres de stage
- `src/models/Candidature.php` - Modèle pour les candidatures
- `src/models/TypeContrat.php` - Modèle pour les types de contrat
- `src/controllers/AuthController.php` - Contrôleur pour l'authentification
- `src/controllers/StagiaireController.php` - Contrôleur pour l'espace stagiaire
- `src/controllers/EntrepriseController.php` - Contrôleur pour l'espace entreprise
- `src/controllers/AdminController.php` - Contrôleur pour l'espace administrateur
- `src/views/auth/login.php` - Page de connexion
- `src/views/auth/register.php` - Page d'inscription
- `src/views/stagiaire/dashboard.php` - Tableau de bord stagiaire
- `src/views/stagiaire/profil.php` - Page de profil stagiaire
- `src/views/stagiaire/offres.php` - Liste des offres pour stagiaires
- `src/views/entreprise/dashboard.php` - Tableau de bord entreprise
- `src/views/entreprise/profil.php` - Page de profil entreprise
- `src/views/entreprise/offres.php` - Gestion des offres entreprise
- `src/views/admin/dashboard.php` - Tableau de bord administrateur
- `src/views/admin/validation-offres.php` - Page de validation des offres
- `src/views/admin/gestion-utilisateurs.php` - Page de gestion des utilisateurs
- `public/index.php` - Point d'entrée principal de l'application
- `public/css/style.css` - Feuilles de style CSS responsive
- `public/js/app.js` - Scripts JavaScript pour l'interactivité
- `uploads/` - Répertoire pour les fichiers uploadés (CV, logos)
- `database/schema.sql` - Script de création de la base de données
- `composer.json` - Fichier de dépendances PHP
- `.htaccess` - Configuration Apache pour les URLs propres

### Notes

- Les fichiers uploadés (CV, logos) doivent être stockés dans le répertoire `uploads/` avec des noms sécurisés
- Utiliser Composer pour gérer les dépendances PHP
- Implémenter une architecture MVC claire pour faciliter la maintenance
- Assurer la sécurité des uploads et la validation des données

## Tâches

### 1.0 Configuration et infrastructure du projet ✅
- [x] 1.1 Initialiser le projet avec Composer
- [x] 1.2 Créer la structure de répertoires MVC
- [x] 1.3 Configurer la base de données MySQL/MariaDB
- [x] 1.4 Créer le fichier de configuration principal
- [x] 1.5 Configurer l'autoloading des classes (via Composer)
- [x] 1.6 Créer le script de création de la base de données
- [x] 1.7 Configurer Apache avec .htaccess pour les URLs propres
- [x] 1.8 Créer le point d'entrée principal (index.php)

### 2.0 Système d'authentification et gestion des comptes ✅
- [x] 2.1 Créer le modèle User avec les rôles (stagiaire, entreprise, admin) ✅
- [x] 2.2 Implémenter le système de hachage des mots de passe ✅
- [x] 2.3 Créer les contrôleurs d'authentification (login, register, logout) ✅
- [x] 2.4 Développer les vues de connexion et inscription ✅
- [x] 2.5 Mettre en place la gestion des sessions ✅
- [x] 2.6 Créer le middleware d'authentification et autorisation ✅
- [x] 2.7 Intégrer le système d'authentification dans le routeur ✅
- [x] 2.8 Configurer l'autoloading PSR-4 ✅
- [ ] 2.9 Implémenter la vérification par email (optionnel)
- [ ] 2.10 Créer le système de récupération de mot de passe (optionnel)

### 3.0 Module stagiaire (profil et candidatures) ✅
- [x] 3.1 Créer le modèle ProfilStagiaire ✅
- [x] 3.2 Développer le contrôleur StagiaireController ✅
- [x] 3.3 Créer le tableau de bord stagiaire ✅
- [x] 3.4 Implémenter la création/modification du profil ✅
- [x] 3.5 Ajouter l'upload sécurisé de CV (PDF uniquement) ✅
- [ ] 3.6 Créer le système de lettre de motivation
- [ ] 3.7 Développer la recherche et filtrage des offres
- [ ] 3.8 Implémenter le système de candidature
- [ ] 3.9 Créer l'historique des candidatures
- [ ] 3.10 Ajouter les notifications de statut de candidature

### 4.0 Module entreprise (profil et offres) ✅
- [x] 4.1 Créer le modèle ProfilEntreprise ✅
- [x] 4.2 Créer le modèle OffreStage ✅
- [x] 4.3 Développer le contrôleur EntrepriseController ✅
- [x] 4.4 Créer le tableau de bord entreprise ✅
- [x] 4.5 Implémenter la création/modification du profil entreprise ✅
- [x] 4.6 Ajouter l'upload du logo entreprise ✅
- [x] 4.7 Développer la création d'offres de stage ✅
- [x] 4.8 Implémenter la gestion des offres (modifier, supprimer) ✅
- [x] 4.9 Créer le système de gestion des candidatures reçues ✅
- [x] 4.10 Ajouter la sélection et notification des candidats ✅
- [x] 4.11 Implémenter les statistiques des offres ✅

### 5.0 Module administrateur (validation et gestion) ✅
- [x] 5.1 Développer le contrôleur AdminController
- [x] 5.2 Créer le tableau de bord administrateur
- [x] 5.3 Implémenter la validation des offres de stage
- [x] 5.4 Créer la gestion des utilisateurs (activation/désactivation)
- [x] 5.5 Développer la modération des profils
- [x] 5.6 Ajouter les statistiques globales de la plateforme
- [x] 5.7 Implémenter la gestion des types de contrat
- [x] 5.8 Créer le système de sauvegarde des données
- [x] 5.9 Ajouter les logs d'activité

### 6.0 Interface utilisateur responsive et sécurité ✅
- [x] 6.1 Développer le design responsive avec CSS Grid/Flexbox ✅
- [x] 6.2 Créer les composants UI réutilisables ✅
- [x] 6.3 Implémenter la navigation adaptative ✅
- [x] 6.4 Ajouter les interactions JavaScript ✅
- [x] 6.5 Optimiser pour mobile et tablette ✅
- [x] 6.6 Implémenter la protection CSRF ✅
- [x] 6.7 Sécuriser les uploads de fichiers ✅
- [x] 6.8 Ajouter la validation côté client et serveur ✅
- [x] 6.9 Implémenter la limitation de taux (rate limiting) ✅
- [x] 6.10 Effectuer les tests de sécurité ✅

### Notes d'implémentation
- Priorité 1: Tâches 1.0, 2.0 (infrastructure et auth)
- Priorité 2: Tâches 3.0, 4.0 (modules principaux)
- Priorité 3: Tâches 5.0, 6.0 (admin et finitions)
- Chaque tâche doit être testée avant de passer à la suivante
- Utiliser des commits Git fréquents pour chaque sous-tâche complétée