Excellent ! Merci beaucoup pour ces réponses précises. Elles sont exactement ce qu'il fallait pour transformer le brouillon en un document solide et directement exploitable.

J'ai intégré toutes vos clarifications. Le point le plus important est la gestion des notifications : le système ne gérera pas l'envoi d'emails, la communication se fera en dehors de la plateforme. C'est une simplification très utile pour une première version. La validation des offres par un admin est aussi une règle métier cruciale que j'ai ajoutée.

Voici le document final, prêt à être sauvegardé sous le nom `prd-jobboard.md` dans votre dossier `/tasks` et à être transmis à un développeur.

---

### **Product Requirements Document (PRD): JobBoard**

#### **1. Introduction/Overview**

Ce document décrit les exigences pour la création d'une plateforme web, nommée "JobBoard", destinée à mettre en relation des stagiaires à la recherche d'opportunités et des entreprises proposant des offres de stage. Le système doit permettre aux entreprises de publier des offres (soumises à validation), aux stagiaires de créer un profil complet pour y postuler, et aux administrateurs de superviser la qualité et le bon fonctionnement de la plateforme. L'objectif de la première version est de fournir des profils utilisateurs robustes et un processus de candidature simple.

#### **2. Goals**

- **Pour les Stagiaires :** Centraliser et simplifier la recherche de stages en offrant un accès direct à des offres qualifiées et vérifiées.
- **Pour les Entreprises :** Faciliter le processus de recrutement de stagiaires en leur fournissant une plateforme pour publier des offres détaillées et recevoir des candidatures ciblées.
- **Pour l'Administrateur :** Assurer la qualité du contenu, garantir la pertinence des offres, et obtenir une vue d'ensemble de l'activité de la plateforme.

#### **3. User Stories**

**Stagiaire :**

- **En tant que** stagiaire, **je veux** créer un compte et un profil personnel complet (informations de contact, upload de mon CV, rédaction d'une lettre de motivation type) **afin de** pouvoir postuler facilement aux offres.
- **En tant que** stagiaire, **je veux** rechercher et filtrer les offres de stage (par type de contrat, niveau d'étude, etc.) **afin de** trouver rapidement celles qui me correspondent.
- **En tant que** stagiaire, **je veux** postuler à une offre **afin de** soumettre ma candidature à l'entreprise.
- **En tant que** stagiaire, **je veux** consulter un tableau de bord **afin de** voir l'historique des offres auxquelles j'ai postulé.

**Entreprise :**

- **En tant que** recruteur, **je veux** créer un compte et un profil pour mon entreprise **afin de** présenter mon activité aux candidats.
- **En tant que** recruteur, **je veux** publier une nouvelle offre de stage via un formulaire détaillé **afin d'**attirer les profils adéquats.
- **En tant que** recruteur, **je veux** que mes offres soient en attente de validation **afin de** m'assurer qu'elles respectent les standards de la plateforme.
- **En tant que** recruteur, **je veux** consulter la liste des candidats ayant postulé à mes offres (avec accès à leur profil, CV et lettre de motivation) **afin de** pouvoir les contacter par email ou téléphone pour la suite du processus.

**Administrateur :**

- **En tant qu'**administrateur, **je veux** revoir et approuver ou rejeter chaque nouvelle offre soumise **afin de** garantir la qualité et la légitimité des annonces sur le site.
- **En tant qu'**administrateur, **je veux** gérer les comptes utilisateurs (stagiaires et entreprises) **afin de** pouvoir les activer ou les suspendre en cas d'abus.
- **En tant qu'**administrateur, **je veux** consulter des statistiques d'utilisation **afin de** suivre la performance de la plateforme.
- **En tant qu'**administrateur, **je veux** gérer les listes de valeurs (ex: types de contrat) **afin de** structurer les données du site.

#### **4. Functional Requirements**

**Gestion des Comptes (Commun)**

1.  Le système doit permettre à un utilisateur de s'inscrire en tant que "Stagiaire" ou "Entreprise".
2.  Le système doit permettre à un utilisateur de se connecter avec son email et un mot de passe.
3.  Le système doit inclure une fonction "mot de passe oublié".

**Module Stagiaire** 4. Le stagiaire doit pouvoir créer et modifier son profil (informations personnelles, contact). 5. Le profil stagiaire doit permettre l'**upload d'un fichier CV** (formats autorisés : PDF, DOCX). 6. Le profil stagiaire doit inclure un champ de texte (`textarea`) pour rédiger une **lettre de motivation** générique, modifiable à chaque candidature. 7. Le système doit afficher une liste des offres de stage dont le statut est "Approuvée". 8. Le stagiaire doit pouvoir postuler à une offre. Le système enregistre alors une `Candidature` liant le `Stagiaire` à l'`OffreStage`. 9. Le stagiaire doit avoir un tableau de bord listant ses candidatures envoyées.

**Module Entreprise** 10. L'entreprise doit pouvoir créer et modifier son profil (nom, description, logo, etc.). 11. L'entreprise doit pouvoir créer une nouvelle offre de stage via un formulaire. Ce formulaire doit inclure les champs : `Titre`, `Description`, `Durée du stage (en mois)`, `Type de contrat` (liste déroulante), `Niveau d'étude requis`. 12. À sa création, une offre a le statut "En attente de validation" et n'est pas visible publiquement. 13. L'entreprise doit pouvoir modifier ou supprimer ses propres offres. 14. L'entreprise doit avoir un tableau de bord listant ses offres publiées. Pour chaque offre, elle doit pouvoir voir la liste des stagiaires ayant postulé. 15. L'entreprise doit pouvoir consulter le profil complet d'un candidat (infos, CV, lettre de motivation).

**Module Administrateur** 16. L'administrateur doit se connecter via une interface séparée et sécurisée. 17. L'administrateur doit voir une liste des offres "En attente de validation". 18. L'administrateur doit pouvoir **approuver** ou **rejeter** une offre. Une offre approuvée devient visible pour les stagiaires. 19. L'administrateur doit pouvoir voir et gérer tous les utilisateurs (activer, suspendre, supprimer). 20. L'administrateur doit avoir un panneau pour gérer les options du champ "Type de contrat" (ex: "Stage académique", "Stage professionnel", "Alternance", "Stage de vacances").

#### **5. Non-Goals (Out of Scope for V1)**

- **Aucune notification par email** n'est envoyée par le système (ni aux entreprises pour les nouvelles candidatures, ni aux stagiaires pour le traitement). La communication se fait en dehors de la plateforme.
- Un système de messagerie instantanée entre utilisateurs.
- Un système de paiement ou d'abonnement.
- L'intégration avec des API externes (ex: LinkedIn).
- Un système de notation des entreprises ou des stagiaires.

#### **6. Design Considerations (Optional)**

- L'interface doit être simple, claire et intuitive pour un utilisateur non technique.
- Le design doit être responsive (utilisable sur mobile, tablette et ordinateur).
- Le développement se basera sur une structure fonctionnelle standard, par exemple avec un framework CSS comme Bootstrap ou Tailwind CSS pour accélérer le développement front-end.

#### **7. Technical Considerations (Optional)**

- **Backend :** PHP (version 7.4 ou supérieure).
- **Database :** MySQL ou MariaDB.
- **Modèle de Données Suggéré :**
  - `utilisateurs` (id, email, password_hash, role: 'stagiaire'/'entreprise'/'admin', created_at)
  - `profils_stagiaires` (user_id, nom, prenom, telephone, cv_path, lettre_motivation_texte)
  - `profils_entreprises` (user_id, nom_entreprise, description, logo_path)
  - `offres_stage` (id, entreprise_id, titre, description, duree_mois, type_contrat_id, niveau_etude, statut: 'pending'/'approved'/'rejected', created_at)
  - `candidatures` (id, stagiaire_id, offre_id, created_at)
  - `types_contrat` (id, libelle) - _Table gérée par l'admin._
- **Sécurité :** Les mots de passe doivent être hachés en utilisant `password_hash()` et vérifiés avec `password_verify()`. Les uploads de fichiers doivent être sécurisés (vérification du type MIME, nom de fichier aléatoire, stockage hors du webroot si possible).
- **Dépendances :** Il est recommandé d'utiliser Composer pour gérer les dépendances PHP.

#### **8. Success Metrics**

- Nombre de candidatures par an.
- Nombre d'entreprises inscrites.
- Temps moyen pour qu'une offre reçoive sa première candidature.

#### **9. Open Questions**

- Toutes les questions initiales ont été répondues. Ce document est considéré comme complet pour lancer le développement de la première version.

---
