<?php

namespace JobBoard\Middleware;

class RateLimitMiddleware
{
    private static $limits = [
        'login' => ['max' => 5, 'window' => 900], // 5 tentatives par 15 minutes
        'register' => ['max' => 3, 'window' => 3600], // 3 inscriptions par heure
        'password_reset' => ['max' => 3, 'window' => 3600], // 3 demandes par heure
        'contact' => ['max' => 10, 'window' => 3600], // 10 messages par heure
        'application' => ['max' => 20, 'window' => 86400], // 20 candidatures par jour
        'offer_creation' => ['max' => 10, 'window' => 86400], // 10 offres par jour
        'file_upload' => ['max' => 5, 'window' => 3600], // 5 uploads par heure
        'api' => ['max' => 100, 'window' => 3600] // 100 requêtes API par heure
    ];
    
    /**
     * Vérifie si l'action est autorisée selon les limites de taux
     */
    public static function checkLimit($action, $identifier = null)
    {
        if (!isset(self::$limits[$action])) {
            return true; // Pas de limite définie
        }
        
        $limit = self::$limits[$action];
        $key = self::generateKey($action, $identifier);
        
        // Nettoyer les anciennes entrées
        self::cleanupOldEntries($key, $limit['window']);
        
        // Compter les tentatives actuelles
        $attempts = self::getAttempts($key);
        
        if ($attempts >= $limit['max']) {
            self::logRateLimitExceeded($action, $identifier);
            return false;
        }
        
        return true;
    }
    
    /**
     * Enregistre une tentative
     */
    public static function recordAttempt($action, $identifier = null)
    {
        if (!isset(self::$limits[$action])) {
            return;
        }
        
        $key = self::generateKey($action, $identifier);
        $attempts = self::getAttempts($key);
        $attempts[] = time();
        
        self::storeAttempts($key, $attempts);
    }
    
    /**
     * Middleware pour vérifier les limites de taux
     */
    public static function requireRateLimit($action, $identifier = null)
    {
        if (!self::checkLimit($action, $identifier)) {
            $limit = self::$limits[$action];
            $timeLeft = self::getTimeUntilReset($action, $identifier);
            
            http_response_code(429);
            header('Retry-After: ' . $timeLeft);
            
            $_SESSION['error'] = sprintf(
                'Trop de tentatives. Veuillez patienter %s avant de réessayer.',
                self::formatTime($timeLeft)
            );
            
            // Redirection ou affichage d'erreur
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                self::showRateLimitError($timeLeft);
            }
            exit;
        }
    }
    
    /**
     * Génère une clé unique pour l'action et l'identifiant
     */
    private static function generateKey($action, $identifier = null)
    {
        if ($identifier === null) {
            $identifier = self::getClientIdentifier();
        }
        
        return 'rate_limit_' . $action . '_' . hash('sha256', $identifier);
    }
    
    /**
     * Obtient un identifiant unique pour le client
     */
    private static function getClientIdentifier()
    {
        // Utiliser l'IP + User Agent pour identifier le client
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Si l'utilisateur est connecté, utiliser son ID
        if (isset($_SESSION['user_id'])) {
            return 'user_' . $_SESSION['user_id'];
        }
        
        return $ip . '_' . hash('sha256', $userAgent);
    }
    
    /**
     * Récupère les tentatives stockées
     */
    private static function getAttempts($key)
    {
        if (!isset($_SESSION[$key])) {
            return [];
        }
        
        return $_SESSION[$key];
    }
    
    /**
     * Stocke les tentatives
     */
    private static function storeAttempts($key, $attempts)
    {
        $_SESSION[$key] = $attempts;
    }
    
    /**
     * Nettoie les anciennes entrées
     */
    private static function cleanupOldEntries($key, $window)
    {
        $attempts = self::getAttempts($key);
        $cutoff = time() - $window;
        
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });
        
        self::storeAttempts($key, array_values($attempts));
    }
    
    /**
     * Calcule le temps restant avant la réinitialisation
     */
    public static function getTimeUntilReset($action, $identifier = null)
    {
        if (!isset(self::$limits[$action])) {
            return 0;
        }
        
        $limit = self::$limits[$action];
        $key = self::generateKey($action, $identifier);
        $attempts = self::getAttempts($key);
        
        if (empty($attempts)) {
            return 0;
        }
        
        $oldestAttempt = min($attempts);
        $resetTime = $oldestAttempt + $limit['window'];
        
        return max(0, $resetTime - time());
    }
    
    /**
     * Obtient les informations sur les limites pour une action
     */
    public static function getLimitInfo($action, $identifier = null)
    {
        if (!isset(self::$limits[$action])) {
            return null;
        }
        
        $limit = self::$limits[$action];
        $key = self::generateKey($action, $identifier);
        
        self::cleanupOldEntries($key, $limit['window']);
        $attempts = self::getAttempts($key);
        
        return [
            'max' => $limit['max'],
            'window' => $limit['window'],
            'current' => count($attempts),
            'remaining' => max(0, $limit['max'] - count($attempts)),
            'reset_time' => self::getTimeUntilReset($action, $identifier)
        ];
    }
    
    /**
     * Réinitialise les limites pour une action et un identifiant
     */
    public static function resetLimit($action, $identifier = null)
    {
        $key = self::generateKey($action, $identifier);
        unset($_SESSION[$key]);
    }
    
    /**
     * Formate le temps en format lisible
     */
    private static function formatTime($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' seconde' . ($seconds > 1 ? 's' : '');
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        } else {
            $hours = floor($seconds / 3600);
            return $hours . ' heure' . ($hours > 1 ? 's' : '');
        }
    }
    
    /**
     * Enregistre les dépassements de limite
     */
    private static function logRateLimitExceeded($action, $identifier)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'identifier' => $identifier ?: self::getClientIdentifier(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? ''
        ];
        
        error_log('Rate limit exceeded: ' . json_encode($logData));
        
        // Optionnel: Stocker en base de données pour analyse
        self::storeSecurityEvent('rate_limit_exceeded', $logData);
    }
    
    /**
     * Stocke les événements de sécurité
     */
    private static function storeSecurityEvent($type, $data)
    {
        // Ici vous pouvez implémenter le stockage en base de données
        // pour analyser les tentatives d'attaque
        
        $logFile = __DIR__ . '/../../logs/security.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = date('Y-m-d H:i:s') . ' [' . strtoupper($type) . '] ' . json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Affiche une page d'erreur pour dépassement de limite
     */
    private static function showRateLimitError($timeLeft)
    {
        $timeFormatted = self::formatTime($timeLeft);
        
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limite de taux dépassée - JobBoard</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
        }
        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        h1 {
            color: #343a40;
            margin-bottom: 20px;
        }
        p {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .retry-info {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>Limite de taux dépassée</h1>
        <p>Vous avez effectué trop de tentatives. Cette mesure de sécurité protège notre plateforme contre les abus.</p>
        <div class="retry-info">
            <strong>Veuillez patienter ' . htmlspecialchars($timeFormatted) . ' avant de réessayer.</strong>
        </div>
        <a href="/" class="btn">Retour à l\'accueil</a>
    </div>
    
    <script>
        // Auto-refresh après le délai
        setTimeout(function() {
            window.location.reload();
        }, ' . ($timeLeft * 1000) . ');
    </script>
</body>
</html>';
    }
    
    /**
     * Middleware spécialisé pour les tentatives de connexion
     */
    public static function requireLoginRateLimit($email = null)
    {
        $identifier = $email ?: self::getClientIdentifier();
        self::requireRateLimit('login', $identifier);
    }
    
    /**
     * Middleware pour les uploads de fichiers
     */
    public static function requireFileUploadRateLimit()
    {
        self::requireRateLimit('file_upload');
    }
    
    /**
     * Middleware pour les candidatures
     */
    public static function requireApplicationRateLimit()
    {
        if (isset($_SESSION['user_id'])) {
            self::requireRateLimit('application', 'user_' . $_SESSION['user_id']);
        } else {
            self::requireRateLimit('application');
        }
    }
    
    /**
     * Obtient les statistiques globales des limites de taux
     */
    public static function getGlobalStats()
    {
        $stats = [];
        
        foreach (self::$limits as $action => $limit) {
            $info = self::getLimitInfo($action);
            if ($info) {
                $stats[$action] = $info;
            }
        }
        
        return $stats;
    }
}