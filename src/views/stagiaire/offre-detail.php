<?php
// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'stagiaire') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

// Récupération des données passées par le contrôleur
$offre = $offre ?? null;
$aDejaPostule = $aDejaPostule ?? false;
$profilComplet = $profilComplet ?? false;

if (!$offre) {
    header('Location: ' . \UrlHelper::url('stagiaire/offers'));
    exit;
}

// Fonction helper pour formater les dates
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Fonction helper pour formater les salaires
function formatSalaire($salaire) {
    if (empty($salaire)) {
        return 'Non spécifié';
    }
    return number_format($salaire, 0, ',', ' ') . ' €';
}
require_once __DIR__ . '/../../utils/UrlHelper.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($offre['titre']) ?> - JobBoard</title>
    <link href="<?= \UrlHelper::url('css/dashboard.css') ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .offer-detail-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .offer-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .offer-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .company-details h3 {
            font-size: 1.5rem;
            color: #007bff;
            margin: 0 0 5px 0;
        }
        
        .company-details p {
            color: #666;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .offer-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .meta-item i {
            color: #007bff;
            font-size: 1.2rem;
            width: 20px;
        }
        
        .meta-content {
            flex: 1;
        }
        
        .meta-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2px;
        }
        
        .meta-value {
            font-weight: 600;
            color: #333;
        }
        
        .offer-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .main-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .content-section {
            margin-bottom: 30px;
        }
        
        .content-section h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .content-section p,
        .content-section ul {
            line-height: 1.6;
            color: #555;
        }
        
        .content-section ul {
            padding-left: 20px;
        }
        
        .content-section li {
            margin-bottom: 8px;
        }
        
        .application-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        
        .application-status {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .status-applied {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #c3e6cb;
        }
        
        .status-applied i {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .application-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group textarea {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            min-height: 120px;
            transition: border-color 0.3s ease;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .btn-disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        .warning-card {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .warning-card h4 {
            color: #856404;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .warning-card p {
            color: #856404;
            margin: 0;
            line-height: 1.5;
        }
        
        .company-details-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .company-details-card h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .company-info-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .company-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .company-info-item i {
            color: #007bff;
            width: 20px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #007bff;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: #0056b3;
        }
        
        .deadline-warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        @media (max-width: 768px) {
            .offer-content {
                grid-template-columns: 1fr;
            }
            
            .offer-meta {
                grid-template-columns: 1fr;
            }
            
            .company-info {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .offer-title {
                font-size: 2rem;
            }
            
            .application-card {
                position: static;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?= \UrlHelper::url('stagiaire/dashboard') ?>">JobBoard</a>
            </div>
            <div class="nav-menu">
                <a href="<?= \UrlHelper::url('stagiaire/dashboard') ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a href="<?= \UrlHelper::url('stagiaire/profile') ?>" class="nav-link">
                    <i class="fas fa-user"></i> Mon profil
                </a>
                <a href="<?= \UrlHelper::url('stagiaire/offers') ?>" class="nav-link active">
                    <i class="fas fa-briefcase"></i> Offres de stage
                </a>
                <a href="<?= \UrlHelper::url('stagiaire/applications') ?>" class="nav-link">
                    <i class="fas fa-file-alt"></i> Mes candidatures
                </a>
                <a href="<?= \UrlHelper::url('auth/logout') ?>" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="offer-detail-container">
        <a href="<?= \UrlHelper::url('stagiaire/offers') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour aux offres
        </a>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- En-tête de l'offre -->
        <div class="offer-header">
            <h1 class="offer-title"><?= htmlspecialchars($offre['titre']) ?></h1>
            
            <div class="company-info">
                <?php if (!empty($offre['logo_path'])): ?>
                    <img src="<?= htmlspecialchars($offre['logo_path']) ?>" 
                         alt="Logo <?= htmlspecialchars($offre['nom_entreprise']) ?>"
                         class="company-logo">
                <?php else: ?>
                    <div class="company-logo" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-building" style="color: #666; font-size: 2rem;"></i>
                    </div>
                <?php endif; ?>
                
                <div class="company-details">
                    <h3><?= htmlspecialchars($offre['nom_entreprise']) ?></h3>
                    <p><?= htmlspecialchars($offre['ville_entreprise'] ?? 'Localisation non spécifiée') ?></p>
                </div>
            </div>
            
            <div class="offer-meta">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="meta-content">
                        <div class="meta-label">Localisation</div>
                        <div class="meta-value"><?= htmlspecialchars($offre['ville'] ?? 'Non spécifié') ?></div>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-briefcase"></i>
                    <div class="meta-content">
                        <div class="meta-label">Type de contrat</div>
                        <div class="meta-value"><?= htmlspecialchars($offre['type_contrat']) ?></div>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <div class="meta-content">
                        <div class="meta-label">Durée</div>
                        <div class="meta-value"><?= htmlspecialchars($offre['duree']) ?> mois</div>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-euro-sign"></i>
                    <div class="meta-content">
                        <div class="meta-label">Rémunération</div>
                        <div class="meta-value"><?= formatSalaire($offre['salaire']) ?></div>
                    </div>
                </div>
                
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="meta-content">
                        <div class="meta-label">Date de début</div>
                        <div class="meta-value"><?= formatDate($offre['date_debut']) ?></div>
                    </div>
                </div>
                
                <?php if (!empty($offre['date_limite_candidature'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-hourglass-end"></i>
                        <div class="meta-content">
                            <div class="meta-label">Date limite</div>
                            <div class="meta-value"><?= formatDate($offre['date_limite_candidature']) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($offre['date_limite_candidature']) && strtotime($offre['date_limite_candidature']) < time()): ?>
                <div class="deadline-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>La date limite de candidature est dépassée</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenu principal -->
        <div class="offer-content">
            <div class="main-content">
                <div class="content-section">
                    <h3><i class="fas fa-file-alt"></i> Description du poste</h3>
                    <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
                </div>
                
                <?php if (!empty($offre['competences_requises'])): ?>
                    <div class="content-section">
                        <h3><i class="fas fa-cogs"></i> Compétences requises</h3>
                        <p><?= nl2br(htmlspecialchars($offre['competences_requises'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($offre['avantages'])): ?>
                    <div class="content-section">
                        <h3><i class="fas fa-star"></i> Avantages</h3>
                        <p><?= nl2br(htmlspecialchars($offre['avantages'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($offre['domaine'])): ?>
                    <div class="content-section">
                        <h3><i class="fas fa-industry"></i> Domaine d'activité</h3>
                        <p><?= htmlspecialchars($offre['domaine']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="sidebar">
                <!-- Formulaire de candidature -->
                <div class="application-card">
                    <?php if ($aDejaPostule): ?>
                        <div class="application-status">
                            <div class="status-applied">
                                <i class="fas fa-check-circle"></i>
                                <h4>Candidature envoyée</h4>
                                <p>Vous avez déjà postulé à cette offre. Vous pouvez suivre l'état de votre candidature dans votre espace personnel.</p>
                            </div>
                        </div>
                        
                        <a href="/stagiaire/applications" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Voir mes candidatures
                        </a>
                    <?php elseif (!empty($offre['date_limite_candidature']) && strtotime($offre['date_limite_candidature']) < time()): ?>
                        <div class="warning-card">
                            <h4><i class="fas fa-exclamation-triangle"></i> Candidature fermée</h4>
                            <p>La date limite de candidature pour cette offre est dépassée.</p>
                        </div>
                    <?php elseif (!$profilComplet): ?>
                        <div class="warning-card">
                            <h4><i class="fas fa-exclamation-triangle"></i> Profil incomplet</h4>
                            <p>Vous devez compléter votre profil et télécharger votre CV avant de pouvoir postuler.</p>
                        </div>
                        
                        <a href="/stagiaire/profile" class="btn btn-primary">
                            <i class="fas fa-user-edit"></i> Compléter mon profil
                        </a>
                    <?php else: ?>
                        <h3><i class="fas fa-paper-plane"></i> Postuler</h3>
                        
                        <form method="POST" action="/stagiaire/offers/<?= $offre['id'] ?>/apply" class="application-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            
                            <div class="form-group">
                                <label for="lettre_motivation">Lettre de motivation</label>
                                <textarea id="lettre_motivation" name="lettre_motivation" 
                                         placeholder="Expliquez pourquoi vous souhaitez rejoindre cette entreprise et en quoi votre profil correspond à cette offre..."
                                         required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <!-- Informations entreprise -->
                <div class="company-details-card">
                    <h3><i class="fas fa-building"></i> À propos de l'entreprise</h3>
                    
                    <div class="company-info-grid">
                        <div class="company-info-item">
                            <i class="fas fa-building"></i>
                            <span><?= htmlspecialchars($offre['nom_entreprise']) ?></span>
                        </div>
                        
                        <?php if (!empty($offre['secteur_entreprise'])): ?>
                            <div class="company-info-item">
                                <i class="fas fa-industry"></i>
                                <span><?= htmlspecialchars($offre['secteur_entreprise']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($offre['ville_entreprise'])): ?>
                            <div class="company-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($offre['ville_entreprise']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($offre['site_web'])): ?>
                            <div class="company-info-item">
                                <i class="fas fa-globe"></i>
                                <a href="<?= htmlspecialchars($offre['site_web']) ?>" target="_blank" rel="noopener">
                                    Site web
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($offre['description_entreprise'])): ?>
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <h4>Description</h4>
                            <p style="color: #666; line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($offre['description_entreprise'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Validation du formulaire
        const form = document.querySelector('.application-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const lettreMotivation = document.getElementById('lettre_motivation');
                
                if (lettreMotivation.value.trim().length < 50) {
                    e.preventDefault();
                    alert('Votre lettre de motivation doit contenir au moins 50 caractères.');
                    lettreMotivation.focus();
                    return false;
                }
                
                // Confirmation avant envoi
                if (!confirm('Êtes-vous sûr de vouloir envoyer votre candidature ? Cette action ne peut pas être annulée.')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    </script>
</body>
</html>