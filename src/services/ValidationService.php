<?php

namespace JobBoard\Services;

class ValidationService
{
    private $errors = [];
    
    /**
     * Valider un email
     * @param string $email
     * @param string $fieldName
     * @return bool
     */
    public function validateEmail($email, $fieldName = 'Email')
    {
        if (empty($email)) {
            $this->errors[] = "$fieldName est requis";
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "$fieldName doit être une adresse email valide";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider un mot de passe
     * @param string $password
     * @param string $fieldName
     * @param int $minLength
     * @return bool
     */
    public function validatePassword($password, $fieldName = 'Mot de passe', $minLength = 8)
    {
        if (empty($password)) {
            $this->errors[] = "$fieldName est requis";
            return false;
        }
        
        if (strlen($password) < $minLength) {
            $this->errors[] = "$fieldName doit contenir au moins $minLength caractères";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider la confirmation de mot de passe
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public function validatePasswordConfirmation($password, $confirmPassword)
    {
        if ($password !== $confirmPassword) {
            $this->errors[] = "Les mots de passe ne correspondent pas";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider un rôle
     * @param string $role
     * @param array $allowedRoles
     * @return bool
     */
    public function validateRole($role, $allowedRoles = ['stagiaire', 'entreprise'])
    {
        if (empty($role)) {
            $this->errors[] = "Le rôle est requis";
            return false;
        }
        
        if (!in_array($role, $allowedRoles)) {
            $this->errors[] = "Rôle invalide";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider qu'un champ n'est pas vide
     * @param mixed $value
     * @param string $fieldName
     * @return bool
     */
    public function validateRequired($value, $fieldName)
    {
        if (empty($value) && $value !== '0') {
            $this->errors[] = "$fieldName est requis";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider la longueur d'une chaîne
     * @param string $value
     * @param string $fieldName
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    public function validateLength($value, $fieldName, $minLength = null, $maxLength = null)
    {
        $length = strlen($value);
        
        if ($minLength !== null && $length < $minLength) {
            $this->errors[] = "$fieldName doit contenir au moins $minLength caractères";
            return false;
        }
        
        if ($maxLength !== null && $length > $maxLength) {
            $this->errors[] = "$fieldName ne peut pas dépasser $maxLength caractères";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valider un fichier uploadé
     * @param array $file
     * @param array $allowedTypes
     * @param int $maxSize
     * @return bool
     */
    public function validateFile($file, $allowedTypes = [], $maxSize = 5242880) // 5MB par défaut
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "Erreur lors de l'upload du fichier";
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024, 1);
            $this->errors[] = "Le fichier ne peut pas dépasser {$maxSizeMB}MB";
            return false;
        }
        
        if (!empty($allowedTypes)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedTypes)) {
                $this->errors[] = "Type de fichier non autorisé. Types acceptés: " . implode(', ', $allowedTypes);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obtenir toutes les erreurs de validation
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Vérifier s'il y a des erreurs
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
    
    /**
     * Réinitialiser les erreurs
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
    
    /**
     * Ajouter une erreur personnalisée
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }
}