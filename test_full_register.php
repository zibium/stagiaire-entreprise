<?php
// Test complet du processus d'inscription avec sessions et CSRF
session_start();

echo "=== Test complet d'inscription entreprise ===\n\n";

// Inclure les dépendances
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/utils/UrlHelper.php';

try {
    // Créer une instance du contrôleur
    $authController = new \JobBoard\Controllers\AuthController();
    
    echo "1. Test de génération du token CSRF...\n";
    $csrfToken = $authController->generateCsrfToken();
    echo "Token CSRF généré: " . substr($csrfToken, 0, 16) . "...\n";
    echo "Token en session: " . (isset($_SESSION['csrf_token']) ? 'Oui' : 'Non') . "\n\n";
    
    echo "2. Simulation d'une requête POST d'inscription...\n";
    
    // Simuler les données POST
    $_POST = [
        'email' => 'test_full_entreprise@example.com',
        'password' => 'testpass123',
        'confirm_password' => 'testpass123',
        'role' => 'entreprise',
        'accept_terms' => '1',
        'csrf_token' => $csrfToken
    ];
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    echo "Données POST simulées:\n";
    foreach ($_POST as $key => $value) {
        if ($key === 'password' || $key === 'confirm_password') {
            echo "- $key: [masqué]\n";
        } else {
            echo "- $key: $value\n";
        }
    }
    echo "\n";
    
    echo "3. Capture de la sortie du processus d'inscription...\n";
    
    // Capturer la sortie
    ob_start();
    
    // Rediriger les headers vers une variable
    $headers = [];
    function test_header($string) {
        global $headers;
        $headers[] = $string;
    }
    
    // Remplacer temporairement la fonction header
    if (!function_exists('header')) {
        function header($string) {
            test_header($string);
        }
    }
    
    try {
        // Appeler la méthode register
        $authController->register();
        
        $output = ob_get_contents();
        ob_end_clean();
        
        echo "Sortie capturée: " . (empty($output) ? 'Aucune sortie' : $output) . "\n";
        echo "Headers capturés: " . count($headers) . " header(s)\n";
        foreach ($headers as $header) {
            echo "- $header\n";
        }
        
        echo "\n4. Vérification des messages de session...\n";
        
        if (isset($_SESSION['success'])) {
            echo "Message de succès: " . $_SESSION['success'] . "\n";
        }
        
        if (isset($_SESSION['error'])) {
            echo "Message d'erreur: " . $_SESSION['error'] . "\n";
        }
        
        if (isset($_SESSION['errors'])) {
            echo "Erreurs de validation:\n";
            foreach ($_SESSION['errors'] as $error) {
                echo "- $error\n";
            }
        }
        
        if (!isset($_SESSION['success']) && !isset($_SESSION['error']) && !isset($_SESSION['errors'])) {
            echo "Aucun message de session trouvé\n";
        }
        
        echo "\n5. Vérification en base de données...\n";
        
        // Vérifier si l'utilisateur a été créé
        $config = require __DIR__ . '/config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute(['test_full_entreprise@example.com']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "Utilisateur trouvé en base:\n";
            echo "- ID: " . $user['id'] . "\n";
            echo "- Email: " . $user['email'] . "\n";
            echo "- Rôle: " . $user['role'] . "\n";
            echo "- Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . "\n";
            
            // Nettoyer
            $deleteStmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
            $deleteStmt->execute([$user['id']]);
            echo "\nUtilisateur de test supprimé.\n";
        } else {
            echo "Aucun utilisateur trouvé en base de données\n";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "Exception durant l'inscription: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur fatale: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Fin du test ===\n";

?>