<?php

namespace JobBoard\Controllers;

use JobBoard\Models\User;
use JobBoard\Models\ProfilStagiaire;
use JobBoard\Models\OffreStage;
use JobBoard\Models\Candidature;
use JobBoard\Middleware\AuthMiddleware;
use JobBoard\Utils\UrlHelper;
use PDO;

class StagiaireController
{
    private $userModel;
    private $profilModel;
    private $offreModel;
    private $candidatureModel;
    private $pdo;

    public function __construct()
    {
        // Configuration de la base de données
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        $this->userModel = new User($this->pdo);
        $this->profilModel = new ProfilStagiaire($this->pdo);
        $this->offreModel = new OffreStage();
        $this->candidatureModel = new Candidature($this->pdo);
    }

    /**
     * Afficher le tableau de bord stagiaire
     */
    public function dashboard()
    {
        AuthMiddleware::requireStagiaire();

        $userId = $_SESSION['user_id'];
        $profil = $this->profilModel->findByUserId($userId);

        $stats = [
            'profil_complete' => !empty($profil),
            'cv_uploaded' => !empty($profil['cv_path']),
            'candidatures_envoyees' => $this->candidatureModel->countByUser($userId),
            'candidatures_en_attente' => $this->candidatureModel->countByStatus($userId, 'en_attente'),
            'completionPercentage' => ($profil ? 50 : 0) + (!empty($profil['cv_path']) ? 50 : 0)
        ];

        extract($stats);
        include __DIR__ . '/../views/stagiaire/dashboard.php';
    }

    public function offres()
    {
        AuthMiddleware::requireStagiaire();
        
        // Récupérer les paramètres de recherche
        $filters = [
            'keywords' => $_GET['search'] ?? '',
            'domaine' => $_GET['domaine'] ?? '',
            'ville' => $_GET['ville'] ?? '',
            'type_contrat' => $_GET['type_contrat'] ?? '',
            'duree_min' => $_GET['duree_min'] ?? '',
            'remuneration' => $_GET['remuneration'] ?? ''
        ];
        
        // Pagination
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 12;
        
        // Récupérer les offres avec filtres
        $offreModel = new \JobBoard\Models\OffreStage();
        
        // Préparer les filtres pour la recherche
        $searchFilters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });
        
        $offres = $offreModel->search($searchFilters, $page, $limit);
        $totalOffres = $offreModel->countSearch($searchFilters);
        $totalPages = ceil($totalOffres / $limit);
        
        // Récupérer les options pour les filtres
        $domaines = $offreModel->getDistinctDomaines();
        $villes = $offreModel->getDistinctVilles();
        $typesContrat = $offreModel->getDistinctTypesContrat();
        
        // Récupérer les candidatures de l'utilisateur
        $candidatureModel = new \JobBoard\Models\Candidature();
        $candidatures = $candidatureModel->findByStagiaire($_SESSION['user_id']);
        
        // Variables pour la vue
        $currentPage = $page;
        
        include __DIR__ . '/../views/stagiaire/offres.php';
    }

    public function candidatures()
    {
        AuthMiddleware::requireStagiaire();
        include __DIR__ . '/../views/stagiaire/candidatures.php';
    }

    public function profile()
    {
        AuthMiddleware::requireStagiaire();
        include __DIR__ . '/../views/stagiaire/profile.php';
    }

    /**
     * Afficher la page d'upload de CV
     */
    public function uploadCV()
    {
        AuthMiddleware::requireStagiaire();
        
        $userId = $_SESSION['user_id'];
        $profil = $this->profilModel->findByUserId($userId);
        
        // Variables pour le layout
        $pageTitle = 'Upload CV - JobBoard';
        $pageDescription = 'Téléchargez votre CV';
        $currentPage = 'upload-cv';
        
        // Capturer le contenu de la page
        ob_start();
        include __DIR__ . '/../views/stagiaire/upload-cv.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Traiter l'upload de CV
     */
    public function handleUploadCV()
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/upload-cv');
            exit;
        }

        // Vérification CSRF
        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            header('Location: /stagiaire/upload-cv');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Vérifier si un fichier a été téléchargé
        if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Erreur lors du téléchargement du fichier.';
            header('Location: /stagiaire/upload-cv');
            exit;
        }

        $file = $_FILES['cv_file'];

        // Valider le fichier
        $validation = $this->validateCvFile($file);
        if ($validation !== true) {
            $_SESSION['error'] = $validation;
            header('Location: /stagiaire/upload-cv');
            exit;
        }

        // Créer le dossier de destination s'il n'existe pas
        $uploadDir = __DIR__ . '/../../uploads/cv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'cv_' . $userId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Déplacer le fichier téléchargé
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $_SESSION['error'] = 'Erreur lors de la sauvegarde du fichier.';
            header('Location: /stagiaire/upload-cv');
            exit;
        }

        try {
            // Mettre à jour le profil avec le chemin du CV
            $profil = $this->profilModel->findByUserId($userId);
            $cvPath = 'cv/' . $filename;

            if ($profil) {
                // Supprimer l'ancien CV s'il existe
                if (!empty($profil['cv_path'])) {
                    $oldCvPath = __DIR__ . '/../../uploads/' . $profil['cv_path'];
                    if (file_exists($oldCvPath)) {
                        unlink($oldCvPath);
                    }
                }

                // Mettre à jour le profil existant
                $result = $this->profilModel->update($profil['id'], ['cv_path' => $cvPath]);
            } else {
                // Créer un nouveau profil avec le CV
                $data = [
                    'user_id' => $userId,
                    'cv_path' => $cvPath
                ];
                $result = $this->profilModel->create($data);
            }

            if ($result) {
                $_SESSION['success'] = 'CV téléchargé avec succès.';
            } else {
                $_SESSION['error'] = 'Erreur lors de la sauvegarde en base de données.';
                // Supprimer le fichier téléchargé en cas d'erreur
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
        } catch (Exception $e) {
            error_log('CV upload error: ' . $e->getMessage());
            $_SESSION['error'] = 'Erreur lors de la sauvegarde du CV.';
            // Supprimer le fichier téléchargé en cas d'erreur
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        header('Location: /stagiaire/upload-cv');
        exit;
    }

    /**
     * Afficher le formulaire de profil
     */
    public function showProfile()
    {
        AuthMiddleware::requireStagiaire();

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        $profil = $this->profilModel->findByUserId($userId);

        // Variables pour le layout
        $pageTitle = 'Mon Profil - JobBoard';
        $pageDescription = 'Gérez votre profil stagiaire';
        $currentPage = 'profile';

        // Capturer le contenu de la page
        ob_start();
        include __DIR__ . '/../views/stagiaire/profile.php';
        $content = ob_get_clean();

        include __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Traiter la mise à jour du profil
     */
    public function updateProfile()
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/profile');
            exit;
        }

        // Vérification CSRF
        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            header('Location: /stagiaire/profile');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Préparer les données
        $data = [
            'user_id' => $userId,
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'date_naissance' => $_POST['date_naissance'] ?? null,
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'niveau_etudes' => $_POST['niveau_etudes'] ?? null,
            'domaine_etudes' => trim($_POST['domaine_etudes'] ?? ''),
            'ecole' => trim($_POST['ecole'] ?? ''),
            'annee_etudes' => $_POST['annee_etudes'] ?? null,
            'competences' => trim($_POST['competences'] ?? ''),
            'langues' => trim($_POST['langues'] ?? ''),
            'experience' => trim($_POST['experience'] ?? ''),
            'lettre_motivation' => trim($_POST['lettre_motivation'] ?? ''),
            'disponibilite_debut' => $_POST['disponibilite_debut'] ?? null,
            'disponibilite_fin' => $_POST['disponibilite_fin'] ?? null,
            'mobilite_geographique' => isset($_POST['mobilite_geographique']) ? 1 : 0,
            'permis_conduire' => isset($_POST['permis_conduire']) ? 1 : 0,
            'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
            'portfolio_url' => trim($_POST['portfolio_url'] ?? '')
        ];

        try {
            // Vérifier si le profil existe déjà
            $existingProfile = $this->profilModel->findByUserId($userId);

            if ($existingProfile) {
                // Mise à jour
                $result = $this->profilModel->update($existingProfile['id'], $data);
                $message = $result ? 'Profil mis à jour avec succès.' : 'Erreur lors de la mise à jour du profil.';
            } else {
                // Création
                $result = $this->profilModel->create($data);
                $message = $result ? 'Profil créé avec succès.' : 'Erreur lors de la création du profil.';
            }

            $_SESSION[$result ? 'success' : 'error'] = $message;
        } catch (Exception $e) {
             error_log('Profile creation error: ' . $e->getMessage());
             $_SESSION['error'] = 'Erreur lors de la sauvegarde du profil.';
         }

        header('Location: /stagiaire/profile');
        exit;
    }

    /**
     * Traiter la création/modification du profil
     */
    public static function handleProfile()
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/profile');
            exit;
        }

        // Vérification CSRF
        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            header('Location: /stagiaire/profile');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Préparer les données
        $data = [
            'user_id' => $userId,
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'date_naissance' => $_POST['date_naissance'] ?? null,
            'telephone' => trim($_POST['telephone'] ?? ''),
            'adresse' => trim($_POST['adresse'] ?? ''),
            'ville' => trim($_POST['ville'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'niveau_etudes' => $_POST['niveau_etudes'] ?? null,
            'domaine_etudes' => trim($_POST['domaine_etudes'] ?? ''),
            'ecole' => trim($_POST['ecole'] ?? ''),
            'annee_etudes' => $_POST['annee_etudes'] ?? null,
            'competences' => trim($_POST['competences'] ?? ''),
            'langues' => trim($_POST['langues'] ?? ''),
            'experience' => trim($_POST['experience'] ?? ''),
            'lettre_motivation' => trim($_POST['lettre_motivation'] ?? ''),
            'disponibilite_debut' => $_POST['disponibilite_debut'] ?? null,
            'disponibilite_fin' => $_POST['disponibilite_fin'] ?? null,
            'mobilite_geographique' => isset($_POST['mobilite_geographique']) ? 1 : 0,
            'permis_conduire' => isset($_POST['permis_conduire']) ? 1 : 0,
            'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
            'portfolio_url' => trim($_POST['portfolio_url'] ?? '')
        ];

        // Créer une instance du modèle
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        $profilModel = new ProfilStagiaire($pdo);

        // Vérifier si le profil existe déjà
        $existingProfile = $profilModel->findByUserId($userId);

        if ($existingProfile) {
            // Mise à jour
            $result = $profilModel->update($existingProfile['id'], $data);
            $message = $result ? 'Profil mis à jour avec succès.' : 'Erreur lors de la mise à jour du profil.';
        } else {
            // Création
            $result = $profilModel->create($data);
            $message = $result ? 'Profil créé avec succès.' : 'Erreur lors de la création du profil.';
        }

        $_SESSION[$result ? 'success' : 'error'] = $message;
        header('Location: /stagiaire/profile');
        exit;
    }



    /**
     * Supprimer le CV
     */
    public static function deleteCv()
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/profile');
            exit;
        }

        // Vérification CSRF
        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            header('Location: /stagiaire/profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $profilModel = new ProfilStagiaire();
        $profil = $profilModel->findByUserId($userId);

        if ($profil && !empty($profil['cv_path'])) {
            // Supprimer le fichier
            $cvPath = __DIR__ . '/../../' . $profil['cv_path'];
            if (file_exists($cvPath)) {
                unlink($cvPath);
            }

            // Mettre à jour la base de données
            $result = ProfilStagiaire::updateCvPath($userId, null);

            if ($result) {
                $_SESSION['success'] = 'CV supprimé avec succès.';
            } else {
                $_SESSION['error'] = 'Erreur lors de la suppression du CV.';
            }
        } else {
            $_SESSION['error'] = 'Aucun CV à supprimer.';
        }

        header('Location: /stagiaire/profile');
        exit;
    }

    /**
     * Afficher la liste des offres de stage
     */
    public function showOffers()
    {
        AuthMiddleware::requireStagiaire();

        $userId = $_SESSION['user_id'];

        // Récupérer les filtres
        $filters = [
            'ville' => $_GET['ville'] ?? '',
            'domaine' => $_GET['domaine'] ?? '',
            'type_contrat' => $_GET['type_contrat'] ?? '',
            'duree_min' => $_GET['duree_min'] ?? '',
            'remuneration' => $_GET['remuneration'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Pagination
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Récupérer les offres avec filtres
        $offreModel = new OffreStage();

        // Adapter les filtres pour la méthode search
        $searchFilters = [];
        if (!empty($filters['search'])) {
            $searchFilters['keywords'] = $filters['search'];
        }
        if (!empty($filters['ville'])) {
            $searchFilters['ville'] = $filters['ville'];
        }
        if (!empty($filters['domaine'])) {
            $searchFilters['domaine'] = $filters['domaine'];
        }
        if (!empty($filters['type_contrat'])) {
            $searchFilters['type_contrat'] = $filters['type_contrat'];
        }

        $offres = $offreModel->search($searchFilters, $page, $limit);
        $totalOffres = $offreModel->countSearch($searchFilters);
        $totalPages = ceil($totalOffres / $limit);
        $currentPage = $page;

        // Récupérer les candidatures existantes de l'utilisateur
        $candidatureModel = new Candidature();
        $candidatures = $candidatureModel->getByStagiaire($userId);

        // Récupérer les données pour les filtres
        $domaines = $offreModel->getDistinctDomaines();
        $villes = $offreModel->getDistinctVilles();
        $typesContrat = $offreModel->getDistinctTypesContrat();

        // Capturer le contenu de la vue
        ob_start();
        include __DIR__ . '/../views/stagiaire/offres.php';
        $content = ob_get_clean();

        // Variables pour le layout
        $pageTitle = 'Offres de Stage - JobBoard';
        $pageDescription = 'Découvrez les opportunités de stage qui correspondent à votre profil';

        include __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Afficher les candidatures du stagiaire
     */
    public static function showApplications()
    {
        AuthMiddleware::requireStagiaire();

        $userId = $_SESSION['user_id'];

        // Récupérer les filtres
        $filters = [
            'statut' => $_GET['statut'] ?? '',
            'periode' => $_GET['periode'] ?? ''
        ];

        // Pagination
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Récupérer les candidatures avec détails
        $candidatureModel = new Candidature();
        $candidatures = $candidatureModel->getByStagiaire($userId, $limit, $offset);

        // Pour le total, on récupère toutes les candidatures sans limite
        $allCandidatures = $candidatureModel->getByStagiaire($userId);
        $totalCandidatures = count($allCandidatures);
        $totalPages = ceil($totalCandidatures / $limit);

        // Statistiques
        $statistiques = Candidature::getStatistiquesByUser($userId);

        $currentPage = $page;

        include __DIR__ . '/../views/stagiaire/candidatures.php';
    }

    /**
     * Postuler à une offre de stage
     */
    public static function applyToOffer($offreId)
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/offers');
            exit;
        }

        // Vérification CSRF
        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de sécurité invalide.';
            header('Location: /stagiaire/offers');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Vérifier que l'offre existe
        $offre = OffreStage::findById($offreId);
        if (!$offre) {
            $_SESSION['error'] = 'Offre de stage introuvable.';
            header('Location: /stagiaire/offers');
            exit;
        }

        // Vérifier que l'utilisateur n'a pas déjà postulé
        if (Candidature::hasUserApplied($userId, $offreId)) {
            $_SESSION['error'] = 'Vous avez déjà postulé à cette offre.';
            header('Location: /stagiaire/offers/' . $offreId);
            exit;
        }

        // Vérifier que le profil est complet
        $profilModel = new ProfilStagiaire();
        $profil = $profilModel->findByUserId($userId);
        if (!$profil || empty($profil['cv_path'])) {
            $_SESSION['error'] = 'Veuillez compléter votre profil et télécharger votre CV avant de postuler.';
            header('Location: /stagiaire/profile');
            exit;
        }

        // Créer la candidature
        $candidatureData = [
            'user_id' => $userId,
            'offre_id' => $offreId,
            'cv_path' => $profil['cv_path'],
            'lettre_motivation' => trim($_POST['lettre_motivation'] ?? ''),
            'statut' => 'en_attente'
        ];

        $result = Candidature::create($candidatureData);

        if ($result) {
            $_SESSION['success'] = 'Votre candidature a été envoyée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'envoi de votre candidature.';
        }

        header('Location: /stagiaire/offers/' . $offreId);
        exit;
    }

    /**
     * Retirer une candidature
     */
    public static function withdrawApplication($candidatureId)
    {
        AuthMiddleware::requireStagiaire();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /stagiaire/applications');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Vérifier que la candidature existe et appartient à l'utilisateur
        $candidature = Candidature::findById($candidatureId);
        if (!$candidature || $candidature['user_id'] != $userId) {
            $_SESSION['error'] = 'Candidature introuvable.';
            header('Location: /stagiaire/applications');
            exit;
        }

        // Vérifier que la candidature peut être retirée (statut en_attente)
        if ($candidature['statut'] !== 'en_attente') {
            $_SESSION['error'] = 'Cette candidature ne peut plus être retirée.';
            header('Location: /stagiaire/applications');
            exit;
        }

        // Supprimer la candidature
        $result = Candidature::delete($candidatureId);

        if ($result) {
            $_SESSION['success'] = 'Candidature retirée avec succès.';
        } else {
            $_SESSION['error'] = 'Erreur lors du retrait de la candidature.';
        }

        header('Location: /stagiaire/applications');
        exit;
    }

    /**
     * Afficher le détail d'une offre
     */
    public static function showOfferDetail($offreId)
    {
        AuthMiddleware::requireStagiaire();

        $userId = $_SESSION['user_id'];

        // Récupérer l'offre avec les détails de l'entreprise
        $offre = OffreStage::findById($offreId);
        if (!$offre) {
            $_SESSION['error'] = 'Offre de stage introuvable.';
            header('Location: /stagiaire/offers');
            exit;
        }

        // Vérifier si l'utilisateur a déjà postulé
        $aDejaPostule = Candidature::hasUserApplied($userId, $offreId);

        // Récupérer le profil pour vérifier s'il est complet
        $profilModel = new ProfilStagiaire();
        $profil = $profilModel->findByUserId($userId);
        $profilComplet = !empty($profil) && !empty($profil['cv_path']);

        include __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Calculer le pourcentage de complétion du profil
     */
    private static function calculateProfileCompletion($profil)
    {
        if (!$profil) {
            return 0;
        }

        $fields = [
            'nom', 'prenom', 'date_naissance', 'telephone', 'adresse',
            'ville', 'code_postal', 'niveau_etudes', 'domaine_etudes',
            'ecole', 'competences', 'cv_path'
        ];

        $completedFields = 0;
        $totalFields = count($fields);

        foreach ($fields as $field) {
            if (!empty($profil[$field])) {
                $completedFields++;
            }
        }

        return round(($completedFields / $totalFields) * 100);
    }

    /**
     * Valider le fichier CV
     */
    private function validateCvFile($file)
    {
        // Vérifier la taille (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return 'Le fichier est trop volumineux (maximum 5MB).';
        }

        // Vérifier l'extension
        $allowedExtensions = ['pdf'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return 'Seuls les fichiers PDF sont autorisés.';
        }

        // Vérifier le type MIME
        $allowedMimeTypes = ['application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return 'Le fichier doit être un PDF valide.';
        }

        return true;
    }

    /**
     * Générer un token CSRF
     */
    public static function generateCsrfToken()
    {
        return AuthMiddleware::generateCsrfToken();
    }

    /**
     * Rediriger vers la page de connexion si non authentifié
     */
    private static function redirectIfNotAuthenticated()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /Dev1/public/auth/login');
            exit;
        }
    }

    /**
     * Vérifier si l'utilisateur est un stagiaire
     */
    private static function checkStagiaireRole()
    {
        if ($_SESSION['user_role'] !== 'stagiaire') {
            header('Location: /');
            exit;
        }
    }

    /**
     * Nettoyer et valider les données d'entrée
     */
    private static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Afficher une page d'erreur 404
     */
    public static function notFound()
    {
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }

    /**
     * Afficher une page d'erreur 403
     */
    public static function forbidden()
    {
        http_response_code(403);
        require_once __DIR__ . '/../views/errors/403.php';
        exit;
    }

    public function rechercherOffres()
    {
        $motsCles = $_GET['mots_cles'] ?? '';
        $domaine = $_GET['domaine'] ?? '';

        $offres = OffreStage::rechercher($motsCles, $domaine);

        require_once __DIR__ . '/../views/stagiaire/recherche_offres.php';
    }

    public function gererMotivation()
    {
        $candidatureId = $_GET['id'] ?? null;
        $motivation = $_POST['motivation'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motivation = SecurityUtils::sanitizeHTML($motivation);
            Candidature::updateMotivation($candidatureId, $motivation);
            header('Location: /mes-candidatures');
            exit;
        }

        $candidature = Candidature::getById($candidatureId);
        require_once __DIR__ . '/../views/stagiaire/lettre_motivation.php';
    }
}