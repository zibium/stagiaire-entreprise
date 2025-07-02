<?php

namespace JobBoard\Utils;

class UrlHelper {
    /**
     * Génère une URL complète avec le bon préfixe
     * @param string $path Le chemin relatif (ex: '/auth/login')
     * @return string L'URL complète (ex: '/Dev1/public/auth/login')
     */
    public static function url($path) {
        // Supprimer le slash initial si présent
        $path = ltrim($path, '/');
        
        // Récupérer le BASE_PATH depuis la configuration
        $basePath = '/';
        if (defined('BASE_PATH') && BASE_PATH !== '/') {
            $basePath = BASE_PATH;
        }
        
        // S'assurer que le BASE_PATH se termine par un slash
        $basePath = rtrim($basePath, '/') . '/';
        
        return $basePath . $path;
    }
    
    /**
     * Génère une URL pour les assets (CSS, JS, images)
     * @param string $asset Le chemin de l'asset (ex: 'css/style.css')
     * @return string L'URL complète de l'asset
     */
    public static function asset($asset) {
        return self::url($asset);
    }
}