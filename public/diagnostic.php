<?php
// Diagnostic PHP complet
echo "<h1>Diagnostic PHP</h1>";

// Informations de base
echo "<h2>Informations de base</h2>";
echo "Version PHP: " . phpversion() . "<br>";
echo "SAPI: " . php_sapi_name() . "<br>";
echo "OS: " . PHP_OS . "<br>";
echo "Architecture: " . php_uname() . "<br>";

// Répertoires
echo "<h2>Répertoires</h2>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "<br>";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Non défini') . "<br>";
echo "Current Working Directory: " . getcwd() . "<br>";
echo "__FILE__: " . __FILE__ . "<br>";
echo "__DIR__: " . __DIR__ . "<br>";

// Include path
echo "<h2>Include Path</h2>";
echo "Include Path: " . get_include_path() . "<br>";

// Auto prepend/append
echo "<h2>Auto Prepend/Append</h2>";
echo "Auto Prepend File: " . ini_get('auto_prepend_file') . "<br>";
echo "Auto Append File: " . ini_get('auto_append_file') . "<br>";

// Variables d'environnement importantes
echo "<h2>Variables d'environnement</h2>";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'Non défini') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Non défini') . "<br>";

// Test d'inclusion
echo "<h2>Test d'inclusion</h2>";
echo "Tentative d'inclusion d'un fichier inexistant...<br>";

// Capturer les erreurs
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Essayer d'inclure un fichier qui n'existe pas
@include 'fichier_inexistant.php';

echo "Test terminé sans erreur fatale.<br>";

// Vérifier les extensions chargées
echo "<h2>Extensions chargées</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo $ext . ", ";
}

echo "<br><br><strong>Diagnostic terminé</strong>";
?>