<?php
/**
 * Configuration générale de l'application JobBoard
 * Plateforme de stages
 */

// Définir les constantes d'environnement
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? true);
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('APP_NAME', 'JobBoard');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', $_ENV['BASE_PATH'] ?? '/');

return [
    // Configuration de l'application
    'app' => [
        'name' => 'JobBoard',
        'version' => '1.0.0',
        'environment' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'timezone' => 'Europe/Paris'
    ],

    // Configuration des sessions
    'session' => [
        'name' => 'jobboard_session',
        'lifetime' => 7200, // 2 heures
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],

    // Configuration des uploads
    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => [
            'cv' => ['pdf', 'doc', 'docx'],
            'logo' => ['jpg', 'jpeg', 'png', 'gif']
        ],
        'path' => __DIR__ . '/../uploads/'
    ],

    // Configuration de sécurité
    'security' => [
        'password_min_length' => 8,
        'session_regenerate' => true,
        'csrf_token_name' => '_token'
    ],

    // Configuration des rôles
    'roles' => [
        'stagiaire' => 'stagiaire',
        'entreprise' => 'entreprise',
        'admin' => 'admin'
    ]
];