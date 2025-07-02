<?php

/**
 * Tests de sécurité pour la plateforme JobBoard
 * 
 * Ce script teste les principales vulnérabilités de sécurité :
 * - Protection CSRF
 * - Validation des entrées
 * - Sécurité des uploads
 * - Rate limiting
 * - Authentification
 * - Autorisation
 * - Injection SQL
 * - XSS
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../src/utils/SecurityUtils.php';

use JobBoard\Middleware\AuthMiddleware;
use JobBoard\Middleware\RateLimitMiddleware;
use JobBoard\Utils\SecurityUtils;

class SecurityTests
{
    private $results = [];
    private $testCount = 0;
    private $passedTests = 0;
    private $failedTests = 0;
    
    public function __construct()
    {
        echo "\n=== TESTS DE SÉCURITÉ JOBBOARD ===\n\n";
    }
    
    /**
     * Lance tous les tests de sécurité
     */
    public function runAllTests()
    {
        $this->testCSRFProtection();
        $this->testInputValidation();
        $this->testFileUploadSecurity();
        $this->testRateLimiting();
        $this->testPasswordSecurity();
        $this->testSQLInjectionProtection();
        $this->testXSSProtection();
        $this->testAuthenticationSecurity();
        $this->testSessionSecurity();
        $this->testFileInclusionProtection();
        
        $this->displayResults();
    }
    
    /**
     * Test de la protection CSRF
     */
    private function testCSRFProtection()
    {
        echo "🔒 Test de protection CSRF...\n";
        
        // Test de génération de token CSRF
        $token1 = AuthMiddleware::generateCsrfToken();
        $token2 = AuthMiddleware::generateCsrfToken();
        
        $this->assert(
            !empty($token1) && !empty($token2),
            "Génération de tokens CSRF",
            "Les tokens CSRF doivent être générés"
        );
        
        $this->assert(
            $token1 !== $token2,
            "Unicité des tokens CSRF",
            "Chaque token CSRF doit être unique"
        );
        
        // Test de validation de token
        $_SESSION['csrf_token'] = $token1;
        
        $this->assert(
            AuthMiddleware::verifyCsrfToken($token1),
            "Validation token CSRF valide",
            "Un token CSRF valide doit être accepté"
        );
        
        $this->assert(
            !AuthMiddleware::verifyCsrfToken('invalid_token'),
            "Rejet token CSRF invalide",
            "Un token CSRF invalide doit être rejeté"
        );
    }
    
    /**
     * Test de validation des entrées
     */
    private function testInputValidation()
    {
        echo "🔍 Test de validation des entrées...\n";
        
        // Test de nettoyage des chaînes
        $maliciousInput = '<script>alert("XSS")</script>';
        $cleaned = SecurityUtils::sanitizeInput($maliciousInput, 'string');
        
        $this->assert(
            !str_contains($cleaned, '<script>'),
            "Nettoyage des scripts malveillants",
            "Les balises script doivent être échappées"
        );
        
        // Test de validation d'email
        try {
            $validEmail = SecurityUtils::sanitizeInput('test@example.com', 'email');
            $this->assert(
                $validEmail === 'test@example.com',
                "Validation email valide",
                "Un email valide doit être accepté"
            );
        } catch (Exception $e) {
            $this->assert(false, "Validation email valide", "Erreur inattendue: " . $e->getMessage());
        }
        
        try {
            SecurityUtils::sanitizeInput('invalid-email', 'email');
            $this->assert(false, "Rejet email invalide", "Un email invalide devrait lever une exception");
        } catch (InvalidArgumentException $e) {
            $this->assert(true, "Rejet email invalide", "Un email invalide doit être rejeté");
        }
        
        // Test de validation d'entier
        try {
            $validInt = SecurityUtils::sanitizeInput('123', 'int', ['min' => 1, 'max' => 1000]);
            $this->assert(
                $validInt === 123,
                "Validation entier valide",
                "Un entier valide doit être accepté"
            );
        } catch (Exception $e) {
            $this->assert(false, "Validation entier valide", "Erreur inattendue: " . $e->getMessage());
        }
    }
    
    /**
     * Test de sécurité des uploads de fichiers
     */
    private function testFileUploadSecurity()
    {
        echo "📁 Test de sécurité des uploads...\n";
        
        // Simulation d'un fichier valide
        $validFile = [
            'name' => 'document.pdf',
            'type' => 'application/pdf',
            'size' => 1024 * 1024, // 1MB
            'tmp_name' => '/tmp/test',
            'error' => UPLOAD_ERR_OK
        ];
        
        // Test de nom de fichier sécurisé
        $secureFilename = SecurityUtils::generateSecureFilename('mon document.pdf', 'cv_');
        
        $this->assert(
            !str_contains($secureFilename, ' '),
            "Génération nom fichier sécurisé",
            "Le nom de fichier ne doit pas contenir d'espaces"
        );
        
        $this->assert(
            str_starts_with($secureFilename, 'mon-document_cv_'),
            "Préfixe nom fichier",
            "Le nom de fichier doit contenir le préfixe"
        );
        
        // Test de validation d'extension
        $maliciousFile = [
            'name' => 'malware.exe',
            'type' => 'application/octet-stream',
            'size' => 1024,
            'tmp_name' => '/tmp/test',
            'error' => UPLOAD_ERR_OK
        ];
        
        $result = SecurityUtils::validateUploadedFile($maliciousFile);
        
        $this->assert(
            is_array($result) && !empty($result),
            "Rejet fichier malveillant",
            "Un fichier avec extension dangereuse doit être rejeté"
        );
    }
    
    /**
     * Test du rate limiting
     */
    private function testRateLimiting()
    {
        echo "⏱️ Test du rate limiting...\n";
        
        // Reset des limites pour le test
        RateLimitMiddleware::resetLimit('login', 'test_user');
        
        // Test de limite normale
        $this->assert(
            RateLimitMiddleware::checkLimit('login', 'test_user'),
            "Première tentative autorisée",
            "La première tentative doit être autorisée"
        );
        
        // Simulation de multiples tentatives
        for ($i = 0; $i < 6; $i++) {
            RateLimitMiddleware::recordAttempt('login', 'test_user');
        }
        
        $this->assert(
            !RateLimitMiddleware::checkLimit('login', 'test_user'),
            "Limite de taux dépassée",
            "Après 5 tentatives, l'accès doit être bloqué"
        );
        
        // Test des informations de limite
        $limitInfo = RateLimitMiddleware::getLimitInfo('login', 'test_user');
        
        $this->assert(
            isset($limitInfo['remaining']) && $limitInfo['remaining'] === 0,
            "Informations de limite correctes",
            "Les informations de limite doivent être exactes"
        );
    }
    
    /**
     * Test de sécurité des mots de passe
     */
    private function testPasswordSecurity()
    {
        echo "🔐 Test de sécurité des mots de passe...\n";
        
        // Test de validation de mot de passe faible
        $weakPassword = '123456';
        $validation = SecurityUtils::validatePassword($weakPassword);
        
        $this->assert(
            is_array($validation) && !empty($validation),
            "Rejet mot de passe faible",
            "Un mot de passe faible doit être rejeté"
        );
        
        // Test de validation de mot de passe fort
        $strongPassword = 'MyStr0ng!P@ssw0rd';
        $validation = SecurityUtils::validatePassword($strongPassword);
        
        $this->assert(
            $validation === true,
            "Acceptation mot de passe fort",
            "Un mot de passe fort doit être accepté"
        );
        
        // Test de hachage sécurisé
        $hash = SecurityUtils::hashPassword($strongPassword);
        
        $this->assert(
            !empty($hash) && $hash !== $strongPassword,
            "Hachage du mot de passe",
            "Le mot de passe doit être haché"
        );
        
        // Test de vérification du hash
        $this->assert(
            SecurityUtils::verifyPassword($strongPassword, $hash),
            "Vérification hash correct",
            "La vérification du hash doit fonctionner"
        );
        
        $this->assert(
            !SecurityUtils::verifyPassword('wrong_password', $hash),
            "Rejet hash incorrect",
            "Un mauvais mot de passe doit être rejeté"
        );
    }
    
    /**
     * Test de protection contre l'injection SQL
     */
    private function testSQLInjectionProtection()
    {
        echo "💉 Test de protection injection SQL...\n";
        
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "admin'/*",
            "1; DELETE FROM offers WHERE 1=1; --"
        ];
        
        foreach ($maliciousInputs as $input) {
            $detected = SecurityUtils::detectMaliciousContent($input);
            $this->assert(
                $detected,
                "Détection injection SQL: " . substr($input, 0, 20) . "...",
                "Le contenu malveillant doit être détecté"
            );
        }
    }
    
    /**
     * Test de protection XSS
     */
    private function testXSSProtection()
    {
        echo "🚫 Test de protection XSS...\n";
        
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(1)">',
            'javascript:alert("XSS")',
            '<svg onload="alert(1)">'
        ];
        
        foreach ($xssPayloads as $payload) {
            $escaped = SecurityUtils::escapeOutput($payload, 'html');
            $this->assert(
                !str_contains($escaped, '<script>') && !str_contains($escaped, 'javascript:'),
                "Échappement XSS: " . substr($payload, 0, 20) . "...",
                "Le contenu XSS doit être échappé"
            );
            
            $detected = SecurityUtils::detectMaliciousContent($payload);
            $this->assert(
                $detected,
                "Détection XSS: " . substr($payload, 0, 20) . "...",
                "Le contenu XSS doit être détecté"
            );
        }
    }
    
    /**
     * Test de sécurité de l'authentification
     */
    private function testAuthenticationSecurity()
    {
        echo "🔑 Test de sécurité de l'authentification...\n";
        
        // Test de génération de token sécurisé
        $token1 = SecurityUtils::generateSecureToken();
        $token2 = SecurityUtils::generateSecureToken();
        
        $this->assert(
            strlen($token1) === 64, // 32 bytes = 64 hex chars
            "Longueur token sécurisé",
            "Le token doit avoir la bonne longueur"
        );
        
        $this->assert(
            $token1 !== $token2,
            "Unicité des tokens sécurisés",
            "Chaque token doit être unique"
        );
        
        $this->assert(
            ctype_xdigit($token1),
            "Format token hexadécimal",
            "Le token doit être en format hexadécimal"
        );
    }
    
    /**
     * Test de sécurité des sessions
     */
    private function testSessionSecurity()
    {
        echo "🍪 Test de sécurité des sessions...\n";
        
        // Simulation d'une session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = 123;
        $_SESSION['user_role'] = 'stagiaire';
        
        // Test de vérification de rôle
        $this->assert(
            AuthMiddleware::hasRole('stagiaire'),
            "Vérification rôle correct",
            "Le rôle correct doit être reconnu"
        );
        
        $this->assert(
            !AuthMiddleware::hasRole('admin'),
            "Rejet rôle incorrect",
            "Un rôle incorrect doit être rejeté"
        );
        
        // Test de vérification d'utilisateur connecté
        $this->assert(
            AuthMiddleware::isLoggedIn(),
            "Détection utilisateur connecté",
            "Un utilisateur connecté doit être détecté"
        );
    }
    
    /**
     * Test de protection contre l'inclusion de fichiers
     */
    private function testFileInclusionProtection()
    {
        echo "📂 Test de protection inclusion de fichiers...\n";
        
        $maliciousPaths = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\config\\sam',
            '/etc/shadow',
            'php://filter/read=convert.base64-encode/resource=index.php'
        ];
        
        foreach ($maliciousPaths as $path) {
            $detected = SecurityUtils::detectMaliciousContent($path);
            $this->assert(
                $detected,
                "Détection chemin malveillant: " . substr($path, 0, 30) . "...",
                "Les chemins malveillants doivent être détectés"
            );
        }
    }
    
    /**
     * Assertion pour les tests
     */
    private function assert($condition, $testName, $message)
    {
        $this->testCount++;
        
        if ($condition) {
            $this->passedTests++;
            echo "  ✅ {$testName}\n";
            $this->results[] = ['status' => 'PASS', 'test' => $testName, 'message' => $message];
        } else {
            $this->failedTests++;
            echo "  ❌ {$testName} - {$message}\n";
            $this->results[] = ['status' => 'FAIL', 'test' => $testName, 'message' => $message];
        }
    }
    
    /**
     * Affiche les résultats des tests
     */
    private function displayResults()
    {
        echo "\n=== RÉSULTATS DES TESTS ===\n";
        echo "Total des tests: {$this->testCount}\n";
        echo "Tests réussis: {$this->passedTests}\n";
        echo "Tests échoués: {$this->failedTests}\n";
        
        $successRate = ($this->passedTests / $this->testCount) * 100;
        echo "Taux de réussite: " . number_format($successRate, 1) . "%\n";
        
        if ($this->failedTests > 0) {
            echo "\n❌ TESTS ÉCHOUÉS:\n";
            foreach ($this->results as $result) {
                if ($result['status'] === 'FAIL') {
                    echo "  - {$result['test']}: {$result['message']}\n";
                }
            }
        }
        
        if ($successRate >= 90) {
            echo "\n🎉 Excellent! La sécurité de l'application est robuste.\n";
        } elseif ($successRate >= 75) {
            echo "\n⚠️ Bon niveau de sécurité, mais quelques améliorations sont nécessaires.\n";
        } else {
            echo "\n🚨 Attention! Des problèmes de sécurité critiques ont été détectés.\n";
        }
        
        // Génération du rapport
        $this->generateReport();
    }
    
    /**
     * Génère un rapport de sécurité
     */
    private function generateReport()
    {
        $reportPath = __DIR__ . '/security_report_' . date('Y-m-d_H-i-s') . '.json';
        
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'total_tests' => $this->testCount,
            'passed_tests' => $this->passedTests,
            'failed_tests' => $this->failedTests,
            'success_rate' => ($this->passedTests / $this->testCount) * 100,
            'results' => $this->results,
            'recommendations' => $this->generateRecommendations()
        ];
        
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        echo "\n📄 Rapport de sécurité généré: {$reportPath}\n";
    }
    
    /**
     * Génère des recommandations de sécurité
     */
    private function generateRecommendations()
    {
        $recommendations = [
            'Effectuer des tests de sécurité réguliers',
            'Maintenir les dépendances à jour',
            'Utiliser HTTPS en production',
            'Configurer des en-têtes de sécurité HTTP',
            'Implémenter une politique de mots de passe forte',
            'Surveiller les logs de sécurité',
            'Effectuer des audits de code réguliers',
            'Former l\'équipe aux bonnes pratiques de sécurité'
        ];
        
        return $recommendations;
    }
}

// Exécution des tests si le script est appelé directement
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $tests = new SecurityTests();
    $tests->runAllTests();
}