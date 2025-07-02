<?php

namespace JobBoard\Models;

use PDO;
use PDOException;

/**
 * Modèle OffreStage
 * Gestion des offres de stage
 */
class OffreStage
{
    private $pdo;
    
    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    public static function rechercher(string $motsCles, string $domaine): array
    {
        $db = Database::getInstance();
        $query = "SELECT * FROM offres_stage 
                 WHERE (titre LIKE :motsCles OR description LIKE :motsCles)
                 AND domaine = :domaine";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':motsCles' => "%$motsCles%",
            ':domaine' => $domaine
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     * Créer une nouvelle offre de stage
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO offres_stage (
                        entreprise_id, titre, description, competences_requises,
                        type_contrat, duree, remuneration, lieu, ville,
                        code_postal, date_debut, date_fin, date_limite_candidature,
                        niveau_etude, domaine, statut, date_creation
                    ) VALUES (
                        :entreprise_id, :titre, :description, :competences_requises,
                        :type_contrat, :duree, :remuneration, :lieu, :ville,
                        :code_postal, :date_debut, :date_fin, :date_limite_candidature,
                        :niveau_etude, :domaine, :statut, NOW()
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':entreprise_id' => $data['entreprise_id'],
                ':titre' => $data['titre'],
                ':description' => $data['description'],
                ':competences_requises' => $data['competences_requises'] ?? null,
                ':type_contrat' => $data['type_contrat'],
                ':duree' => $data['duree'],
                ':remuneration' => $data['remuneration'] ?? null,
                ':lieu' => $data['lieu'] ?? null,
                ':ville' => $data['ville'],
                ':code_postal' => $data['code_postal'] ?? null,
                ':date_debut' => $data['date_debut'],
                ':date_fin' => $data['date_fin'] ?? null,
                ':date_limite_candidature' => $data['date_limite_candidature'],
                ':niveau_etude' => $data['niveau_etude'],
                ':domaine' => $data['domaine'],
                ':statut' => $data['statut'] ?? 'en_attente'
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erreur création offre de stage: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Trouver une offre par ID
     */
    public function findById($id)
    {
        try {
            $sql = "SELECT os.*, pe.nom_entreprise, pe.secteur_activite, pe.logo_path,
                           pe.ville as entreprise_ville, pe.site_web
                    FROM offres_stage os
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    WHERE os.id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erreur recherche offre par ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir les offres d'une entreprise
     */
    public function findByEntreprise($entrepriseId, $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT os.*, pe.nom_entreprise
                    FROM offres_stage os
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    WHERE os.entreprise_id = :entreprise_id
                    ORDER BY os.date_creation DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':entreprise_id', $entrepriseId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur recherche offres par entreprise: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mettre à jour une offre
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE offres_stage SET
                        titre = :titre,
                        description = :description,
                        competences_requises = :competences_requises,
                        type_contrat = :type_contrat,
                        duree = :duree,
                        remuneration = :remuneration,
                        lieu = :lieu,
                        ville = :ville,
                        code_postal = :code_postal,
                        date_debut = :date_debut,
                        date_fin = :date_fin,
                        date_limite_candidature = :date_limite_candidature,
                        niveau_etude = :niveau_etude,
                        domaine = :domaine,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':titre' => $data['titre'],
                ':description' => $data['description'],
                ':competences_requises' => $data['competences_requises'] ?? null,
                ':type_contrat' => $data['type_contrat'],
                ':duree' => $data['duree'],
                ':remuneration' => $data['remuneration'] ?? null,
                ':lieu' => $data['lieu'] ?? null,
                ':ville' => $data['ville'],
                ':code_postal' => $data['code_postal'] ?? null,
                ':date_debut' => $data['date_debut'],
                ':date_fin' => $data['date_fin'] ?? null,
                ':date_limite_candidature' => $data['date_limite_candidature'],
                ':niveau_etude' => $data['niveau_etude'],
                ':domaine' => $data['domaine']
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer une offre
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM offres_stage WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erreur suppression offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Changer le statut d'une offre
     */
    public function updateStatus($id, $status)
    {
        try {
            $sql = "UPDATE offres_stage SET statut = :statut, updated_at = NOW() WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':statut' => $status
            ]);
        } catch (PDOException $e) {
            error_log("Erreur mise à jour statut offre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rechercher des offres avec filtres
     */
    public function search($filters = [], $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE os.statut = 'publiee'";
            $params = [];
            
            // Filtres de recherche
            if (!empty($filters['keywords'])) {
                $whereClause .= " AND (os.titre LIKE :keywords OR os.description LIKE :keywords OR os.competences_requises LIKE :keywords)";
                $params[':keywords'] = '%' . $filters['keywords'] . '%';
            }
            
            if (!empty($filters['ville'])) {
                $whereClause .= " AND os.ville LIKE :ville";
                $params[':ville'] = '%' . $filters['ville'] . '%';
            }
            
            if (!empty($filters['domaine'])) {
                $whereClause .= " AND os.domaine = :domaine";
                $params[':domaine'] = $filters['domaine'];
            }
            
            if (!empty($filters['type_contrat'])) {
                $whereClause .= " AND os.type_contrat = :type_contrat";
                $params[':type_contrat'] = $filters['type_contrat'];
            }
            
            if (!empty($filters['niveau_etude'])) {
                $whereClause .= " AND os.niveau_etude = :niveau_etude";
                $params[':niveau_etude'] = $filters['niveau_etude'];
            }
            
            if (!empty($filters['remuneration'])) {
                if ($filters['remuneration'] === 'oui') {
                    $whereClause .= " AND os.remuneration IS NOT NULL AND os.remuneration > 0";
                } elseif ($filters['remuneration'] === 'non') {
                    $whereClause .= " AND (os.remuneration IS NULL OR os.remuneration = 0)";
                }
            }
            
            // Filtrer par date limite de candidature
            $whereClause .= " AND os.date_limite_candidature >= CURDATE()";
            
            $sql = "SELECT os.*, pe.nom_entreprise, pe.secteur_activite, pe.logo_path,
                           pe.ville as entreprise_ville
                    FROM offres_stage os
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    {$whereClause}
                    ORDER BY os.date_creation DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->pdo->prepare($sql);
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur recherche offres: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les résultats de recherche
     */
    public function countSearch($filters = [])
    {
        try {
            $whereClause = "WHERE os.statut = 'publiee'";
            $params = [];
            
            // Appliquer les mêmes filtres que search
            if (!empty($filters['keywords'])) {
                $whereClause .= " AND (os.titre LIKE :keywords OR os.description LIKE :keywords OR os.competences_requises LIKE :keywords)";
                $params[':keywords'] = '%' . $filters['keywords'] . '%';
            }
            
            if (!empty($filters['ville'])) {
                $whereClause .= " AND os.ville LIKE :ville";
                $params[':ville'] = '%' . $filters['ville'] . '%';
            }
            
            if (!empty($filters['domaine'])) {
                $whereClause .= " AND os.domaine = :domaine";
                $params[':domaine'] = $filters['domaine'];
            }
            
            if (!empty($filters['type_contrat'])) {
                $whereClause .= " AND os.type_contrat = :type_contrat";
                $params[':type_contrat'] = $filters['type_contrat'];
            }
            
            if (!empty($filters['niveau_etude'])) {
                $whereClause .= " AND os.niveau_etude = :niveau_etude";
                $params[':niveau_etude'] = $filters['niveau_etude'];
            }
            
            if (!empty($filters['remuneration'])) {
                if ($filters['remuneration'] === 'oui') {
                    $whereClause .= " AND os.remuneration IS NOT NULL AND os.remuneration > 0";
                } elseif ($filters['remuneration'] === 'non') {
                    $whereClause .= " AND (os.remuneration IS NULL OR os.remuneration = 0)";
                }
            }
            
            $whereClause .= " AND os.date_limite_candidature >= CURDATE()";
            
            $sql = "SELECT COUNT(*) as total
                    FROM offres_stage os
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    {$whereClause}";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erreur comptage recherche offres: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtenir les offres récentes
     */
    public function getRecent($limit = 5)
    {
        try {
            $sql = "SELECT os.*, pe.nom_entreprise, pe.logo_path
                    FROM offres_stage os
                    JOIN profils_entreprises pe ON os.entreprise_id = pe.id
                    WHERE os.statut = 'publiee'
                    AND os.date_limite_candidature >= CURDATE()
                    ORDER BY os.date_creation DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur récupération offres récentes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Valider les données d'une offre
     */
    public function validateOfferData($data)
    {
        $errors = [];
        
        // Titre obligatoire
        if (empty($data['titre']) || strlen(trim($data['titre'])) < 5) {
            $errors[] = "Le titre est obligatoire (minimum 5 caractères)";
        }
        
        // Description obligatoire
        if (empty($data['description']) || strlen(trim($data['description'])) < 50) {
            $errors[] = "La description est obligatoire (minimum 50 caractères)";
        }
        
        // Type de contrat obligatoire
        $typesContrat = ['stage', 'apprentissage', 'alternance'];
        if (empty($data['type_contrat']) || !in_array($data['type_contrat'], $typesContrat)) {
            $errors[] = "Le type de contrat est obligatoire";
        }
        
        // Durée obligatoire
        if (empty($data['duree']) || !is_numeric($data['duree']) || $data['duree'] < 1) {
            $errors[] = "La durée est obligatoire (en semaines, minimum 1)";
        }
        
        // Ville obligatoire
        if (empty($data['ville']) || strlen(trim($data['ville'])) < 2) {
            $errors[] = "La ville est obligatoire";
        }
        
        // Date de début obligatoire
        if (empty($data['date_debut'])) {
            $errors[] = "La date de début est obligatoire";
        } elseif (strtotime($data['date_debut']) < time()) {
            $errors[] = "La date de début doit être dans le futur";
        }
        
        // Date limite de candidature obligatoire
        if (empty($data['date_limite_candidature'])) {
            $errors[] = "La date limite de candidature est obligatoire";
        } elseif (strtotime($data['date_limite_candidature']) < time()) {
            $errors[] = "La date limite de candidature doit être dans le futur";
        } elseif (!empty($data['date_debut']) && strtotime($data['date_limite_candidature']) >= strtotime($data['date_debut'])) {
            $errors[] = "La date limite de candidature doit être antérieure à la date de début";
        }
        
        // Niveau d'étude obligatoire
        $niveauxEtude = ['bac', 'bac+1', 'bac+2', 'bac+3', 'bac+4', 'bac+5', 'bac+6'];
        if (empty($data['niveau_etude']) || !in_array($data['niveau_etude'], $niveauxEtude)) {
            $errors[] = "Le niveau d'étude est obligatoire";
        }
        
        // Domaine obligatoire
        if (empty($data['domaine'])) {
            $errors[] = "Le domaine est obligatoire";
        }
        
        // Validation du code postal
        if (!empty($data['code_postal']) && !preg_match('/^[0-9]{5}$/', $data['code_postal'])) {
            $errors[] = "Le code postal doit contenir 5 chiffres";
        }
        
        // Validation de la rémunération
        if (!empty($data['remuneration']) && (!is_numeric($data['remuneration']) || $data['remuneration'] < 0)) {
            $errors[] = "La rémunération doit être un nombre positif";
        }
        
        return $errors;
    }
    
    /**
     * Obtenir les statistiques des offres
     */
    public function getStatistics($entrepriseId = null)
    {
        try {
            $stats = [];
            $whereClause = $entrepriseId ? "WHERE entreprise_id = :entreprise_id" : "";
            $params = $entrepriseId ? [':entreprise_id' => $entrepriseId] : [];
            
            // Total des offres
            $sql = "SELECT COUNT(*) as total FROM offres_stage {$whereClause}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $stats['total_offers'] = $stmt->fetchColumn();
            
            // Offres publiées
            $wherePubliee = $whereClause ? $whereClause . " AND statut = 'publiee'" : "WHERE statut = 'publiee'";
            $sql = "SELECT COUNT(*) as published FROM offres_stage {$wherePubliee}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $stats['published_offers'] = $stmt->fetchColumn();
            
            // Offres actives (non expirées)
            $whereActive = $wherePubliee . " AND date_limite_candidature >= CURDATE()";
            $sql = "SELECT COUNT(*) as active FROM offres_stage {$whereActive}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $stats['active_offers'] = $stmt->fetchColumn();
            
            // Répartition par domaine
            $sql = "SELECT domaine, COUNT(*) as count
                    FROM offres_stage
                    {$whereClause}
                    GROUP BY domaine
                    ORDER BY count DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $stats['domains'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur statistiques offres: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les offres expirant bientôt
     */
    public function getExpiringSoon($entrepriseId, $days = 7)
    {
        try {
            $sql = "SELECT *
                    FROM offres_stage
                    WHERE entreprise_id = :entreprise_id
                    AND statut = 'publiee'
                    AND date_limite_candidature BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                    ORDER BY date_limite_candidature ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':entreprise_id' => $entrepriseId,
                ':days' => $days
            ]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur offres expirant bientôt: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir la liste des domaines distincts
     */
    public function getDistinctDomaines()
    {
        try {
            $sql = "SELECT DISTINCT domaine 
                    FROM offres_stage 
                    WHERE statut = 'publiee' 
                    AND domaine IS NOT NULL 
                    AND domaine != '' 
                    ORDER BY domaine";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return array_column($stmt->fetchAll(), 'domaine');
        } catch (PDOException $e) {
            error_log("Erreur récupération domaines: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir la liste des villes distinctes
     */
    public function getDistinctVilles()
    {
        try {
            $sql = "SELECT DISTINCT ville 
                    FROM offres_stage 
                    WHERE statut = 'publiee' 
                    AND ville IS NOT NULL 
                    AND ville != '' 
                    ORDER BY ville";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return array_column($stmt->fetchAll(), 'ville');
        } catch (PDOException $e) {
            error_log("Erreur récupération villes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les offres d'une entreprise
     */
    public function countByEntreprise($entrepriseId)
    {
        try {
            $sql = "SELECT COUNT(*) FROM offres_stage WHERE entreprise_id = :entreprise_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':entreprise_id' => $entrepriseId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur comptage offres par entreprise: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtenir la liste des types de contrat distincts
     */
    public function getDistinctTypesContrat()
    {
        try {
            $sql = "SELECT DISTINCT type_contrat 
                    FROM offres_stage 
                    WHERE statut = 'publiee' 
                    AND type_contrat IS NOT NULL 
                    AND type_contrat != '' 
                    ORDER BY type_contrat";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return array_column($stmt->fetchAll(), 'type_contrat');
        } catch (PDOException $e) {
            error_log("Erreur récupération types contrat: " . $e->getMessage());
            return [];
        }
    }
}