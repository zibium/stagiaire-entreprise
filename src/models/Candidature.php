<?php

namespace JobBoard\Models;

use PDO;
use PDOException;

/**
 * Modèle Candidature
 * Gestion des candidatures aux offres de stage
 */
class Candidature
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

    public function countByUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM candidatures WHERE stagiaire_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new \Exception("Erreur de comptage: " . $e->getMessage());
        }
    }

    public function countByStatus($userId, $status)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM candidatures WHERE stagiaire_id = ? AND statut = ?");
            $stmt->execute([$userId, $status]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new \Exception("Erreur de comptage: " . $e->getMessage());
        }
    }

    /**
     * Créer une nouvelle candidature
     */
    public function create($data)
    {
        try {
            // Vérifier si l'utilisateur a déjà postulé à cette offre
            if ($this->hasApplied($data['stagiaire_id'], $data['offre_id'])) {
                return ['success' => false, 'message' => 'Vous avez déjà postulé à cette offre'];
            }
            
            $sql = "INSERT INTO candidatures (
                        stagiaire_id, offre_id, lettre_motivation, cv_path,
                        statut, date_candidature
                    ) VALUES (
                        :stagiaire_id, :offre_id, :lettre_motivation, :cv_path,
                        :statut, NOW()
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':stagiaire_id' => $data['stagiaire_id'],
                ':offre_id' => $data['offre_id'],
                ':lettre_motivation' => $data['lettre_motivation'] ?? null,
                ':cv_path' => $data['cv_path'] ?? null,
                ':statut' => 'en_attente'
            ]);
            
            if ($result) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Erreur lors de la création de la candidature'];
        } catch (PDOException $e) {
            error_log("Erreur création candidature: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique lors de la candidature'];
        }
    }
    
    /**
     * Vérifier si un stagiaire a déjà postulé à une offre
     */
    public function hasApplied($stagiaire_id, $offre_id)
    {
        try {
            $sql = "SELECT COUNT(*) FROM candidatures 
                    WHERE stagiaire_id = :stagiaire_id AND offre_id = :offre_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':stagiaire_id' => $stagiaire_id,
                ':offre_id' => $offre_id
            ]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur vérification candidature: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les candidatures d'un stagiaire
     */
    public function getByStagiaire($stagiaire_id, $limit = null, $offset = 0)
    {
        try {
            $sql = "SELECT c.*, os.titre, os.ville, os.type_contrat, os.duree,
                           pe.nom_entreprise, pe.logo_path,
                           c.date_candidature, c.statut
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    WHERE c.stagiaire_id = :stagiaire_id
                    ORDER BY c.date_candidature DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':stagiaire_id', $stagiaire_id, PDO::PARAM_INT);
            
            if ($limit) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur récupération candidatures stagiaire: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les candidatures pour une offre
     */
    public function getByOffre($offre_id, $limit = null, $offset = 0)
    {
        try {
            $sql = "SELECT c.*, ps.prenom, ps.nom, ps.email, ps.telephone,
                           ps.cv_path, ps.niveau_etude, ps.domaine_etude,
                           u.email as user_email
                    FROM candidatures c
                    JOIN profils_stagiaire ps ON c.stagiaire_id = ps.id
                    JOIN users u ON ps.user_id = u.id
                    WHERE c.offre_id = :offre_id
                    ORDER BY c.date_candidature DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':offre_id', $offre_id, PDO::PARAM_INT);
            
            if ($limit) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur récupération candidatures offre: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mettre à jour le statut d'une candidature
     */
    public function updateStatus($id, $statut, $commentaire = null)
    {
        try {
            $sql = "UPDATE candidatures 
                    SET statut = :statut, commentaire_entreprise = :commentaire,
                        date_reponse = NOW()
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':statut' => $statut,
                ':commentaire' => $commentaire
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour statut candidature: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir une candidature par ID
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT c.*, os.titre, os.description, os.ville, os.type_contrat,
                           pe.nom_entreprise, pe.logo_path,
                           ps.prenom, ps.nom, ps.email, ps.telephone
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    JOIN profils_stagiaire ps ON c.stagiaire_id = ps.id
                    WHERE c.id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur récupération candidature: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Supprimer une candidature
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM candidatures WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression candidature: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les statistiques des candidatures
     */
    public function getStatistics($stagiaire_id = null, $entreprise_id = null)
    {
        try {
            $where = [];
            $params = [];
            
            if ($stagiaire_id) {
                $where[] = "c.stagiaire_id = :stagiaire_id";
                $params[':stagiaire_id'] = $stagiaire_id;
            }
            
            if ($entreprise_id) {
                $where[] = "os.entreprise_id = :entreprise_id";
                $params[':entreprise_id'] = $entreprise_id;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN c.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                        SUM(CASE WHEN c.statut = 'acceptee' THEN 1 ELSE 0 END) as acceptees,
                        SUM(CASE WHEN c.statut = 'refusee' THEN 1 ELSE 0 END) as refusees,
                        SUM(CASE WHEN c.statut = 'entretien' THEN 1 ELSE 0 END) as entretiens
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    {$whereClause}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur statistiques candidatures: " . $e->getMessage());
            return [
                'total' => 0,
                'en_attente' => 0,
                'acceptees' => 0,
                'refusees' => 0,
                'entretiens' => 0
            ];
        }
    }
    
    /**
     * Obtenir les candidatures récentes
     */
    public function getRecent($limit = 10)
    {
        try {
            $sql = "SELECT c.*, os.titre, pe.nom_entreprise,
                           ps.prenom, ps.nom
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    JOIN profils_stagiaire ps ON c.stagiaire_id = ps.id
                    ORDER BY c.date_candidature DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur candidatures récentes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les statistiques des candidatures pour un utilisateur
     */
    public static function getStatistiquesByUser($userId)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            $sql = "SELECT 
                        COUNT(*) as total_candidatures,
                        SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                        SUM(CASE WHEN statut = 'acceptee' THEN 1 ELSE 0 END) as acceptees,
                        SUM(CASE WHEN statut = 'refusee' THEN 1 ELSE 0 END) as refusees
                    FROM candidatures 
                    WHERE stagiaire_id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur statistiques candidatures: " . $e->getMessage());
            return [
                'total_candidatures' => 0,
                'en_attente' => 0,
                'acceptees' => 0,
                'refusees' => 0
            ];
        }
    }

    public static function updateMotivation(int $userId, int $candidatureId, string $motivation): bool
    {
        $db = self::getDb();
        $stmt = $db->prepare("UPDATE candidatures SET motivation = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$motivation, $candidatureId, $userId]);
    }



    private static function sendStatusNotification(int $candidatureId, string $newStatus): void
    {
        $candidature = self::getById($candidatureId);
        $user = User::getById($candidature['user_id']);
        
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->setFrom($_ENV['EMAIL_FROM']);
        $mail->addAddress($user['email']);
        
        $mail->Subject = "Mise à jour de votre candidature";
        $mail->Body = "Votre candidature n°{$candidatureId} a été mise à jour : {$newStatus}";
        
        $mail->send();
    }

    public static function getById(int $id): ?array
    {
        $db = self::getDb();
        $stmt = $db->prepare("SELECT * FROM candidatures WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Obtenir les candidatures récentes d'une entreprise
     */
    public function getRecentByEntreprise($entrepriseId, $limit = 5)
    {
        try {
            $sql = "SELECT c.*, os.titre as offre_titre, ps.nom, ps.prenom
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    JOIN profils_stagiaire ps ON c.stagiaire_id = ps.id
                    WHERE os.entreprise_id = :entreprise_id
                    ORDER BY c.date_candidature DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':entreprise_id', $entrepriseId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur candidatures récentes par entreprise: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les candidatures d'une entreprise avec filtres
     */
    public function findByEntreprise($entrepriseId, $filters = [], $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ["os.entreprise_id = :entreprise_id"];
            $params = [':entreprise_id' => $entrepriseId];
            
            // Ajouter les filtres
            if (!empty($filters['statut'])) {
                $whereConditions[] = "c.statut = :statut";
                $params[':statut'] = $filters['statut'];
            }
            
            if (!empty($filters['offre_id'])) {
                $whereConditions[] = "c.offre_id = :offre_id";
                $params[':offre_id'] = $filters['offre_id'];
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $sql = "SELECT c.*, os.titre as offre_titre, ps.nom, ps.prenom, ps.email
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    JOIN profils_stagiaire ps ON c.stagiaire_id = ps.id
                    WHERE {$whereClause}
                    ORDER BY c.date_candidature DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur candidatures par entreprise: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les candidatures d'une entreprise
     */
    public function countByEntreprise($entrepriseId, $filters = [])
    {
        try {
            $whereConditions = ["os.entreprise_id = :entreprise_id"];
            $params = [':entreprise_id' => $entrepriseId];
            
            // Ajouter les filtres
            if (!empty($filters['statut'])) {
                $whereConditions[] = "c.statut = :statut";
                $params[':statut'] = $filters['statut'];
            }
            
            if (!empty($filters['offre_id'])) {
                $whereConditions[] = "c.offre_id = :offre_id";
                $params[':offre_id'] = $filters['offre_id'];
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $sql = "SELECT COUNT(*) 
                    FROM candidatures c
                    JOIN offres_stage os ON c.offre_id = os.id
                    WHERE {$whereClause}";
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur comptage candidatures par entreprise: " . $e->getMessage());
            return 0;
        }
    }
}