<?php

namespace JobBoard\Services;

class ConfigService
{
    private static $config = null;
    
    /**
     * Charger la configuration
     */
    public static function load()
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/config.php';
        }
        return self::$config;
    }
    
    /**
     * Obtenir une valeur de configuration
     * @param string $key Clé de configuration (ex: 'app.name' ou 'database.host')
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $config = self::load();
        
        // Support pour les clés imbriquées (ex: 'app.name')
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
    
    /**
     * Vérifier si une clé de configuration existe
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }
    
    /**
     * Obtenir toute la configuration
     * @return array
     */
    public static function all()
    {
        return self::load();
    }
}