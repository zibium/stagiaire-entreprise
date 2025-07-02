<?php

namespace JobBoard\Models;

use PDO;
use PDOException;
use DateTime;

class ProfilStagiaire
{
    private $pdo;

    public function __construct($pdo = null)
    {
        if ($pdo) {
            $this->pdo = $pdo;
        } else {
            $this->pdo = self::getConnection();
        }
    }

    private static function getConnection()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    /**
     * Créer un nouveau profil stagiaire (méthode d'instance)
     */
    public function create($data)
    {
        // Validation des données
        if (!self::validateProfileData($data)) {
            return false;
        }

        $sql = "INSERT INTO profils_stagiaires (
                    user_id, nom, prenom, telephone, cv_path,
                    lettre_motivation_texte, niveau_etude, email,
                    domaine_etude, ville, code_postal, created_at, updated_at
                ) VALUES (
                    :user_id, :nom, :prenom, :telephone, :cv_path,
                    :lettre_motivation_texte, :niveau_etude, :email,
                    :domaine_etude, :ville, :code_postal, NOW(), NOW()
                )";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $data['user_id'],
                ':nom' => $data['nom'],
                ':prenom' => $data['prenom'],
                ':telephone' => $data['telephone'] ?? null,
                ':cv_path' => $data['cv_path'] ?? null,
                ':lettre_motivation_texte' => $data['lettre_motivation'] ?? null,
                ':niveau_etude' => $data['niveau_etude'] ?? null,
                ':email' => $data['email'] ?? null,
                ':domaine_etude' => $data['domaine_etude'] ?? null,
                ':ville' => $data['ville'] ?? null,
                ':code_postal' => $data['code_postal'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un nouveau profil stagiaire (méthode statique pour compatibilité)
     */
    public static function createStatic($data)
    {
        $instance = new self();
        return $instance->create($data);
    }

    /**
     * Trouver un profil par ID utilisateur (méthode d'instance)
     */
    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM profils_stagiaires WHERE user_id = :user_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Trouver un profil par ID utilisateur (méthode statique pour compatibilité)
     */
    public static function findByUserIdStatic($userId)
    {
        $instance = new self();
        return $instance->findByUserId($userId);
    }

    /**
     * Récupérer un profil par ID
     */
    public static function findById($id)
    {
        $pdo = self::getConnection();

        $sql = "SELECT ps.*, u.email, u.role
                FROM profils_stagiaires ps
                JOIN users u ON ps.user_id = u.id
                WHERE ps.id = :id";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un profil stagiaire (méthode d'instance)
     */
    public function update($id, $data)
    {
        // Validation des données
        if (!self::validateProfileData($data, false)) {
            return false;
        }

        $sql = "UPDATE profils_stagiaires SET
                    nom = :nom, prenom = :prenom, telephone = :telephone,
                    cv_path = :cv_path, lettre_motivation_texte = :lettre_motivation_texte,
                    niveau_etude = :niveau_etude, email = :email,
                    domaine_etude = :domaine_etude, ville = :ville,
                    code_postal = :code_postal, updated_at = NOW()
                WHERE id = :id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nom' => $data['nom'],
                ':prenom' => $data['prenom'],
                ':telephone' => $data['telephone'] ?? null,
                ':cv_path' => $data['cv_path'] ?? null,
                ':lettre_motivation_texte' => $data['lettre_motivation'] ?? null,
                ':niveau_etude' => $data['niveau_etude'] ?? null,
                ':email' => $data['email'] ?? null,
                ':domaine_etude' => $data['domaine_etude'] ?? null,
                ':ville' => $data['ville'] ?? null,
                ':code_postal' => $data['code_postal'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un profil stagiaire (méthode statique pour compatibilité)
     */
    public static function updateStatic($id, $data)
    {
        $instance = new self();
        return $instance->update($id, $data);
    }

    /**
     * Supprimer un profil stagiaire
     */
    public static function delete($id)
    {
        $pdo = self::getConnection();

        $sql = "DELETE FROM profils_stagiaires WHERE id = :id";

        try {
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer tous les profils stagiaires avec pagination
     */
    public static function getAll($page = 1, $limit = 20, $filters = [])
    {
        $pdo = self::getConnection();
        $offset = ($page - 1) * $limit;

        $whereClause = "WHERE 1=1";
        $params = [];

        // Filtres
        if (!empty($filters['ville'])) {
            $whereClause .= " AND ps.ville LIKE :ville";
            $params[':ville'] = '%' . $filters['ville'] . '%';
        }

        if (!empty($filters['niveau_etudes'])) {
            $whereClause .= " AND ps.niveau_etudes = :niveau_etudes";
            $params[':niveau_etudes'] = $filters['niveau_etudes'];
        }

        if (!empty($filters['domaine_etudes'])) {
            $whereClause .= " AND ps.domaine_etudes LIKE :domaine_etudes";
            $params[':domaine_etudes'] = '%' . $filters['domaine_etudes'] . '%';
        }

        $sql = "SELECT ps.*, u.email
                FROM profils_stagiaires ps
                JOIN users u ON ps.user_id = u.id
                {$whereClause}
                ORDER BY ps.created_at DESC
                LIMIT :limit OFFSET :offset";

        try {
            $stmt = $pdo->prepare($sql);
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des profils: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter le nombre total de profils
     */
    public static function count($filters = [])
    {
        $pdo = self::getConnection();

        $whereClause = "WHERE 1=1";
        $params = [];

        // Appliquer les mêmes filtres que getAll
        if (!empty($filters['ville'])) {
            $whereClause .= " AND ps.ville LIKE :ville";
            $params[':ville'] = '%' . $filters['ville'] . '%';
        }

        if (!empty($filters['niveau_etudes'])) {
            $whereClause .= " AND ps.niveau_etudes = :niveau_etudes";
            $params[':niveau_etudes'] = $filters['niveau_etudes'];
        }

        if (!empty($filters['domaine_etudes'])) {
            $whereClause .= " AND ps.domaine_etudes LIKE :domaine_etudes";
            $params[':domaine_etudes'] = '%' . $filters['domaine_etudes'] . '%';
        }

        $sql = "SELECT COUNT(*) as total
                FROM profils_stagiaires ps
                JOIN users u ON ps.user_id = u.id
                {$whereClause}";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des profils: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mettre à jour le chemin du CV
     */
    public static function updateCvPath($userId, $cvPath)
    {
        $pdo = self::getConnection();

        $sql = "UPDATE profils_stagiaires SET cv_path = :cv_path, updated_at = NOW() WHERE user_id = :user_id";

        try {
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':cv_path' => $cvPath,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du CV: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un profil existe pour un utilisateur
     */
    public static function existsForUser($userId)
    {
        $pdo = self::getConnection();

        $sql = "SELECT COUNT(*) as count FROM profils_stagiaires WHERE user_id = :user_id";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du profil: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validation des données du profil
     */
    private static function validateProfileData($data, $isCreation = true)
    {
        // Validation des champs obligatoires
        if ($isCreation && empty($data['user_id'])) {
            return false;
        }

        if (empty($data['nom']) || empty($data['prenom'])) {
            return false;
        }

        // Validation du nom et prénom (lettres, espaces, tirets)
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['nom']) ||
            !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['prenom'])) {
            return false;
        }

        // Validation de la date de naissance
        if (!empty($data['date_naissance'])) {
            $date = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
            if (!$date || $date->format('Y-m-d') !== $data['date_naissance']) {
                return false;
            }

            // Vérifier que l'âge est raisonnable (entre 16 et 65 ans)
            $age = (new DateTime())->diff($date)->y;
            if ($age < 16 || $age > 65) {
                return false;
            }
        }

        // Validation du téléphone
        if (!empty($data['telephone'])) {
            if (!preg_match('/^[0-9+\s\-\.\(\)]+$/', $data['telephone'])) {
                return false;
            }
        }

        // Validation du code postal
        if (!empty($data['code_postal'])) {
            if (!preg_match('/^[0-9]{5}$/', $data['code_postal'])) {
                return false;
            }
        }

        // Validation des URLs
        if (!empty($data['linkedin_url'])) {
            if (!filter_var($data['linkedin_url'], FILTER_VALIDATE_URL)) {
                return false;
            }
        }

        if (!empty($data['portfolio_url'])) {
            if (!filter_var($data['portfolio_url'], FILTER_VALIDATE_URL)) {
                return false;
            }
        }

        // Validation des dates de disponibilité
        if (!empty($data['disponibilite_debut']) && !empty($data['disponibilite_fin'])) {
            $debut = new DateTime($data['disponibilite_debut']);
            $fin = new DateTime($data['disponibilite_fin']);

            if ($debut >= $fin) {
                return false;
            }
        }

        return true;
    }

    /**
     * Rechercher des profils par compétences
     */
    public static function searchByCompetences($competences, $limit = 10)
    {
        $pdo = self::getConnection();

        $sql = "SELECT ps.*, u.email
                FROM profils_stagiaires ps
                JOIN users u ON ps.user_id = u.id
                WHERE ps.competences LIKE :competences
                ORDER BY ps.updated_at DESC
                LIMIT :limit";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':competences' => '%' . $competences . '%',
                ':limit' => $limit
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche par compétences: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les statistiques des profils
     */
    public static function getStatistics()
    {
        $pdo = self::getConnection();

        $sql = "SELECT
                    COUNT(*) as total_profils,
                    COUNT(CASE WHEN cv_path IS NOT NULL THEN 1 END) as avec_cv,
                    COUNT(CASE WHEN niveau_etudes = 'Bac+2' THEN 1 END) as bac_plus_2,
                    COUNT(CASE WHEN niveau_etudes = 'Bac+3' THEN 1 END) as bac_plus_3,
                    COUNT(CASE WHEN niveau_etudes = 'Bac+5' THEN 1 END) as bac_plus_5,
                    COUNT(CASE WHEN permis_conduire = 1 THEN 1 END) as avec_permis
                FROM profils_stagiaires";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            return [];
        }
    }
}