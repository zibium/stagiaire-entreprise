<?php

namespace JobBoard\Models;

use PDO;
use PDOException;

/**
 * Modèle ProfilEntreprise
 * Gestion des profils d'entreprise
 */
class ProfilEntreprise
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }
    
    /**
     * Obtenir la connexion à la base de données
     */
    private function getConnection()
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            return $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    /**
     * Créer un nouveau profil entreprise
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO profils_entreprises (
                        user_id, nom_entreprise, secteur_activite, taille_entreprise,
                        description, adresse, ville, code_postal,
                        telephone, site_web, linkedin, contact_nom, contact_prenom,
                        contact_fonction, contact_email, contact_telephone, logo_path
                    ) VALUES (
                        :user_id, :nom_entreprise, :secteur_activite, :taille_entreprise,
                        :description, :adresse, :ville, :code_postal,
                        :telephone, :site_web, :linkedin, :contact_nom, :contact_prenom,
                        :contact_fonction, :contact_email, :contact_telephone, :logo_path
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':nom_entreprise' => $data['nom_entreprise'],
                ':secteur_activite' => $data['secteur_activite'],
                ':taille_entreprise' => $data['taille_entreprise'],
                ':description' => $data['description'] ?? null,
                ':adresse' => $data['adresse'] ?? null,
                ':ville' => $data['ville'] ?? null,
                ':code_postal' => $data['code_postal'] ?? null,
                ':telephone' => $data['telephone'] ?? null,
                ':site_web' => $data['site_web'] ?? null,
                ':linkedin' => $data['linkedin'] ?? null,
                ':contact_nom' => $data['contact_nom'] ?? null,
                ':contact_prenom' => $data['contact_prenom'] ?? null,
                ':contact_fonction' => $data['contact_fonction'] ?? null,
                ':contact_email' => $data['contact_email'] ?? null,
                ':contact_telephone' => $data['contact_telephone'] ?? null,
                ':logo_path' => $data['logo_path'] ?? null
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erreur création profil entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Trouver un profil par user_id
     */
    public function findByUserId($userId)
    {
        try {
            $sql = "SELECT pe.*, u.email, u.role, u.email_verified, u.created_at as user_created_at
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    WHERE pe.user_id = :user_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur recherche profil entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Trouver un profil par ID
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT pe.*, u.email, u.role, u.email_verified
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    WHERE pe.id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur recherche profil entreprise par ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour un profil entreprise
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE profils_entreprises SET
                        nom_entreprise = :nom_entreprise,
                        secteur_activite = :secteur_activite,
                        taille_entreprise = :taille_entreprise,
                        description = :description,
                        adresse = :adresse,
                        ville = :ville,
                        code_postal = :code_postal,
                        telephone = :telephone,
                        site_web = :site_web,
                        linkedin = :linkedin,
                        contact_nom = :contact_nom,
                        contact_prenom = :contact_prenom,
                        contact_fonction = :contact_fonction,
                        contact_email = :contact_email,
                        contact_telephone = :contact_telephone
                    WHERE id = :id";
            
            try {
                $logFile = __DIR__ . '/../../logs/debug.log';
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - SQL: " . $sql . "\n", FILE_APPEND);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - ID: " . $id . "\n", FILE_APPEND);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - Data: " . json_encode($data) . "\n", FILE_APPEND);
            } catch (Exception $logError) {
                // Ignore logging errors
            }
            
            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':id' => $id,
                ':nom_entreprise' => $data['nom_entreprise'],
                ':secteur_activite' => $data['secteur_activite'],
                ':taille_entreprise' => $data['taille_entreprise'],
                ':description' => $data['description'] ?? null,
                ':adresse' => $data['adresse'] ?? null,
                ':ville' => $data['ville'] ?? null,
                ':code_postal' => $data['code_postal'] ?? null,
                ':telephone' => $data['telephone'] ?? null,
                ':site_web' => $data['site_web'] ?? null,
                ':linkedin' => $data['linkedin'] ?? null,
                ':contact_nom' => $data['contact_nom'] ?? null,
                ':contact_prenom' => $data['contact_prenom'] ?? null,
                ':contact_fonction' => $data['contact_fonction'] ?? null,
                ':contact_email' => $data['contact_email'] ?? null,
                ':contact_telephone' => $data['contact_telephone'] ?? null
            ];
            
            try {
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - Params: " . json_encode($params) . "\n", FILE_APPEND);
            } catch (Exception $logError) {}
            $result = $stmt->execute($params);
            try {
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - Execute result: " . ($result ? 'true' : 'false') . "\n", FILE_APPEND);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - ProfilEntreprise::update - Rows affected: " . $stmt->rowCount() . "\n", FILE_APPEND);
            } catch (Exception $logError) {}

            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un profil entreprise
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM profils_entreprises WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression profil entreprise: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir tous les profils avec pagination
     */
    public function getAll($page = 1, $limit = 10, $filters = [])
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Filtres
            if (!empty($filters['secteur'])) {
                $whereClause .= " AND pe.secteur_activite = :secteur";
                $params[':secteur'] = $filters['secteur'];
            }
            
            if (!empty($filters['ville'])) {
                $whereClause .= " AND pe.ville LIKE :ville";
                $params[':ville'] = '%' . $filters['ville'] . '%';
            }
            
            if (!empty($filters['taille'])) {
                $whereClause .= " AND pe.taille_entreprise = :taille";
                $params[':taille'] = $filters['taille'];
            }
            
            $sql = "SELECT pe.*, u.email, u.email_verified
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    {$whereClause}
                    ORDER BY pe.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur récupération profils entreprise: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter le nombre total de profils
     */
    public function count($filters = [])
    {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Appliquer les mêmes filtres que getAll
            if (!empty($filters['secteur'])) {
                $whereClause .= " AND pe.secteur_activite = :secteur";
                $params[':secteur'] = $filters['secteur'];
            }
            
            if (!empty($filters['ville'])) {
                $whereClause .= " AND pe.ville LIKE :ville";
                $params[':ville'] = '%' . $filters['ville'] . '%';
            }
            
            if (!empty($filters['taille'])) {
                $whereClause .= " AND pe.taille_entreprise = :taille";
                $params[':taille'] = $filters['taille'];
            }
            
            $sql = "SELECT COUNT(*) as total
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    {$whereClause}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur comptage profils entreprise: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mettre à jour le chemin du logo
     */
    public function updateLogoPath($id, $logoPath)
    {
        try {
            $sql = "UPDATE profils_entreprises SET logo_path = :logo_path, updated_at = NOW() WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':logo_path' => $logoPath
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour logo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifier si un profil existe pour un utilisateur
     */
    public function existsForUser($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM profils_entreprises WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur vérification existence profil: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valider les données du profil
     */
    public function validateProfileData($data)
    {
        $errors = [];
        
        // Nom entreprise obligatoire
        if (empty($data['nom_entreprise']) || strlen(trim($data['nom_entreprise'])) < 2) {
            $errors[] = "Le nom de l'entreprise est obligatoire (minimum 2 caractères)";
        }
        
        // Secteur d'activité obligatoire
        if (empty($data['secteur_activite'])) {
            $errors[] = "Le secteur d'activité est obligatoire";
        }
        
        // Taille entreprise obligatoire
        if (empty($data['taille_entreprise'])) {
            $errors[] = "La taille de l'entreprise est obligatoire";
        }
        
        // Validation du code postal
        if (!empty($data['code_postal']) && !preg_match('/^[0-9]{5}$/', $data['code_postal'])) {
            $errors[] = "Le code postal doit contenir 5 chiffres";
        }
        
        // Validation du téléphone
        if (!empty($data['telephone']) && !preg_match('/^[0-9+\-\s\.\(\)]{10,}$/', $data['telephone'])) {
            $errors[] = "Le format du téléphone n'est pas valide";
        }
        
        // Validation du site web
        if (!empty($data['site_web']) && !filter_var($data['site_web'], FILTER_VALIDATE_URL)) {
            $errors[] = "L'URL du site web n'est pas valide";
        }
        
        // Validation du SIRET
        if (!empty($data['siret']) && !preg_match('/^[0-9]{14}$/', $data['siret'])) {
            $errors[] = "Le SIRET doit contenir 14 chiffres";
        }
        
        return $errors;
    }
    
    /**
     * Rechercher des entreprises par secteur
     */
    public function searchBySector($sector, $limit = 10)
    {
        try {
            $sql = "SELECT pe.*, u.email
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    WHERE pe.secteur_activite = :sector
                    AND u.email_verified = 1
                    ORDER BY pe.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':sector', $sector, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur recherche par secteur: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les statistiques des profils entreprise
     */
    public function getStatistics()
    {
        try {
            $stats = [];
            
            // Total des profils
            $sql = "SELECT COUNT(*) as total FROM profils_entreprises";
            $stmt = $this->pdo->query($sql);
            $stats['total_profiles'] = $stmt->fetchColumn();
            
            // Profils vérifiés
            $sql = "SELECT COUNT(*) as verified
                    FROM profils_entreprises pe
                    JOIN utilisateurs u ON pe.user_id = u.id
                    WHERE u.email_verified = 1";
            $stmt = $this->pdo->query($sql);
            $stats['verified_profiles'] = $stmt->fetchColumn();
            
            // Répartition par secteur
            $sql = "SELECT secteur_activite, COUNT(*) as count
                    FROM profils_entreprises
                    GROUP BY secteur_activite
                    ORDER BY count DESC
                    LIMIT 10";
            $stmt = $this->pdo->query($sql);
            $stats['sectors'] = $stmt->fetchAll();
            
            // Répartition par taille
            $sql = "SELECT taille_entreprise, COUNT(*) as count
                    FROM profils_entreprises
                    GROUP BY taille_entreprise
                    ORDER BY count DESC";
            $stmt = $this->pdo->query($sql);
            $stats['sizes'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur statistiques profils entreprise: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculer le pourcentage de completion du profil
     */
    public function calculateCompletionPercentage($profile)
    {
        $fields = [
            'nom_entreprise', 'secteur_activite', 'taille_entreprise',
            'description', 'adresse', 'ville', 'code_postal',
            'telephone', 'site_web', 'logo_path'
        ];
        
        $completed = 0;
        $total = count($fields);
        
        foreach ($fields as $field) {
            if (!empty($profile[$field])) {
                $completed++;
            }
        }
        
        return round(($completed / $total) * 100);
    }
    
    /**
     * Obtenir le pourcentage de completion du profil par ID
     */
    public function getCompletionPercentage($profilId)
    {
        try {
            $profile = $this->findById($profilId);
            if (!$profile) {
                return 0;
            }
            return $this->calculateCompletionPercentage($profile);
        } catch (PDOException $e) {
            error_log("Erreur calcul pourcentage completion: " . $e->getMessage());
            return 0;
        }
    }
}