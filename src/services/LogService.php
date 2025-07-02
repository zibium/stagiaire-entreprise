<?php

namespace JobBoard\Services;

class LogService
{
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';
    
    private static $logFile = null;
    
    /**
     * Initialiser le service de log
     */
    public static function init()
    {
        if (self::$logFile === null) {
            $logDir = __DIR__ . '/../../logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            self::$logFile = $logDir . '/app.log';
        }
    }
    
    /**
     * Écrire un log
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private static function log($level, $message, $context = [])
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log de débogage
     * @param string $message
     * @param array $context
     */
    public static function debug($message, $context = [])
    {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * Log d'information
     * @param string $message
     * @param array $context
     */
    public static function info($message, $context = [])
    {
        self::log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * Log d'avertissement
     * @param string $message
     * @param array $context
     */
    public static function warning($message, $context = [])
    {
        self::log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * Log d'erreur
     * @param string $message
     * @param array $context
     */
    public static function error($message, $context = [])
    {
        self::log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * Log critique
     * @param string $message
     * @param array $context
     */
    public static function critical($message, $context = [])
    {
        self::log(self::LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * Logger une exception
     * @param \Exception $exception
     * @param string $level
     */
    public static function exception($exception, $level = self::LEVEL_ERROR)
    {
        $message = $exception->getMessage();
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];
        
        self::log($level, $message, $context);
    }
    
    /**
     * Logger une tentative de connexion
     * @param string $email
     * @param bool $success
     * @param string $ip
     */
    public static function loginAttempt($email, $success, $ip = null)
    {
        $ip = $ip ?: $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $status = $success ? 'SUCCESS' : 'FAILED';
        $message = "Login attempt for $email: $status";
        $context = ['ip' => $ip, 'email' => $email, 'success' => $success];
        
        self::info($message, $context);
    }
    
    /**
     * Logger une inscription
     * @param string $email
     * @param string $role
     * @param string $ip
     */
    public static function registration($email, $role, $ip = null)
    {
        $ip = $ip ?: $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $message = "New registration: $email as $role";
        $context = ['ip' => $ip, 'email' => $email, 'role' => $role];
        
        self::info($message, $context);
    }
}