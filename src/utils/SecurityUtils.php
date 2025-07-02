<?php

namespace JobBoard\Utils;

class SecurityUtils
{
    /**
     * Nettoie et valide les entrées utilisateur
     */
    public static function sanitizeInput($input, $type = 'string', $options = [])
    {
        if ($input === null) {
            return null;
        }
        
        // Suppression des espaces en début et fin
        $input = trim($input);
        
        switch ($type) {
            case 'string':
                return self::sanitizeString($input, $options);
            case 'email':
                return self::sanitizeEmail($input);
            case 'url':
                return self::sanitizeUrl($input);
            case 'phone':
                return self::sanitizePhone($input);
            case 'int':
                return self::sanitizeInt($input, $options);
            case 'float':
                return self::sanitizeFloat($input, $options);
            case 'html':
                return self::sanitizeHtml($input, $options);
            case 'filename':
                return self::sanitizeFilename($input);
            case 'slug':
                return self::sanitizeSlug($input);
            default:
                return self::sanitizeString($input, $options);
        }
    }
    
    /**
     * Nettoie une chaîne de caractères
     */
    private static function sanitizeString($input, $options = [])
    {
        $maxLength = $options['max_length'] ?? 1000;
        $allowHtml = $options['allow_html'] ?? false;
        
        if (!$allowHtml) {
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        
        // Limitation de la longueur
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        
        return $input;
    }
    
    /**
     * Valide et nettoie un email
     */
    private static function sanitizeEmail($input)
    {
        $email = filter_var($input, FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide');
        }
        
        return strtolower($email);
    }
    
    /**
     * Valide et nettoie une URL
     */
    private static function sanitizeUrl($input)
    {
        $url = filter_var($input, FILTER_SANITIZE_URL);
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL invalide');
        }
        
        return $url;
    }
    
    /**
     * Nettoie un numéro de téléphone
     */
    private static function sanitizePhone($input)
    {
        // Suppression de tous les caractères non numériques sauf + et espaces
        $phone = preg_replace('/[^0-9+\s-]/', '', $input);
        
        // Validation basique
        if (!preg_match('/^[+]?[0-9\s-]{8,20}$/', $phone)) {
            throw new \InvalidArgumentException('Numéro de téléphone invalide');
        }
        
        return $phone;
    }
    
    /**
     * Nettoie un entier
     */
    private static function sanitizeInt($input, $options = [])
    {
        $min = $options['min'] ?? PHP_INT_MIN;
        $max = $options['max'] ?? PHP_INT_MAX;
        
        $int = filter_var($input, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max
            ]
        ]);
        
        if ($int === false) {
            throw new \InvalidArgumentException('Entier invalide');
        }
        
        return $int;
    }
    
    /**
     * Nettoie un nombre décimal
     */
    private static function sanitizeFloat($input, $options = [])
    {
        $min = $options['min'] ?? -PHP_FLOAT_MAX;
        $max = $options['max'] ?? PHP_FLOAT_MAX;
        
        $float = filter_var($input, FILTER_VALIDATE_FLOAT);
        
        if ($float === false || $float < $min || $float > $max) {
            throw new \InvalidArgumentException('Nombre décimal invalide');
        }
        
        return $float;
    }
    
    /**
     * Nettoie du HTML en gardant certaines balises
     */
    private static function sanitizeHtml($input, $options = [])
    {
        $allowedTags = $options['allowed_tags'] ?? '<p><br><strong><em><ul><ol><li><a>';
        
        return strip_tags($input, $allowedTags);
    }
    
    /**
     * Nettoie un nom de fichier
     */
    private static function sanitizeFilename($input)
    {
        // Suppression des caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $input);
        
        // Limitation de la longueur
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        // Vérification que le nom n'est pas vide
        if (empty($filename)) {
            throw new \InvalidArgumentException('Nom de fichier invalide');
        }
        
        return $filename;
    }
    
    /**
     * Crée un slug sécurisé
     */
    private static function sanitizeSlug($input)
    {
        // Conversion en minuscules
        $slug = strtolower($input);
        
        // Suppression des accents
        $slug = self::removeAccents($slug);
        
        // Remplacement des espaces et caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Suppression des tirets en début et fin
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Supprime les accents d'une chaîne
     */
    private static function removeAccents($string)
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ç' => 'c', 'ñ' => 'n'
        ];
        
        return strtr($string, $accents);
    }
    
    /**
     * Valide un mot de passe selon les critères de sécurité
     */
    public static function validatePassword($password, $options = [])
    {
        $minLength = $options['min_length'] ?? 8;
        $requireUppercase = $options['require_uppercase'] ?? true;
        $requireLowercase = $options['require_lowercase'] ?? true;
        $requireNumbers = $options['require_numbers'] ?? true;
        $requireSpecialChars = $options['require_special_chars'] ?? true;
        
        $errors = [];
        
        // Longueur minimale
        if (strlen($password) < $minLength) {
            $errors[] = "Le mot de passe doit contenir au moins {$minLength} caractères";
        }
        
        // Majuscules
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }
        
        // Minuscules
        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }
        
        // Chiffres
        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }
        
        // Caractères spéciaux
        if ($requireSpecialChars && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
        }
        
        // Vérification contre les mots de passe communs
        if (self::isCommonPassword($password)) {
            $errors[] = 'Ce mot de passe est trop commun';
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Vérifie si un mot de passe est dans la liste des mots de passe communs
     */
    private static function isCommonPassword($password)
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            'dragon', 'master', 'shadow', 'football', 'baseball'
        ];
        
        return in_array(strtolower($password), $commonPasswords);
    }
    
    /**
     * Génère un mot de passe sécurisé
     */
    public static function generateSecurePassword($length = 12)
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $all = $lowercase . $uppercase . $numbers . $special;
        
        $password = '';
        
        // Assurer au moins un caractère de chaque type
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Compléter avec des caractères aléatoires
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Mélanger les caractères
        return str_shuffle($password);
    }
    
    /**
     * Valide un fichier uploadé
     */
    public static function validateUploadedFile($file, $options = [])
    {
        $maxSize = $options['max_size'] ?? 5 * 1024 * 1024; // 5MB par défaut
        $allowedTypes = $options['allowed_types'] ?? ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $allowedMimes = $options['allowed_mimes'] ?? [
            'image/jpeg', 'image/png', 'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $errors = [];
        
        // Vérification de l'erreur d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'Le fichier est trop volumineux';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'Le fichier n\'a été que partiellement uploadé';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'Aucun fichier n\'a été uploadé';
                    break;
                default:
                    $errors[] = 'Erreur lors de l\'upload du fichier';
            }
            return $errors;
        }
        
        // Vérification de la taille
        if ($file['size'] > $maxSize) {
            $errors[] = 'Le fichier est trop volumineux (max: ' . self::formatBytes($maxSize) . ')';
        }
        
        // Vérification du type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimes)) {
            $errors[] = 'Type de fichier non autorisé';
        }
        
        // Vérification de l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'Extension de fichier non autorisée';
        }
        
        // Vérification du nom de fichier
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $file['name'])) {
            $errors[] = 'Nom de fichier invalide';
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Formate la taille en octets en format lisible
     */
    private static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Génère un nom de fichier sécurisé et unique
     */
    public static function generateSecureFilename($originalName, $prefix = '')
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Nettoyage du nom de base
        $basename = self::sanitizeSlug($basename);
        
        // Génération d'un identifiant unique
        $uniqueId = uniqid($prefix, true);
        
        return $basename . '_' . $uniqueId . '.' . $extension;
    }
    
    /**
     * Vérifie si une chaîne contient du contenu malveillant
     */
    public static function detectMaliciousContent($input)
    {
        $patterns = [
            // Scripts
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            
            // SQL Injection
            '/union.*select/i',
            '/drop.*table/i',
            '/insert.*into/i',
            '/delete.*from/i',
            '/update.*set/i',
            
            // Path Traversal
            '/\.\.\//',
            '/\.\.\\\\//',
            
            // PHP Code
            '/<\?php/i',
            '/<\?=/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Encode les données pour éviter les attaques XSS
     */
    public static function escapeOutput($data, $context = 'html')
    {
        switch ($context) {
            case 'html':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            case 'attr':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            case 'js':
                return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            case 'css':
                return preg_replace('/[^a-zA-Z0-9\s\-_#.,]/', '', $data);
            case 'url':
                return urlencode($data);
            default:
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Génère un token sécurisé
     */
    public static function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Hash sécurisé d'un mot de passe
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3          // 3 threads
        ]);
    }
    
    /**
     * Vérifie un mot de passe hashé
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}