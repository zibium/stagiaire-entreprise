<?php

namespace JobBoard\Controllers;

use JobBoard\Models\ProfilEntreprise;
use JobBoard\Models\OffreStage;
use JobBoard\Models\Candidature;
use JobBoard\Middleware\AuthMiddleware;
use Exception;
use PDO;

/**
 * Contrôleur Entreprise
 * Gestion des fonctionnalités entreprise
 */
class EntrepriseController
{
    private $profilEntreprise;
    private $offreStage;
    private $candidature;
    private $pdo;
    
    public function __construct()
    {
        // Configuration de la base de données
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        $this->profilEntreprise = new ProfilEntreprise($this->pdo);
        $this->offreStage = new OffreStage($this->pdo);
        $this->candidature = new Candidature($this->pdo);
    }
    
    /**
     * Afficher le tableau de bord entreprise
     */
    public function showDashboard()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil de l'entreprise
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                // Rediriger vers la création de profil si pas encore créé
                require_once __DIR__ . '/../utils/UrlHelper.php';
                header('Location: ' . \UrlHelper::url('entreprise/profile'));
                exit;
            }
            
            // Récupérer les statistiques
            $stats = $this->getEntrepriseStats($profil['id']);
            
            // Récupérer les offres récentes
            $offresRecentes = $this->offreStage->findByEntreprise($profil['id'], 1, 5);
            
            // Récupérer les candidatures récentes
            $candidaturesRecentes = $this->candidature->getRecentByEntreprise($profil['id'], 5);
            
            // Récupérer les offres expirant bientôt
            $offresExpirant = $this->offreStage->getExpiringSoon($profil['id']);
            
            // Données pour la vue
            $data = [
                'profil' => $profil,
                'stats' => $stats,
                'offres_recentes' => $offresRecentes,
                'candidatures_recentes' => $candidaturesRecentes,
                'offres_expirant' => $offresExpirant,
                'completion_percentage' => $this->profilEntreprise->getCompletionPercentage($profil['id'])
            ];
            
            $this->render('entreprise/dashboard', $data);
            
        } catch (Exception $e) {
            error_log("Erreur dashboard entreprise: " . $e->getMessage());
            $this->render('error', ['message' => 'Une erreur est survenue']);
        }
    }
    
    /**
     * Afficher le profil entreprise
     */
    public function showProfile()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil existant
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            // Données pour la vue
            $data = [
                'profil' => $profil,
                'secteurs' => $this->getSecteurs(),
                'tailles' => $this->getTaillesEntreprise()
            ];
            
            $this->render('entreprise/profile', $data);
            
        } catch (Exception $e) {
            error_log("Erreur affichage profil entreprise: " . $e->getMessage());
            $this->render('error', ['message' => 'Une erreur est survenue']);
        }
    }
    
    /**
     * Mettre à jour le profil entreprise
     */
    public function updateProfile()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /entreprise/profile');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer les données du formulaire
            $data = [
                'user_id' => $userId,
                'nom_entreprise' => trim($_POST['nom_entreprise'] ?? ''),
                'secteur_activite' => trim($_POST['secteur_activite'] ?? ''),
                'taille_entreprise' => trim($_POST['taille_entreprise'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'adresse' => trim($_POST['adresse'] ?? ''),
                'ville' => trim($_POST['ville'] ?? ''),
                'code_postal' => trim($_POST['code_postal'] ?? ''),
                'telephone' => trim($_POST['telephone'] ?? ''),
                'site_web' => trim($_POST['site_web'] ?? ''),
                'linkedin' => trim($_POST['linkedin'] ?? ''),
                'contact_nom' => trim($_POST['contact_nom'] ?? ''),
                'contact_prenom' => trim($_POST['contact_prenom'] ?? ''),
                'contact_fonction' => trim($_POST['contact_fonction'] ?? ''),
                'contact_email' => trim($_POST['contact_email'] ?? ''),
                'contact_telephone' => trim($_POST['contact_telephone'] ?? '')
            ];
            
            // Valider les données
            $errors = $this->profilEntreprise->validateProfileData($data);
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Vérifier si le profil existe déjà
            $profilExistant = $this->profilEntreprise->findByUserId($userId);
            
            // Debug logging - simplified to avoid server crashes
            try {
                $logFile = __DIR__ . '/../../logs/debug.log';
                if (!file_exists(dirname($logFile))) {
                    @mkdir(dirname($logFile), 0777, true);
                }
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Profile update attempt for user ID: " . $userId . "\n", FILE_APPEND);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Existing profile: " . ($profilExistant ? 'found' : 'not found') . "\n", FILE_APPEND);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Data to update: " . json_encode($data) . "\n", FILE_APPEND);
            } catch (Exception $logError) {
                // Ignore logging errors to prevent server crashes
            }
            
            if ($profilExistant) {
                // Mettre à jour le profil existant
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Updating existing profile with ID: " . $profilExistant['id'] . "\n", FILE_APPEND);
                $result = $this->profilEntreprise->update($profilExistant['id'], $data);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Update result: " . ($result ? 'success' : 'failed') . "\n", FILE_APPEND);
            } else {
                // Créer un nouveau profil
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Creating new profile\n", FILE_APPEND);
                $result = $this->profilEntreprise->create($data);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Create result: " . ($result ? 'success' : 'failed') . "\n", FILE_APPEND);
            }
            
            if ($result) {
                $_SESSION['success'] = 'Profil mis à jour avec succès';
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Success message set in session\n", FILE_APPEND);
            } else {
                $_SESSION['errors'] = ['Erreur lors de la mise à jour du profil'];
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Error message set in session\n", FILE_APPEND);
            }
            
        } catch (Exception $e) {
            try {
                $logFile = __DIR__ . '/../../logs/debug.log';
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            } catch (Exception $logError) {
                // Ignore logging errors
            }
            $_SESSION['errors'] = ['Une erreur est survenue lors de la mise à jour'];
        }
        
        header('Location: /entreprise/profile');
        exit;
    }
    
    /**
     * Gérer l'upload du logo
     */
    public function uploadLogo()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /entreprise/profile');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                $_SESSION['errors'] = ['Profil non trouvé'];
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Vérifier si un fichier a été uploadé
            if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['errors'] = ['Aucun fichier sélectionné ou erreur d\'upload'];
                header('Location: /entreprise/profile');
                exit;
            }
            
            $file = $_FILES['logo'];
            
            // Valider le fichier
            $validation = $this->validateLogoFile($file);
            if (!$validation['valid']) {
                $_SESSION['errors'] = $validation['errors'];
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Générer un nom unique pour le fichier
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'logo_' . $profil['id'] . '_' . time() . '.' . $extension;
            $uploadPath = 'uploads/logos/' . $fileName;
            $fullPath = __DIR__ . '/../../public/' . $uploadPath;
            
            // Créer le dossier s'il n'existe pas
            $uploadDir = dirname($fullPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Supprimer l'ancien logo s'il existe
            if (!empty($profil['logo_path'])) {
                $oldLogoPath = __DIR__ . '/../../public/' . $profil['logo_path'];
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            
            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                // Mettre à jour le chemin dans la base de données
                $result = $this->profilEntreprise->updateLogoPath($profil['id'], $uploadPath);
                
                if ($result) {
                    $_SESSION['success'] = 'Logo mis à jour avec succès';
                } else {
                    $_SESSION['errors'] = ['Erreur lors de la mise à jour du logo en base de données'];
                    // Supprimer le fichier uploadé en cas d'erreur
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            } else {
                $_SESSION['errors'] = ['Erreur lors de l\'upload du fichier'];
            }
            
        } catch (Exception $e) {
            error_log("Erreur upload logo: " . $e->getMessage());
            $_SESSION['errors'] = ['Une erreur est survenue lors de l\'upload'];
        }
        
        header('Location: /entreprise/profile');
        exit;
    }
    
    /**
     * Supprimer le logo
     */
    public function deleteLogo()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil || empty($profil['logo_path'])) {
                $_SESSION['errors'] = ['Aucun logo à supprimer'];
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Supprimer le fichier
            $logoPath = __DIR__ . '/../../public/' . $profil['logo_path'];
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
            
            // Mettre à jour la base de données
            $result = $this->profilEntreprise->updateLogoPath($profil['id'], null);
            
            if ($result) {
                $_SESSION['success'] = 'Logo supprimé avec succès';
            } else {
                $_SESSION['errors'] = ['Erreur lors de la suppression du logo'];
            }
            
        } catch (Exception $e) {
            error_log("Erreur suppression logo: " . $e->getMessage());
            $_SESSION['errors'] = ['Une erreur est survenue lors de la suppression'];
        }
        
        header('Location: /entreprise/profile');
        exit;
    }
    
    /**
     * Afficher les offres de l'entreprise
     */
    public function showOffers()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil de l'entreprise
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Pagination
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 10;
            
            // Récupérer les offres
            $offres = $this->offreStage->findByEntreprise($profil['id'], $page, $limit);
            
            // Compter le total pour la pagination
            $totalOffres = $this->offreStage->countByEntreprise($profil['id']);
            $totalPages = ceil($totalOffres / $limit);
            
            // Données pour la vue
            $data = [
                'offres' => $offres,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_offres' => $totalOffres,
                'profil' => $profil
            ];
            
            $this->render('entreprise/offers', $data);
            
        } catch (Exception $e) {
            error_log("Erreur affichage offres entreprise: " . $e->getMessage());
            $this->render('error', ['message' => 'Une erreur est survenue']);
        }
    }
    
    /**
     * Afficher le formulaire de création d'offre
     */
    public function showCreateOffer()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        // Données pour la vue
        $data = [
            'domaines' => $this->getDomaines(),
            'niveaux_etude' => $this->getNiveauxEtude(),
            'types_contrat' => $this->getTypesContrat()
        ];
        
        $this->render('entreprise/create-offer', $data);
    }
    
    /**
     * Créer une nouvelle offre
     */
    public function createOffer()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /entreprise/offers/create');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil de l'entreprise
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                $_SESSION['errors'] = ['Profil entreprise non trouvé'];
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Récupérer les données du formulaire
            $data = [
                'entreprise_id' => $profil['id'],
                'titre' => trim($_POST['titre'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'competences_requises' => trim($_POST['competences_requises'] ?? ''),
                'type_contrat' => trim($_POST['type_contrat'] ?? ''),
                'duree' => trim($_POST['duree'] ?? ''),
                'remuneration' => !empty($_POST['remuneration']) ? floatval($_POST['remuneration']) : null,
                'lieu' => trim($_POST['lieu'] ?? ''),
                'ville' => trim($_POST['ville'] ?? ''),
                'code_postal' => trim($_POST['code_postal'] ?? ''),
                'date_debut' => trim($_POST['date_debut'] ?? ''),
                'date_fin' => !empty($_POST['date_fin']) ? trim($_POST['date_fin']) : null,
                'date_limite_candidature' => trim($_POST['date_limite_candidature'] ?? ''),
                'niveau_etude' => trim($_POST['niveau_etude'] ?? ''),
                'domaine' => trim($_POST['domaine'] ?? ''),
                'statut' => 'en_attente' // Par défaut en attente de validation
            ];
            
            // Valider les données
            $errors = $this->offreStage->validateOfferData($data);
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
                header('Location: /entreprise/offers/create');
                exit;
            }
            
            // Créer l'offre
            $offreId = $this->offreStage->create($data);
            
            if ($offreId) {
                $_SESSION['success'] = 'Offre créée avec succès. Elle sera publiée après validation.';
                header('Location: /entreprise/offers');
            } else {
                $_SESSION['errors'] = ['Erreur lors de la création de l\'offre'];
                header('Location: /entreprise/offers/create');
            }
            
        } catch (Exception $e) {
            error_log("Erreur création offre: " . $e->getMessage());
            $_SESSION['errors'] = ['Une erreur est survenue lors de la création'];
            header('Location: /entreprise/offers/create');
        }
        
        exit;
    }
    
    /**
     * Afficher les candidatures reçues
     */
    public function showApplications()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil de l'entreprise
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                header('Location: /entreprise/profile');
                exit;
            }
            
            // Pagination
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 10;
            
            // Filtres
            $filters = [
                'statut' => $_GET['statut'] ?? '',
                'offre_id' => $_GET['offre_id'] ?? ''
            ];
            
            // Récupérer les candidatures
            $candidatures = $this->candidature->findByEntreprise($profil['id'], $filters, $page, $limit);
            
            // Récupérer les offres pour le filtre
            $offres = $this->offreStage->findByEntreprise($profil['id'], 1, 100);
            
            // Compter le total pour la pagination
            $totalCandidatures = $this->candidature->countByEntreprise($profil['id'], $filters);
            $totalPages = ceil($totalCandidatures / $limit);
            
            // Données pour la vue
            $data = [
                'candidatures' => $candidatures,
                'offres' => $offres,
                'filters' => $filters,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_candidatures' => $totalCandidatures,
                'profil' => $profil
            ];
            
            $this->render('entreprise/applications', $data);
            
        } catch (Exception $e) {
            error_log("Erreur affichage candidatures entreprise: " . $e->getMessage());
            $this->render('error', ['message' => 'Une erreur est survenue']);
        }
    }
    
    /**
     * Obtenir les statistiques de l'entreprise
     */
    private function getEntrepriseStats($entrepriseId)
    {
        $stats = [];
        
        // Statistiques des offres
        $offreStats = $this->offreStage->getStatistics($entrepriseId);
        $stats['offres'] = $offreStats;
        
        // Statistiques des candidatures
        $candidatureStats = $this->candidature->getStatistics($entrepriseId);
        $stats['candidatures'] = $candidatureStats;
        
        return $stats;
    }
    
    /**
     * Valider un fichier logo
     */
    private function validateLogoFile($file)
    {
        $errors = [];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            $errors[] = 'Le fichier est trop volumineux (maximum 2MB)';
        }
        
        // Vérifier le type MIME
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Type de fichier non autorisé (JPG, PNG, GIF, WebP uniquement)';
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'Extension de fichier non autorisée';
        }
        
        // Vérifier que c'est vraiment une image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors[] = 'Le fichier n\'est pas une image valide';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Obtenir la liste des secteurs d'activité
     */
    private function getSecteurs()
    {
        return [
            'informatique' => 'Informatique / Numérique',
            'commerce' => 'Commerce / Vente',
            'marketing' => 'Marketing / Communication',
            'finance' => 'Finance / Comptabilité',
            'rh' => 'Ressources Humaines',
            'industrie' => 'Industrie / Production',
            'sante' => 'Santé / Social',
            'education' => 'Éducation / Formation',
            'juridique' => 'Juridique / Droit',
            'batiment' => 'Bâtiment / Travaux Publics',
            'transport' => 'Transport / Logistique',
            'tourisme' => 'Tourisme / Hôtellerie',
            'agriculture' => 'Agriculture / Agroalimentaire',
            'energie' => 'Énergie / Environnement',
            'media' => 'Médias / Culture',
            'autre' => 'Autre'
        ];
    }
    
    /**
     * Obtenir la liste des tailles d'entreprise
     */
    private function getTaillesEntreprise()
    {
        return [
            'tpe' => 'TPE (1-9 salariés)',
            'pme' => 'PME (10-249 salariés)',
            'eti' => 'ETI (250-4999 salariés)',
            'ge' => 'Grande entreprise (5000+ salariés)'
        ];
    }
    
    /**
     * Obtenir la liste des domaines
     */
    private function getDomaines()
    {
        return [
            'informatique' => 'Informatique',
            'web' => 'Développement Web',
            'mobile' => 'Développement Mobile',
            'data' => 'Data Science / IA',
            'cybersecurite' => 'Cybersécurité',
            'reseaux' => 'Réseaux / Systèmes',
            'marketing' => 'Marketing Digital',
            'design' => 'Design / UX/UI',
            'commerce' => 'Commerce',
            'finance' => 'Finance',
            'rh' => 'Ressources Humaines',
            'juridique' => 'Juridique',
            'communication' => 'Communication',
            'autre' => 'Autre'
        ];
    }
    
    /**
     * Obtenir la liste des niveaux d'étude
     */
    private function getNiveauxEtude()
    {
        return [
            'bac' => 'Bac',
            'bac+1' => 'Bac+1',
            'bac+2' => 'Bac+2 (BTS/DUT)',
            'bac+3' => 'Bac+3 (Licence)',
            'bac+4' => 'Bac+4 (Master 1)',
            'bac+5' => 'Bac+5 (Master 2)',
            'bac+6' => 'Bac+6 et plus'
        ];
    }
    
    /**
     * Obtenir la liste des types de contrat
     */
    private function getTypesContrat()
    {
        return [
            'stage' => 'Stage',
            'apprentissage' => 'Apprentissage',
            'alternance' => 'Alternance'
        ];
    }
    
    /**
     * Calculer le pourcentage de complétion du profil
     */
    private function calculateCompletion($profil)
    {
        $fields = [
            'nom_entreprise',
            'secteur_activite',
            'taille_entreprise',
            'adresse',
            'ville',
            'code_postal',
            'telephone',
            'site_web',
            'description'
        ];
        
        $completed = 0;
        $total = count($fields);
        
        foreach ($fields as $field) {
            if (!empty($profil[$field])) {
                $completed++;
            }
        }
        
        $percentage = round(($completed / $total) * 100);
        
        return [
            'percentage' => $percentage,
            'completed' => $completed,
            'total' => $total,
            'missing_fields' => array_filter($fields, function($field) use ($profil) {
                return empty($profil[$field]);
            })
        ];
    }
    
    /**
     * Afficher les statistiques de l'entreprise
     */
    public function showStatistics()
    {
        // Vérifier l'authentification et le rôle
        AuthMiddleware::requireEntreprise();
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Récupérer le profil de l'entreprise
            $profil = $this->profilEntreprise->findByUserId($userId);
            
            if (!$profil) {
                header('Location: /entreprise/profile');
                exit;
            }
            
            $entrepriseId = $profil['id'];
            
            // Récupérer les statistiques
            $stats = $this->getStatisticsData($entrepriseId);
            
            // Rendre la vue
            $this->render('entreprise/statistics', [
                'profil' => $profil,
                'stats' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur statistiques entreprise: " . $e->getMessage());
            $this->render('error', ['message' => 'Erreur lors du chargement des statistiques']);
        }
    }
    
    /**
     * Récupérer les données statistiques pour l'entreprise
     */
    private function getStatisticsData($entrepriseId)
    {
        // Statistiques des offres
        $totalOffers = $this->offreStage->countByEntreprise($entrepriseId);
        $activeOffers = $this->offreStage->countByEntreprise($entrepriseId, ['statut' => 'publiee']);
        $pendingOffers = $this->offreStage->countByEntreprise($entrepriseId, ['statut' => 'en_attente']);
        
        // Statistiques des candidatures
        $totalApplications = $this->candidature->countByEntreprise($entrepriseId);
        $pendingApplications = $this->candidature->countByEntreprise($entrepriseId, ['statut' => 'en_attente']);
        $acceptedApplications = $this->candidature->countByEntreprise($entrepriseId, ['statut' => 'acceptee']);
        $rejectedApplications = $this->candidature->countByEntreprise($entrepriseId, ['statut' => 'refusee']);
        
        // Statistiques détaillées des offres
        $offerStats = $this->offreStage->getStatistics($entrepriseId);
        
        return [
            'offers' => [
                'total' => $totalOffers,
                'active' => $activeOffers,
                'pending' => $pendingOffers,
                'details' => $offerStats
            ],
            'applications' => [
                'total' => $totalApplications,
                'pending' => $pendingApplications,
                'accepted' => $acceptedApplications,
                'rejected' => $rejectedApplications
            ]
        ];
    }
    
    /**
     * Rendre une vue
     */
    private function render($view, $data = [])
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Inclure la vue
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \Exception("Vue non trouvée: {$view}");
        }
    }
}