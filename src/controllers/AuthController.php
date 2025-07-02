<?php

namespace JobBoard\Controllers;

use JobBoard\Models\User;
use JobBoard\Utils\UrlHelper;
use Exception;
use PDO;

class AuthController
{
    private $user;
    private $pdo;
    
    public function __construct()
    {
        // Configuration de la base de données
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        $this->user = new User($this->pdo);
    }
    
    /**
     * Affiche la page de connexion (sélection du type)
     */
    public function showLogin()
    {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $authController = $this;

        require_once __DIR__ . '/../views/includes/header.php';
        include __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Affiche la page de connexion pour les entreprises
     */
    public function showLoginEntreprise()
    {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $authController = $this;
        require_once __DIR__ . '/../views/includes/header.php';
        include __DIR__ . '/../views/auth/login-entreprise.php';
    }
    
    /**
     * Affiche la page de connexion pour les stagiaires
     */
    public function showLoginStagiaire()
    {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $authController = $this;
        require_once __DIR__ . '/../views/includes/header.php';
        include __DIR__ . '/../views/auth/login-stagiaire.php';
    }
    
    /**
     * Traite la connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(UrlHelper::url('auth/login'));
            return;
        }
        
        // Vérification CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            $this->redirect('/auth/login');
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            $this->redirect('/auth/login');
            return;
        }
        
        try {
            $user = $this->user->authenticate($email, $password);
            
            if ($user) {
                // Vérifier si le compte est actif
                if (!$user['is_active']) {
                    $_SESSION['error'] = 'Votre compte est désactivé. Contactez l\'administrateur.';
                    $this->redirect('/auth/login');
                    return;
                }
                
                // Démarrer la session
                $this->startUserSession($user, $remember);
                
                $_SESSION['success'] = 'Connexion réussie !';
                $this->redirectToDashboard();
            } else {
                $_SESSION['error'] = 'Email ou mot de passe incorrect.';
                $this->redirect('/auth/login');
            }
        } catch (Exception $e) {
            error_log('Erreur de connexion: ' . $e->getMessage());
            $_SESSION['error'] = 'Une erreur est survenue lors de la connexion.';
            $this->redirect('/auth/login');
        }
    }
    
    /**
     * Affiche la page d'inscription
     */
    public function showRegister()
    {
        // Rediriger si déjà connecté
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }
        
        $authController = $this;
        require_once __DIR__ . '/../views/includes/header.php';
        include __DIR__ . '/../views/auth/register.php';
    }
    
    /**
     * Traite l'inscription
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/register');
            return;
        }
        
        // Vérification CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            $this->redirect('/auth/register');
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';
        $acceptTerms = isset($_POST['accept_terms']);
        
        // Validation
        $errors = $this->validateRegistration($email, $password, $confirmPassword, $role, $acceptTerms);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/auth/register');
            return;
        }
        
        try {
            $userId = $this->user->create($email, $password, $role);
            
            if ($userId) {
                $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                $this->redirect('/auth/login');
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'inscription.';
                $this->redirect('/auth/register');
            }
        } catch (Exception $e) {
            error_log('Erreur d\'inscription: ' . $e->getMessage());
            
            if (strpos($e->getMessage(), 'existe déjà') !== false) {
                $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription.';
            }
            
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/auth/register');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout()
    {
        // Détruire la session
        session_destroy();
        
        // Supprimer le cookie "remember me" si présent
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        // Rediriger vers la page d'accueil
        $this->redirect(UrlHelper::url(''));
    }
    
    /**
     * Vérification de l'email (activation du compte)
     */
    public function verifyEmail()
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Token de vérification manquant.';
            $this->redirect(UrlHelper::url('auth/login'));
            return;
        }
        
        try {
            $result = $this->user->verifyEmail($token);
            
            if ($result) {
                $_SESSION['success'] = 'Votre email a été vérifié avec succès ! Vous pouvez maintenant vous connecter.';
            } else {
                $_SESSION['error'] = 'Token de vérification invalide ou expiré.';
            }
        } catch (Exception $e) {
            error_log('Erreur de vérification email: ' . $e->getMessage());
            $_SESSION['error'] = 'Une erreur est survenue lors de la vérification.';
        }
        
        $this->redirect('/auth/login');
    }
    
    /**
     * Démarre la session utilisateur
     */
    private function startUserSession($user, $remember = false)
    {
        // Régénérer l'ID de session pour la sécurité
        session_regenerate_id(true);
        
        // Stocker les informations utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Gestion du "Remember Me"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 jours
            
            // Stocker le token en base (à implémenter dans User model)
            // $this->user->setRememberToken($user['id'], $token, $expiry);
            
            setcookie('remember_token', $token, $expiry, '/', '', true, true);
        }
    }
    
    /**
     * Valide les données d'inscription
     */
    private function validateRegistration($email, $password, $confirmPassword, $role, $acceptTerms)
    {
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
        $validRoles = [User::ROLE_STAGIAIRE, User::ROLE_ENTREPRISE];
        if (empty($role) || !in_array($role, $validRoles)) {
            $errors[] = 'Veuillez sélectionner un type de compte valide.';
        }
        
        // Acceptation des conditions
        if (!$acceptTerms) {
            $errors[] = 'Vous devez accepter les conditions d\'utilisation.';
        }
        
        return $errors;
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    private function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Redirige vers le tableau de bord approprié selon le rôle
     */
    private function redirectToDashboard()
    {
        $role = $_SESSION['user_role'] ?? '';
        
        switch ($role) {
            case User::ROLE_ADMIN:
                $this->redirect(UrlHelper::url('admin/dashboard'));
                break;
            case User::ROLE_ENTREPRISE:
                $this->redirect(UrlHelper::url('entreprise/dashboard'));
                break;
            case User::ROLE_STAGIAIRE:
                $this->redirect(UrlHelper::url('stagiaire/dashboard'));
                break;
            default:
                $this->redirect(UrlHelper::url(''));
        }
    }
    
    /**
     * Vérifie le token CSRF
     */
    private function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Génère un token CSRF
     */
    public function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Redirection vers une URL
     */
    private function redirect($url)
    {
        header('Location: ' . UrlHelper::url($url));
        exit;
    }
}