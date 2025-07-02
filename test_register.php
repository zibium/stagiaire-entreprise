<?php
// Test du processus d'inscription entreprise via l'interface web
session_start();

// Simuler les données POST
$_POST = [
    'email' => 'test_entreprise_web@example.com',
    'password' => 'testpass123',
    'confirm_password' => 'testpass123',
    'role' => 'entreprise',
    'accept_terms' => '1',
    'csrf_token' => 'test_token'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

// Désactiver la vérification CSRF pour le test
class TestAuthController {
    private $user;
    private $pdo;
    
    public function __construct() {
        $config = require __DIR__ . '/config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        
        require_once __DIR__ . '/src/models/User.php';
        $this->user = new \JobBoard\Models\User($this->pdo);
    }
    
    public function testRegister() {
        echo "Test d'inscription entreprise via interface web...\n\n";
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';
        $acceptTerms = isset($_POST['accept_terms']);
        
        echo "Données reçues:\n";
        echo "- Email: $email\n";
        echo "- Password: " . (empty($password) ? 'vide' : 'fourni') . "\n";
        echo "- Confirm Password: " . (empty($confirmPassword) ? 'vide' : 'fourni') . "\n";
        echo "- Role: $role\n";
        echo "- Accept Terms: " . ($acceptTerms ? 'oui' : 'non') . "\n\n";
        
        // Validation
        $errors = $this->validateRegistration($email, $password, $confirmPassword, $role, $acceptTerms);
        
        if (!empty($errors)) {
            echo "Erreurs de validation:\n";
            foreach ($errors as $error) {
                echo "- $error\n";
            }
            return;
        }
        
        echo "Validation réussie, tentative de création...\n";
        
        try {
            // Nettoyer d'abord si l'email existe
            $checkStmt = $this->pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $deleteStmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE email = ?');
                $deleteStmt->execute([$email]);
                echo "Email existant supprimé pour le test\n";
            }
            
            $userId = $this->user->create($email, $password, $role);
            
            if ($userId) {
                echo "Inscription réussie ! ID utilisateur: $userId\n";
                
                // Vérifier l'utilisateur créé
                $verifyStmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
                $verifyStmt->execute([$userId]);
                $user = $verifyStmt->fetch();
                
                echo "\nUtilisateur créé:\n";
                echo "- ID: " . $user['id'] . "\n";
                echo "- Email: " . $user['email'] . "\n";
                echo "- Rôle: " . $user['role'] . "\n";
                echo "- Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . "\n";
                
                // Nettoyer
                $deleteStmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
                $deleteStmt->execute([$userId]);
                echo "\nUtilisateur de test supprimé.\n";
                
            } else {
                echo "Erreur: La méthode create() a retourné false\n";
            }
        } catch (Exception $e) {
            echo "Exception capturée: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    private function validateRegistration($email, $password, $confirmPassword, $role, $acceptTerms) {
        $errors = [];
        
        // Validation email
        if (empty($email)) {
            $errors[] = 'L\'adresse email est requise.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'adresse email n\'est pas valide.';
        }
        
        // Validation mot de passe
        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        
        // Confirmation mot de passe
        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        // Validation rôle
        $validRoles = ['stagiaire', 'entreprise'];
        if (empty($role) || !in_array($role, $validRoles)) {
            $errors[] = 'Veuillez sélectionner un type de compte valide.';
        }
        
        // Acceptation des conditions
        if (!$acceptTerms) {
            $errors[] = 'Vous devez accepter les conditions d\'utilisation.';
        }
        
        return $errors;
    }
}

try {
    $testController = new TestAuthController();
    $testController->testRegister();
} catch (Exception $e) {
    echo "Erreur fatale: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>