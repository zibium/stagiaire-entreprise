<?php
// Page de test pour l'inscription entreprise
session_start();

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/test_errors.log');

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Inscription Entreprise</title></head><body>";
echo "<h1>Test d'inscription entreprise</h1>";

// Inclure les dépendances
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/utils/UrlHelper.php';

try {
    $authController = new \JobBoard\Controllers\AuthController();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>Traitement de l'inscription...</h2>";
        echo "<p>Données reçues:</p><ul>";
        foreach ($_POST as $key => $value) {
            if ($key === 'password' || $key === 'confirm_password') {
                echo "<li>$key: [masqué]</li>";
            } else {
                echo "<li>$key: " . htmlspecialchars($value) . "</li>";
            }
        }
        echo "</ul>";
        
        // Traitement de l'inscription
        ob_start();
        try {
            $authController->register();
            $output = ob_get_contents();
        } catch (Exception $e) {
            $output = "Exception: " . $e->getMessage();
        }
        ob_end_clean();
        
        echo "<h3>Résultat:</h3>";
        
        if (isset($_SESSION['success'])) {
            echo "<div style='color: green; padding: 10px; border: 1px solid green; background: #f0fff0;'>";
            echo "Succès: " . htmlspecialchars($_SESSION['success']);
            echo "</div>";
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['error'])) {
            echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #fff0f0;'>";
            echo "Erreur: " . htmlspecialchars($_SESSION['error']);
            echo "</div>";
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['errors'])) {
            echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #fff0f0;'>";
            echo "Erreurs de validation:<ul>";
            foreach ($_SESSION['errors'] as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul></div>";
            unset($_SESSION['errors']);
        }
        
        if (!empty($output)) {
            echo "<p>Sortie: " . htmlspecialchars($output) . "</p>";
        }
        
        // Vérifier en base
        echo "<h3>Vérification en base de données:</h3>";
        try {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ? ORDER BY created_at DESC LIMIT 1');
            $stmt->execute([$_POST['email']]);
            $user = $stmt->fetch();
            
            if ($user) {
                echo "<p style='color: green;'>Utilisateur trouvé en base:</p>";
                echo "<ul>";
                echo "<li>ID: " . $user['id'] . "</li>";
                echo "<li>Email: " . htmlspecialchars($user['email']) . "</li>";
                echo "<li>Rôle: " . htmlspecialchars($user['role']) . "</li>";
                echo "<li>Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . "</li>";
                echo "<li>Créé le: " . $user['created_at'] . "</li>";
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>Aucun utilisateur trouvé en base de données</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Erreur de vérification: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
    } else {
        // Afficher le formulaire
        $csrfToken = $authController->generateCsrfToken();
        
        echo "<form method='POST' style='max-width: 400px;'>";
        echo "<h2>Formulaire d'inscription entreprise</h2>";
        
        echo "<p><label>Email:</label><br>";
        echo "<input type='email' name='email' value='test_entreprise_form@example.com' required style='width: 100%; padding: 5px;'></p>";
        
        echo "<p><label>Mot de passe:</label><br>";
        echo "<input type='password' name='password' value='testpass123' required style='width: 100%; padding: 5px;'></p>";
        
        echo "<p><label>Confirmer le mot de passe:</label><br>";
        echo "<input type='password' name='confirm_password' value='testpass123' required style='width: 100%; padding: 5px;'></p>";
        
        echo "<p><label>Rôle:</label><br>";
        echo "<select name='role' required style='width: 100%; padding: 5px;'>";
        echo "<option value=''>Sélectionner...</option>";
        echo "<option value='stagiaire'>Stagiaire</option>";
        echo "<option value='entreprise' selected>Entreprise</option>";
        echo "</select></p>";
        
        echo "<p><label>";
        echo "<input type='checkbox' name='accept_terms' value='1' checked> J'accepte les conditions d'utilisation";
        echo "</label></p>";
        
        echo "<input type='hidden' name='csrf_token' value='" . htmlspecialchars($csrfToken) . "'>";
        
        echo "<p><input type='submit' value='S\'inscrire' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'></p>";
        
        echo "</form>";
        
        echo "<p><strong>Token CSRF:</strong> " . htmlspecialchars(substr($csrfToken, 0, 16)) . "...</p>";
        echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #fff0f0;'>";
    echo "Erreur fatale: " . htmlspecialchars($e->getMessage());
    echo "<br>Trace: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";

?>