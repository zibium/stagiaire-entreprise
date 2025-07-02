<?php
// UrlHelper est disponible via l'alias global défini dans header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= '/Dev1/public/css/dashboard.css' ?>" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= '/Dev1/public/' ?>">
                <i class="fas fa-briefcase me-2"></i>JobBoard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= '/Dev1/public/stagiaire/dashboard' ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= '/Dev1/public/stagiaire/offers' ?>">
                            <i class="fas fa-search me-1"></i>Offres de stage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= '/Dev1/public/stagiaire/applications' ?>">
                            <i class="fas fa-paper-plane me-1"></i>Mes candidatures
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($user['email'] ?? 'Utilisateur') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= '/Dev1/public/stagiaire/profile' ?>"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= '/Dev1/public/auth/logout' ?>"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Messages d'alerte -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- En-tête du dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="card-title mb-2">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    Bienvenue, <?= ($profil && isset($profil['prenom'], $profil['nom'])) ? htmlspecialchars($profil['prenom'] . ' ' . $profil['nom']) : htmlspecialchars($user['email'] ?? 'Utilisateur') ?>!
                                </h1>
                                <p class="card-text mb-0">
                                    Gérez votre profil et trouvez le stage de vos rêves
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="dashboard-icon">
                                    <i class="fas fa-graduation-cap fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-success text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h5 class="card-title">Profil</h5>
                        <p class="card-text">
                            <?php if ($stats['profil_complete']): ?>
                                <span class="text-success"><i class="fas fa-check"></i> Créé</span>
                            <?php else: ?>
                                <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> À compléter</span>
                            <?php endif; ?>
                        </p>
                        <small class="text-muted"><?= htmlspecialchars($completion_percentage) ?>% complété</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-info text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h5 class="card-title">CV</h5>
                        <p class="card-text">
                            <?php if ($stats['cv_uploaded']): ?>
                                <span class="text-success"><i class="fas fa-check"></i> Uploadé</span>
                            <?php else: ?>
                                <span class="text-warning"><i class="fas fa-upload"></i> À uploader</span>
                            <?php endif; ?>
                        </p>
                        <small class="text-muted">Format PDF uniquement</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-primary text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <h5 class="card-title">Candidatures</h5>
                        <p class="card-text">
                            <span class="h4 text-primary"><?= $stats['candidatures_envoyees'] ?></span>
                        </p>
                        <small class="text-muted">Candidatures envoyées</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-warning text-white rounded-circle mx-auto mb-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="card-title">En attente</h5>
                        <p class="card-text">
                            <span class="h4 text-warning"><?= $stats['candidatures_en_attente'] ?></span>
                        </p>
                        <small class="text-muted">Réponses en attente</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Actions rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (!$stats['profil_complete']): ?>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <a href="<?= '/Dev1/public/stagiaire/profile' ?>" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                        <i class="fas fa-user-plus fa-2x mb-2"></i>
                                        <span>Créer mon profil</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!$stats['cv_uploaded']): ?>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <a href="<?= '/Dev1/public/stagiaire/profile' ?>#cv-section" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                        <i class="fas fa-upload fa-2x mb-2"></i>
                                        <span>Uploader mon CV</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-md-6 col-lg-3 mb-3">
                                <a href="<?= '/Dev1/public/stagiaire/offers' ?>" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <span>Chercher des stages</span>
                                </a>
                            </div>
                            
                            <div class="col-md-6 col-lg-3 mb-3">
                                <a href="<?= '/Dev1/public/stagiaire/applications' ?>" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                    <i class="fas fa-list fa-2x mb-2"></i>
                                    <span>Mes candidatures</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression du profil -->
        <?php if ($stats['profil_complete'] && $completionPercentage < 100): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>Complétion du profil
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Progression</span>
                                <span class="fw-bold"><?= $completionPercentage ?>%</span>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $completionPercentage ?>%"></div>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="fas fa-lightbulb me-1"></i>
                                Un profil complet augmente vos chances d'être remarqué par les entreprises.
                                <a href="<?= UrlHelper::url('stagiaire/profile') ?>" class="text-decoration-none">Compléter maintenant</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Conseils et astuces -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>Conseils pour votre profil
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Renseignez toutes les informations de votre profil
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Uploadez un CV au format PDF récent
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Détaillez vos compétences et expériences
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                Rédigez une lettre de motivation personnalisée
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-star me-2 text-warning"></i>Optimisez vos candidatures
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Lisez attentivement les offres avant de postuler
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Adaptez votre lettre de motivation à chaque offre
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Suivez le statut de vos candidatures
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Relancez poliment après une semaine
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Activité récente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune activité récente</p>
                            <small class="text-muted">Vos candidatures et interactions apparaîtront ici</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 JobBoard. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Aide</a>
                    <a href="#" class="text-muted text-decoration-none me-3">Contact</a>
                    <a href="#" class="text-muted text-decoration-none">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation des cartes statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
    </script>
</body>
</html>