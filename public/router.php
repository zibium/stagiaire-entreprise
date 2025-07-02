<?php
// Router simple pour le serveur de développement PHP
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Si c'est un fichier statique qui existe, le servir directement
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false; // Laisser le serveur servir le fichier statique
}

// Sinon, rediriger vers index.php
require_once __DIR__ . '/index.php';
?>