-- Script de création de la base de données JobBoard
-- Plateforme de stages

CREATE DATABASE IF NOT EXISTS jobboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobboard;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('stagiaire', 'entreprise', 'admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des profils stagiaires
CREATE TABLE profils_stagiaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    cv_path VARCHAR(255),
    lettre_motivation_texte TEXT,
    niveau_etude VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table des profils entreprises
CREATE TABLE profils_entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom_entreprise VARCHAR(255) NOT NULL,
    description TEXT,
    logo_path VARCHAR(255),
    secteur_activite VARCHAR(100),
    taille_entreprise VARCHAR(50),
    adresse TEXT,
    site_web VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Table des types de contrat
CREATE TABLE types_contrat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des types de contrat par défaut
INSERT INTO types_contrat (libelle, description) VALUES
('Stage académique', 'Stage dans le cadre d\'un cursus scolaire'),
('Stage professionnel', 'Stage d\'insertion professionnelle'),
('Alternance', 'Contrat d\'alternance'),
('Stage de vacances', 'Stage pendant les vacances scolaires');

-- Table des offres de stage
CREATE TABLE offres_stage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    duree_mois INT NOT NULL,
    type_contrat_id INT NOT NULL,
    niveau_etude VARCHAR(100),
    competences_requises TEXT,
    remuneration VARCHAR(100),
    lieu VARCHAR(255),
    statut ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    date_debut DATE,
    date_fin DATE,
    nb_candidatures INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (entreprise_id) REFERENCES profils_entreprises(id) ON DELETE CASCADE,
    FOREIGN KEY (type_contrat_id) REFERENCES types_contrat(id)
);

-- Table des candidatures
CREATE TABLE candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    offre_id INT NOT NULL,
    lettre_motivation TEXT,
    cv_path VARCHAR(255),
    statut ENUM('en_attente', 'acceptee', 'refusee', 'entretien') DEFAULT 'en_attente',
    commentaire_entreprise TEXT,
    date_candidature TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reponse TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stagiaire_id) REFERENCES profils_stagiaires(id) ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES offres_stage(id) ON DELETE CASCADE,
    UNIQUE KEY unique_candidature (stagiaire_id, offre_id)
);

-- Ajout de colonnes manquantes aux profils stagiaires
ALTER TABLE profils_stagiaires 
ADD COLUMN email VARCHAR(255),
ADD COLUMN domaine_etude VARCHAR(100),
ADD COLUMN ville VARCHAR(100),
ADD COLUMN code_postal VARCHAR(10);

-- Ajout de colonnes manquantes aux profils entreprises
ALTER TABLE profils_entreprises 
ADD COLUMN ville VARCHAR(100),
ADD COLUMN code_postal VARCHAR(10),
ADD COLUMN telephone VARCHAR(20),
ADD COLUMN linkedin VARCHAR(255),
ADD COLUMN contact_nom VARCHAR(100),
ADD COLUMN contact_prenom VARCHAR(100),
ADD COLUMN contact_fonction VARCHAR(100),
ADD COLUMN contact_email VARCHAR(255),
ADD COLUMN contact_telephone VARCHAR(20);

-- Ajout de colonnes manquantes aux offres de stage
ALTER TABLE offres_stage 
ADD COLUMN ville VARCHAR(100),
ADD COLUMN code_postal VARCHAR(10),
ADD COLUMN domaine VARCHAR(100),
ADD COLUMN date_limite_candidature DATE;

-- Table des logs d'activité
CREATE TABLE logs_activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- Index pour optimiser les performances
CREATE INDEX idx_utilisateurs_email ON utilisateurs(email);
CREATE INDEX idx_utilisateurs_role ON utilisateurs(role);
CREATE INDEX idx_offres_statut ON offres_stage(statut);
CREATE INDEX idx_offres_entreprise ON offres_stage(entreprise_id);
CREATE INDEX idx_candidatures_stagiaire ON candidatures(stagiaire_id);
CREATE INDEX idx_candidatures_offre ON candidatures(offre_id);
CREATE INDEX idx_logs_user ON logs_activite(user_id);
CREATE INDEX idx_logs_created ON logs_activite(created_at);

-- Création de l'utilisateur administrateur par défaut
INSERT INTO utilisateurs (email, password_hash, role) VALUES
('admin@jobboard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Mot de passe par défaut: 'password' (à changer en production)