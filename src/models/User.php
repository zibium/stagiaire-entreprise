<?php
/**
 * Modèle User - Gestion des utilisateurs
 * JobBoard - Plateforme de stages
 */

namespace JobBoard\Models;

use PDO;
use Exception;

class User
{
    private $pdo;
    public $email_verified = 0;
    public $verification_token;
    public $password_reset_token;
    public $password_reset_expires;
    
    // Constantes pour les rôles
    const ROLE_STAGIAIRE = 'stagiaire';
    const ROLE_ENTREPRISE = 'entreprise';
    const ROLE_ADMIN = 'admin';
    
    // Propriétés de l'utilisateur
    public $id;
    public $email;
    public $role;
    public $is_active;
    public $created_at;
    public $updated_at;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function create($email, $password, $role)
    {
        // Validation des données
        if (!$this->isValidEmail($email)) {
            throw new Exception('Email invalide');
        }
        
        if (!$this->isValidRole($role)) {
            throw new Exception('Rôle invalide');
        }
        
        if (strlen($password) < 8) {
            throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
        }
        
        // Vérifier si l'email existe déjà
        if ($this->emailExists($email)) {
            throw new Exception('Cet email est déjà utilisé');
        }
        
        // Hachage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertion en base
        $sql = "INSERT INTO utilisateurs (email, password_hash, role, is_active, email_verified) 
                VALUES (:email, :password_hash, :role, 1, 0)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':role' => $role
        ]);
        
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        
        throw new Exception('Erreur lors de la création de l\'utilisateur');
    }
    
    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password)
    {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Charger les données de l'utilisateur
            $this->loadFromArray($user);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Trouver un utilisateur par ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $user = $stmt->fetch();
        
        if ($user) {
            $this->loadFromArray($user);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $user = $stmt->fetch();
        
        if ($user) {
            $this->loadFromArray($user);
            return true;
        }
        
        return false;
    }
    
    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword($newPassword)
    {
        if (!$this->id) {
            throw new Exception('Utilisateur non chargé');
        }
        
        if (strlen($newPassword) < 8) {
            throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
        }
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE utilisateurs SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':password_hash' => $passwordHash,
            ':id' => $this->id
        ]);
    }
    
    /**
     * Activer/désactiver un utilisateur
     */
    public function setActive($active)
    {
        if (!$this->id) {
            throw new Exception('Utilisateur non chargé');
        }
        
        $sql = "UPDATE utilisateurs SET is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        $result = $stmt->execute([
            ':is_active' => $active ? 1 : 0,
            ':id' => $this->id
        ]);
        
        if ($result) {
            $this->is_active = $active;
        }
        
        return $result;
    }
    
    /**
     * Marquer l'email comme vérifié
     */
    public function verifyEmail()
    {
        if (!$this->id) {
            throw new Exception('Utilisateur non chargé');
        }
        
        $sql = "UPDATE utilisateurs SET email_verified = 1, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        $result = $stmt->execute([':id' => $this->id]);
        
        if ($result) {
            $this->email_verified = true;
        }
        
        return $result;
    }
    
    /**
     * Obtenir tous les utilisateurs (pour admin)
     */
    public function getAll($role = null, $limit = 50, $offset = 0)
    {
        $sql = "SELECT * FROM utilisateurs";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = :role";
            $params[':role'] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Bind des paramètres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function delete()
    {
        if (!$this->id) {
            throw new Exception('Utilisateur non chargé');
        }
        
        $sql = "DELETE FROM utilisateurs WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([':id' => $this->id]);
    }
    
    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }
    
    /**
     * Vérifier si l'utilisateur est un stagiaire
     */
    public function isStagiaire()
    {
        return $this->hasRole(self::ROLE_STAGIAIRE);
    }
    
    /**
     * Vérifier si l'utilisateur est une entreprise
     */
    public function isEntreprise()
    {
        return $this->hasRole(self::ROLE_ENTREPRISE);
    }
    
    /**
     * Vérifier si l'utilisateur est un admin
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }
    
    /**
     * Obtenir les rôles disponibles
     */
    public static function getAvailableRoles()
    {
        return [
            self::ROLE_STAGIAIRE,
            self::ROLE_ENTREPRISE,
            self::ROLE_ADMIN
        ];
    }
    
    // Méthodes privées
    
    /**
     * Charger les données depuis un tableau
     */
    private function loadFromArray($data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->role = $data['role'];
        $this->is_active = (bool)$data['is_active'];
        $this->email_verified = (bool)$data['email_verified'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }
    
    /**
     * Valider un email
     */
    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valider un rôle
     */
    private function isValidRole($role)
    {
        return in_array($role, self::getAvailableRoles());
    }
    
    /**
     * Vérifier si un email existe déjà
     */
    private function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtenir le nombre d'utilisateurs par mois
     */
    public function getUsersByMonth()
    {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as mois,
                        COUNT(*) as total
                    FROM utilisateurs 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY mois";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getUsersByMonth: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir les données de croissance des utilisateurs
     */
    public function getGrowthData()
    {
        try {
            $sql = "SELECT 
                        role,
                        DATE_FORMAT(created_at, '%Y-%m') as mois,
                        COUNT(*) as total
                    FROM utilisateurs 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    GROUP BY role, DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY mois, role";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getGrowthData: " . $e->getMessage());
            return [];
        }
    }
}