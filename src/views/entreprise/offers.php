<?php
require_once __DIR__ . '/../../utils/UrlHelper.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'entreprise') {
    header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

// Configuration de la page
$pageTitle = 'Mes offres - Entreprise';
$currentPage = 'offers';

// Récupérer les données passées par le contrôleur
$offres = $offres ?? [];
$user = $user ?? [];

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
                        <i class="fas fa-briefcase me-2"></i>
                        Mes offres
                    </h1>
                    <p class="text-muted mb-0">Gérez vos offres de stage</p>
                </div>
                <a href="<?= \UrlHelper::url('entreprise/offers/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle offre
                </a>
            </div>
        </div>

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

        <!-- Liste des offres -->
        <div class="row">
            <div class="col-12">
        </div>
        <!-- Statistiques rapides -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Offres actives</h6>
                                <h3 class="mb-0">12</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-briefcase fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Candidatures reçues</h6>
                                <h3 class="mb-0">45</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">En attente</h6>
                                <h3 class="mb-0">8</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Acceptées</h6>
                                <h3 class="mb-0">15</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Rechercher</label>
                                <input type="text" class="form-control" id="search" placeholder="Titre, description...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="expired">Expirée</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Trier par</label>
                                <select class="form-select" id="sort">
                                    <option value="date_desc">Date (plus récent)</option>
                                    <option value="date_asc">Date (plus ancien)</option>
                                    <option value="title">Titre</option>
                                    <option value="applications">Candidatures</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des offres -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Mes offres de stage</h5>
                        <span class="badge bg-secondary">12 offres</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Domaine</th>
                                        <th>Date limite</th>
                                        <th>Candidatures</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Exemple d'offre -->
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">Développeur Web Full Stack</h6>
                                                <small class="text-muted">Publié le 15 Nov 2024</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">Informatique</span>
                                        </td>
                                        <td>
                                            <span class="text-success">30 Déc 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">12 candidatures</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">Assistant Marketing Digital</h6>
                                                <small class="text-muted">Publié le 10 Nov 2024</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Marketing</span>
                                        </td>
                                        <td>
                                            <span class="text-warning">25 Déc 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">8 candidatures</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">Analyste Financier Junior</h6>
                                                <small class="text-muted">Publié le 5 Nov 2024</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Finance</span>
                                        </td>
                                        <td>
                                            <span class="text-danger">20 Déc 2024</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">25 candidatures</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">Expirée</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" title="Renouveler">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour les animations des cartes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des cartes statistiques
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>