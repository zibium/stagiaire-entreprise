<?php

namespace JobBoard\Controllers;

use JobBoard\Models\User;
use JobBoard\Models\OffreStage;
use JobBoard\Models\ProfilEntreprise;
use JobBoard\Models\ProfilStagiaire;
use PDO;

class AdminController
{
    private $pdo;
    private $userModel;
    private $offreModel;
    private $entrepriseModel;
    private $stagiaireModel;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        
        $this->userModel = new User($this->pdo);
        $this->offreModel = new OffreStage($this->pdo);
        $this->entrepriseModel = new ProfilEntreprise($this->pdo);
        $this->stagiaireModel = new ProfilStagiaire($this->pdo);
    }

    /**
     * Affiche le tableau de bord administrateur
     */
    public function showDashboard()
    {
        $stats = $this->getDashboardStats();
        $recentUsers = $this->getRecentUsers();
        $pendingOffers = $this->getPendingOffers();
        $recentActivity = $this->getRecentActivity();
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Affiche la gestion des utilisateurs
     */
    public function showUsers()
    {
        $users = $this->getAllUsers();
        $stats = $this->getUserStats();
        
        require_once __DIR__ . '/../views/admin/users.php';
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleUserStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            return;
        }

        $userId = $_POST['user_id'] ?? null;
        $action = $_POST['action'] ?? null;

        if (!$userId || !in_array($action, ['activate', 'deactivate'])) {
            $_SESSION['error'] = 'Paramètres invalides.';
            header('Location: /admin/users');
            return;
        }

        try {
            $status = ($action === 'activate') ? 'active' : 'inactive';
            $this->userModel->updateStatus($userId, $status);
            
            $_SESSION['success'] = 'Statut de l\'utilisateur mis à jour avec succès.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du statut.';
        }

        header('Location: /admin/users');
    }

    /**
     * Affiche la gestion des offres
     */
    public function showOffers()
    {
        $offers = $this->getAllOffers();
        $stats = $this->getOfferStats();
        
        require_once __DIR__ . '/../views/admin/offers.php';
    }

    /**
     * Valide ou rejette une offre
     */
    public function moderateOffer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/offers');
            return;
        }

        $offerId = $_POST['offer_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $reason = $_POST['reason'] ?? '';

        if (!$offerId || !in_array($action, ['approve', 'reject'])) {
            $_SESSION['error'] = 'Paramètres invalides.';
            header('Location: /admin/offers');
            return;
        }

        try {
            $status = ($action === 'approve') ? 'published' : 'rejected';
            $this->offreModel->updateStatus($offerId, $status, $reason);
            
            $_SESSION['success'] = 'Offre ' . ($action === 'approve' ? 'approuvée' : 'rejetée') . ' avec succès.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la modération de l\'offre.';
        }

        header('Location: /admin/offers');
    }

    /**
     * Affiche les statistiques globales
     */
    public function showStatistics()
    {
        $globalStats = $this->getGlobalStatistics();
        $chartData = $this->getChartData();
        
        require_once __DIR__ . '/../views/admin/statistics.php';
    }

    /**
     * Affiche les logs d'activité
     */
    public function showLogs()
    {
        $logs = $this->getActivityLogs();
        
        require_once __DIR__ . '/../views/admin/logs.php';
    }

    /**
     * Exporte les données
     */
    public function exportData()
    {
        $type = $_GET['type'] ?? 'users';
        $format = $_GET['format'] ?? 'csv';

        try {
            switch ($type) {
                case 'users':
                    $data = $this->getAllUsers();
                    break;
                case 'offers':
                    $data = $this->getAllOffers();
                    break;
                case 'applications':
                    $data = $this->getAllApplications();
                    break;
                default:
                    throw new Exception('Type d\'export invalide');
            }

            $this->generateExport($data, $type, $format);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de l\'export: ' . $e->getMessage();
            header('Location: /admin/dashboard');
        }
    }

    // Méthodes privées pour récupérer les données

    private function getDashboardStats()
    {
        return [
            'total_users' => $this->userModel->getTotalCount(),
            'total_companies' => $this->userModel->getCountByRole(User::ROLE_ENTREPRISE),
            'total_interns' => $this->userModel->getCountByRole(User::ROLE_STAGIAIRE),
            'total_offers' => $this->offreModel->getTotalCount(),
            'pending_offers' => $this->offreModel->getCountByStatus('pending'),
            'active_offers' => $this->offreModel->getCountByStatus('published'),
            'total_applications' => $this->offreModel->getTotalApplications(),
            'new_users_today' => $this->userModel->getNewUsersToday(),
            'new_offers_today' => $this->offreModel->getNewOffersToday()
        ];
    }

    private function getRecentUsers($limit = 5)
    {
        return $this->userModel->getRecent($limit);
    }

    private function getPendingOffers($limit = 5)
    {
        return $this->offreModel->getPendingOffers($limit);
    }

    private function getRecentActivity($limit = 10)
    {
        // Simulation des logs d'activité récente
        return [
            ['action' => 'Nouvelle inscription', 'user' => 'Jean Dupont', 'time' => '2025-01-15 10:30:00'],
            ['action' => 'Offre créée', 'user' => 'TechCorp', 'time' => '2025-01-15 09:15:00'],
            ['action' => 'Candidature soumise', 'user' => 'Marie Martin', 'time' => '2025-01-15 08:45:00']
        ];
    }

    private function getAllUsers()
    {
        return $this->userModel->getAllWithProfiles();
    }

    private function getUserStats()
    {
        return [
            'total' => $this->userModel->getTotalCount(),
            'active' => $this->userModel->getCountByStatus('active'),
            'inactive' => $this->userModel->getCountByStatus('inactive'),
            'companies' => $this->userModel->getCountByRole(User::ROLE_ENTREPRISE),
            'interns' => $this->userModel->getCountByRole(User::ROLE_STAGIAIRE)
        ];
    }

    private function getAllOffers()
    {
        return $this->offreModel->getAllWithCompany();
    }

    private function getOfferStats()
    {
        return [
            'total' => $this->offreModel->getTotalCount(),
            'pending' => $this->offreModel->getCountByStatus('pending'),
            'published' => $this->offreModel->getCountByStatus('published'),
            'rejected' => $this->offreModel->getCountByStatus('rejected'),
            'expired' => $this->offreModel->getExpiredCount()
        ];
    }

    private function getGlobalStatistics()
    {
        return [
            'users_by_month' => $this->userModel->getUsersByMonth(),
            'offers_by_month' => $this->offreModel->getOffersByMonth(),
            'applications_by_month' => $this->offreModel->getApplicationsByMonth(),
            'top_domains' => $this->offreModel->getTopDomains(),
            'success_rate' => $this->calculateSuccessRate()
        ];
    }

    private function getChartData()
    {
        return [
            'users_growth' => $this->userModel->getGrowthData(),
            'offers_distribution' => $this->offreModel->getDistributionData(),
            'applications_trends' => $this->offreModel->getApplicationTrends()
        ];
    }

    private function getActivityLogs($limit = 50)
    {
        // Simulation des logs - à remplacer par une vraie table de logs
        return [
            ['id' => 1, 'action' => 'LOGIN', 'user_id' => 1, 'details' => 'Connexion admin', 'created_at' => '2025-01-15 10:30:00'],
            ['id' => 2, 'action' => 'OFFER_APPROVED', 'user_id' => 1, 'details' => 'Offre #123 approuvée', 'created_at' => '2025-01-15 10:25:00']
        ];
    }

    private function getAllApplications()
    {
        return $this->offreModel->getAllApplications();
    }

    private function calculateSuccessRate()
    {
        $totalApplications = $this->offreModel->getTotalApplications();
        $acceptedApplications = $this->offreModel->getAcceptedApplications();
        
        return $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 2) : 0;
    }

    private function generateExport($data, $type, $format)
    {
        $filename = $type . '_' . date('Y-m-d_H-i-s') . '.' . $format;
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        if ($format === 'csv') {
            $output = fopen('php://output', 'w');
            
            if (!empty($data)) {
                // En-têtes
                fputcsv($output, array_keys($data[0]));
                
                // Données
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
        } else {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        exit;
    }
}