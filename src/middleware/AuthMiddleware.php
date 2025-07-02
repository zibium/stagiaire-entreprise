<?php

namespace JobBoard\Middleware;

use JobBoard\Models\User;

class AuthMiddleware
{
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function requireAuth()
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /Dev1/public/auth/login');
            exit;
        }
        
        // Vérifier si la session n'a pas expiré
        if (self::isSessionExpired()) {
            self::logout();
            $_SESSION['error'] = 'Votre session a expiré. Veuillez vous reconnecter.';
            header('Location: /Dev1/public/auth/login');
            exit;
        }
        
        // Régénérer l'ID de session périodiquement pour la sécurité
        self::regenerateSessionId();
    }
    
    /**
     * Vérifie si l'utilisateur a le rôle requis
     */
    public static function requireRole($requiredRole)
    {
        self::requireAuth();
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if ($userRole !== $requiredRole) {
            $_SESSION['error'] = 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.';
            self::redirectToDashboard();
            exit;
        }
    }
    
    /**
     * Vérifie si l'utilisateur a l'un des rôles autorisés
     */
    public static function requireAnyRole($allowedRoles)
    {
        self::requireAuth();
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.';
            self::redirectToDashboard();
            exit;
        }
    }
    
    /**
     * Middleware pour les administrateurs uniquement
     */
    public static function requireAdmin()
    {
        self::requireRole(User::ROLE_ADMIN);
    }
    
    /**
     * Middleware pour les stagiaires uniquement
     */
    public static function requireStagiaire()
    {
        self::requireRole(User::ROLE_STAGIAIRE);
    }
    
    /**
     * Middleware pour les entreprises uniquement
     */
    public static function requireEntreprise()
    {
        self::requireRole(User::ROLE_ENTREPRISE);
    }
    
    /**
     * Middleware pour les utilisateurs connectés (stagiaires ou entreprises)
     */
    public static function requireUser()
    {
        self::requireAnyRole([User::ROLE_STAGIAIRE, User::ROLE_ENTREPRISE]);
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn()
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public static function hasRole($role)
    {
        return self::isLoggedIn() && ($_SESSION['user_role'] ?? '') === $role;
    }
    
    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public static function isAdmin()
    {
        return self::hasRole(User::ROLE_ADMIN);
    }
    
    /**
     * Vérifie si l'utilisateur est un stagiaire
     */
    public static function isStagiaire()
    {
        return self::hasRole(User::ROLE_STAGIAIRE);
    }
    
    /**
     * Vérifie si l'utilisateur est une entreprise
     */
    public static function isEntreprise()
    {
        return self::hasRole(User::ROLE_ENTREPRISE);
    }
    
    /**
     * Obtient l'ID de l'utilisateur connecté
     */
    public static function getUserId()
    {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Obtient l'email de l'utilisateur connecté
     */
    public static function getUserEmail()
    {
        return self::isLoggedIn() ? $_SESSION['user_email'] : null;
    }
    
    /**
     * Obtient le rôle de l'utilisateur connecté
     */
    public static function getUserRole()
    {
        return self::isLoggedIn() ? $_SESSION['user_role'] : null;
    }
    
    /**
     * Vérifie si la session a expiré
     */
    private static function isSessionExpired()
    {
        $maxLifetime = 3600; // 1 heure par défaut
        
        if (isset($_SESSION['login_time'])) {
            return (time() - $_SESSION['login_time']) > $maxLifetime;
        }
        
        return true;
    }
    
    /**
     * Régénère l'ID de session périodiquement
     */
    private static function regenerateSessionId()
    {
        $regenerateInterval = 1800; // 30 minutes
        
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
            session_regenerate_id(true);
        } elseif (time() - $_SESSION['last_regeneration'] > $regenerateInterval) {
            $_SESSION['last_regeneration'] = time();
            session_regenerate_id(true);
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public static function logout()
    {
        // Détruire toutes les variables de session
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        // Détruire la session
        session_destroy();
        
        // Supprimer le cookie "remember me" si présent
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
    }
    
    /**
     * Redirige vers le tableau de bord approprié selon le rôle
     */
    private static function redirectToDashboard()
    {
        $role = $_SESSION['user_role'] ?? '';
        
        switch ($role) {
            case User::ROLE_ADMIN:
                header('Location: /Dev1/public/admin/dashboard');
                break;
            case User::ROLE_ENTREPRISE:
                header('Location: /Dev1/public/entreprise/dashboard');
                break;
            case User::ROLE_STAGIAIRE:
                header('Location: /Dev1/public/stagiaire/dashboard');
                break;
            default:
                header('Location: /Dev1/public/');
        }
    }
    
    /**
     * Middleware pour rediriger les utilisateurs connectés
     * (utile pour les pages de connexion/inscription)
     */
    public static function redirectIfAuthenticated()
    {
        if (self::isLoggedIn()) {
            self::redirectToDashboard();
            exit;
        }
    }
    
    /**
     * Vérifie les permissions pour une ressource spécifique
     * Par exemple, un stagiaire ne peut modifier que son propre profil
     */
    public static function canAccessResource($resourceUserId)
    {
        if (self::isAdmin()) {
            return true; // Les admins peuvent tout faire
        }
        
        $currentUserId = self::getUserId();
        return $currentUserId && $currentUserId == $resourceUserId;
    }
    
    /**
     * Middleware pour vérifier l'accès à une ressource
     */
    public static function requireResourceAccess($resourceUserId)
    {
        self::requireAuth();
        
        if (!self::canAccessResource($resourceUserId)) {
            $_SESSION['error'] = 'Vous n\'avez pas les permissions pour accéder à cette ressource.';
            self::redirectToDashboard();
            exit;
        }
    }
    
    /**
     * Vérifie le token CSRF
     */
    public static function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Génère un token CSRF
     */
    public static function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Middleware pour vérifier le token CSRF
     */
    public static function requireCsrfToken()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!self::verifyCsrfToken($token)) {
                $_SESSION['error'] = 'Token de sécurité invalide.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                exit;
            }
        }
    }
    
    /**
     * Enregistre l'activité de l'utilisateur (pour audit)
     */
    public static function logActivity($action, $details = '')
    {
        if (self::isLoggedIn()) {
            $userId = self::getUserId();
            $userRole = self::getUserRole();
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Log dans un fichier ou base de données
            $logEntry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => $userId,
                'user_role' => $userRole,
                'action' => $action,
                'details' => $details,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'url' => $_SERVER['REQUEST_URI'] ?? ''
            ];
            
            // Ici vous pouvez implémenter la logique de logging
            // Par exemple, écrire dans un fichier de log ou insérer en base
            error_log('User Activity: ' . json_encode($logEntry));
        }
    }
}