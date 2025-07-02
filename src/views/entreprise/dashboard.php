<?php
require_once __DIR__ . '/../../utils/UrlHelper.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'entreprise') {
    header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

// Configuration de la page
$pageTitle = 'Tableau de bord - Entreprise';
$currentPage = 'dashboard';

// Récupérer les données passées par le contrôleur
$user = $profil ?? [];
$stats = $stats ?? [];
$recentOffers = $offres_recentes ?? [];
$recentApplications = $candidatures_recentes ?? [];

// Inclure le header
include __DIR__ . '/../layouts/header.php';

// Inclure la sidebar entreprise
include __DIR__ . '/../layouts/sidebar-entreprise.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Tableau de bord
                    </h1>
                    <p class="text-muted mb-0">Vue d'ensemble de votre activité</p>
                </div>
            </div>
        </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
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
                <div class="text-center mb-5">
                    <h1 class="display-4 mb-4">
                        <i class="fas fa-building me-3 text-primary"></i>
                        Bienvenue, <?= ($user && isset($user['nom_entreprise'])) ? htmlspecialchars($user['nom_entreprise']) : htmlspecialchars($user['email'] ?? 'Entreprise') ?>!
                    </h1>
                    <p class="lead text-muted">
                        Gérez vos offres de stage et trouvez les meilleurs candidats
                    </p>
                </div>

                <!-- Statistiques rapides -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Offres actives</h5>
                                <h3 class="text-primary"><?= $stats['offres_actives'] ?? 0 ?></h3>
                                <small class="text-muted">Offres publiées</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Candidatures</h5>
                                <h3 class="text-info"><?= $stats['candidatures_totales'] ?? 0 ?></h3>
                                <small class="text-muted">Candidatures reçues</small>
                            </div>
                </div>
                        </div>
                     </div>
                     
                     <div class="col-lg-3 col-md-6 mb-4">
                         <div class="card h-100 border-0 shadow-sm">
                             <div class="card-body text-center">
                                 <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                 <h5 class="card-title">En attente</h5>
                                 <h3 class="text-warning"><?= $stats['candidatures_en_attente'] ?? 0 ?></h3>
                                 <small class="text-muted">À examiner</small>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-lg-3 col-md-6 mb-4">
                         <div class="card h-100 border-0 shadow-sm">
                             <div class="card-body text-center">
                                 <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                 <h5 class="card-title">Acceptées</h5>
                                 <h3 class="text-success"><?= $stats['candidatures_acceptees'] ?? 0 ?></h3>
                                 <small class="text-muted">Candidatures validées</small>
                             </div>
                         </div>
                     </div>
                 </div>

                <!-- Actions rapides -->
                <div class="text-center mb-4">
                    <h3 class="mb-4">Actions rapides</h3>
                    <div class="row justify-content-center">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?= \UrlHelper::url('entreprise/offers/create') ?>" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <span>Créer une offre</span>
                            </a>
                        
                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                             <a href="<?= \UrlHelper::url('entreprise/applications') ?>" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                 <i class="fas fa-eye fa-2x mb-2"></i>
                                 <span>Voir les candidatures</span>
                             </a>
                         </div>
                         
                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                             <a href="<?= \UrlHelper::url('entreprise/offers') ?>" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                 <i class="fas fa-list fa-2x mb-2"></i>
                                 <span>Gérer mes offres</span>
                             </a>
                         </div>
                         
                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                             <a href="<?= \UrlHelper::url('entreprise/profile') ?>" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                 <i class="fas fa-building fa-2x mb-2"></i>
                                 <span>Mon profil</span>
                             </a>
                         </div>
                         
                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                             <a href="<?= \UrlHelper::url('entreprise/statistics') ?>" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                 <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                 <span>Statistiques</span>
                             </a>
                         </div>
                     </div>
                  </div>

                <!-- Conseils et astuces -->
                <h3 class="text-center mb-4">Conseils et astuces</h3>
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-5 col-md-6 mb-3">
                 <div class="card h-100">
                     <div class="card-header bg-light">
                         <h6 class="card-title mb-0">
                             <i class="fas fa-lightbulb me-2 text-warning"></i>Conseils pour vos offres
                         </h6>
                     </div>
                     <div class="card-body">
                         <ul class="list-unstyled mb-0">
                             <li class="mb-2">
                                 <i class="fas fa-check text-success me-2"></i>
                                 Rédigez des descriptions claires et détaillées
                             </li>
                             <li class="mb-2">
                                 <i class="fas fa-check text-success me-2"></i>
                                 Précisez les compétences requises
                             </li>
                             <li class="mb-2">
                                 <i class="fas fa-check text-success me-2"></i>
                                 Indiquez la durée et les modalités du stage
                             </li>
                             <li class="mb-0">
                                 <i class="fas fa-check text-success me-2"></i>
                                 Mettez à jour régulièrement vos offres
                             </li>
                         </ul>
                     </div>
                 </div>
             </div>
             
             <div class="col-lg-5 col-md-6 mb-3">
                 <div class="card h-100">
                     <div class="card-header bg-light">
                         <h6 class="card-title mb-0">
                             <i class="fas fa-star me-2 text-warning"></i>Gestion des candidatures
                         </h6>
                     </div>
                     <div class="card-body">
                         <ul class="list-unstyled mb-0">
                             <li class="mb-2">
                                 <i class="fas fa-arrow-right text-primary me-2"></i>
                                 Examinez rapidement les nouvelles candidatures
                             </li>
                             <li class="mb-2">
                                 <i class="fas fa-arrow-right text-primary me-2"></i>
                                 Donnez des retours constructifs aux candidats
                             </li>
                             <li class="mb-2">
                                 <i class="fas fa-arrow-right text-primary me-2"></i>
                                 Organisez des entretiens pour les profils intéressants
                             </li>
                             <li class="mb-0">
                                 <i class="fas fa-arrow-right text-primary me-2"></i>
                                 Communiquez clairement vos décisions
                             </li>
                         </ul>
                     </div>
                 </div>
             </div>
                 </div>

                 <!-- Offres récentes -->
                 <?php if (!empty($recentOffers)): ?>
                 <h3 class="text-center mb-4">Mes offres récentes</h3>
                 <div class="row justify-content-center mb-4">
                     <div class="col-lg-10">
                         <div class="card">
                              <div class="card-body">
                         <div class="row">
                             <?php foreach (array_slice($recentOffers, 0, 3) as $offre): ?>
                             <div class="col-md-4 mb-3">
                                 <div class="card border-0 shadow-sm h-100">
                                     <div class="card-body">
                                         <h6 class="card-title"><?= htmlspecialchars($offre['titre'] ?? 'Offre sans titre') ?></h6>
                                         <p class="card-text text-muted small"><?= htmlspecialchars(substr($offre['description'] ?? '', 0, 100)) ?>...</p>
                                         <div class="d-flex justify-content-between align-items-center">
                                             <small class="text-muted"><?= date('d/m/Y', strtotime($offre['date_creation'] ?? 'now')) ?></small>
                                             <span class="badge bg-<?= ($offre['statut'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                 <?= ucfirst($offre['statut'] ?? 'Active') ?>
                                             </span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <?php endforeach; ?>
                         </div>
                         <div class="text-center mt-3">
                             <a href="<?= \UrlHelper::url('entreprise/offers') ?>" class="btn btn-outline-primary">
                                 <i class="fas fa-eye me-1"></i>Voir toutes mes offres
                             </a>
                         </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <?php endif; ?>

                 <!-- Candidatures récentes -->
                 <?php if (!empty($recentApplications)): ?>
                 <h3 class="text-center mb-4">Candidatures récentes</h3>
                 <div class="row justify-content-center mb-4">
                     <div class="col-lg-10">
                         <div class="card">
                              <div class="card-body">
                         <div class="row">
                             <?php foreach (array_slice($recentApplications, 0, 3) as $candidature): ?>
                             <div class="col-md-4 mb-3">
                                 <div class="card border-0 shadow-sm h-100">
                                     <div class="card-body">
                                         <h6 class="card-title"><?= htmlspecialchars($candidature['nom_candidat'] ?? 'Candidat') ?></h6>
                                         <p class="card-text text-muted small">Pour: <?= htmlspecialchars($candidature['titre_offre'] ?? 'Offre') ?></p>
                                         <div class="d-flex justify-content-between align-items-center">
                                             <small class="text-muted"><?= date('d/m/Y', strtotime($candidature['date_candidature'] ?? 'now')) ?></small>
                                             <span class="badge bg-<?= ($candidature['statut'] ?? 'en_attente') === 'en_attente' ? 'warning' : (($candidature['statut'] ?? '') === 'acceptee' ? 'success' : 'danger') ?>">
                                                 <?= ucfirst(str_replace('_', ' ', $candidature['statut'] ?? 'En attente')) ?>
                                             </span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <?php endforeach; ?>
                         </div>
                         <div class="text-center mt-3">
                             <a href="<?= \UrlHelper::url('entreprise/applications') ?>" class="btn btn-outline-primary">
                                 <i class="fas fa-eye me-1"></i>Voir toutes les candidatures
                             </a>
                         </div>
                     </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <?php else: ?>
                 <div class="row justify-content-center">
                     <div class="col-lg-10">
                         <div class="card">
                             <div class="card-header">
                                 <h5 class="card-title mb-0">
                                     <i class="fas fa-users me-2"></i>Candidatures récentes
                                 </h5>
                             </div>
                     <div class="card-body">
                         <div class="text-center py-4">
                             <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                             <p class="text-muted mb-0">Aucune candidature récente</p>
                             <small class="text-muted">Les candidatures pour vos offres apparaîtront ici</small>
                         </div>
                     </div>
                             </div>
                         </div>
                     </div>
                 <?php endif; ?>
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
    </div>
</div>

<?php
// Inclure le footer
include __DIR__ . '/../layouts/footer.php';
?>