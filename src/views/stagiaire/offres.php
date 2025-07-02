<?php
// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'stagiaire') {
    header('Location: /Dev1/public/auth/login');
    exit;
}

// Récupération des données passées par le contrôleur
$offres = $offres ?? [];
$totalOffres = $totalOffres ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$filters = $filters ?? [];
$domaines = $domaines ?? [];
$villes = $villes ?? [];
$typesContrat = $typesContrat ?? [];
$candidatures = $candidatures ?? [];

// Fonction helper pour formater les dates
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Fonction helper pour formater la durée
function formatDuree($duree) {
    return $duree . ' mois';
}

// Fonction helper pour vérifier si on a déjà postulé
function hasApplied($offreId, $candidatures) {
    foreach ($candidatures as $candidature) {
        if ($candidature['offre_id'] == $offreId) {
            return $candidature;
        }
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres de Stage - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= \UrlHelper::url('css/style.css') ?>" rel="stylesheet">
    <style>
        /* Styles personnalisés pour harmoniser avec le tableau de bord */
        .status-en-attente {
            color: #ffc107;
        }

        .status-acceptee {
            color: #28a745;
        }

        .status-refusee {
            color: #dc3545;
        }

        .status-entretien {
            color: #17a2b8;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= \UrlHelper::url('') ?>">
                <i class="fas fa-briefcase me-2"></i>JobBoard
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \UrlHelper::url('stagiaire/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= \UrlHelper::url('stagiaire/offres') ?>">
                            <i class="fas fa-search me-1"></i>Offres de stage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \UrlHelper::url('stagiaire/candidatures') ?>">
                            <i class="fas fa-paper-plane me-1"></i>Mes candidatures
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user_email'] ?? 'Utilisateur') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= \UrlHelper::url('stagiaire/profile') ?>"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= \UrlHelper::url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
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

        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="card-title mb-2">
                                    <i class="fas fa-search me-2"></i>
                                    Offres de stage
                                </h1>
                                <p class="card-text mb-0">
                                    Découvrez les offres de stage disponibles
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="dashboard-icon">
                                    <i class="fas fa-briefcase fa-3x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des filtres -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Filtres de recherche
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= \UrlHelper::url('stagiaire/offres') ?>">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                   placeholder="Titre, entreprise, compétences...">
                        </div>

                        <div class="col-md-2">
                            <label for="domaine" class="form-label">Domaine</label>
                            <select class="form-select" id="domaine" name="domaine">
                                <option value="">Tous les domaines</option>
                                <?php foreach ($domaines as $domaine): ?>
                                    <option value="<?= htmlspecialchars($domaine) ?>"
                                            <?= ($filters['domaine'] ?? '') === $domaine ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($domaine) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="ville" class="form-label">Ville</label>
                            <select class="form-select" id="ville" name="ville">
                                <option value="">Toutes les villes</option>
                                <?php foreach ($villes as $ville): ?>
                                    <option value="<?= htmlspecialchars($ville) ?>"
                                            <?= ($filters['ville'] ?? '') === $ville ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ville) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="type_contrat" class="form-label">Type de contrat</label>
                            <select class="form-select" id="type_contrat" name="type_contrat">
                                <option value="">Tous les types</option>
                                <?php foreach ($typesContrat as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>"
                                            <?= ($filters['type_contrat'] ?? '') === $type ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="duree_min" class="form-label">Durée min. (mois)</label>
                            <input type="number" class="form-control" id="duree_min" name="duree_min" min="1" max="12"
                                   value="<?= htmlspecialchars($filters['duree_min'] ?? '') ?>"
                                   placeholder="Ex: 3">
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="remuneration" class="form-label">Rémunération</label>
                            <select class="form-select" id="remuneration" name="remuneration">
                                <option value="">Toutes</option>
                                <option value="oui" <?= ($filters['remuneration'] ?? '') === 'oui' ? 'selected' : '' ?>>Rémunéré</option>
                                <option value="non" <?= ($filters['remuneration'] ?? '') === 'non' ? 'selected' : '' ?>>Non rémunéré</option>
                            </select>
                        </div>

                        <div class="col-md-9 d-flex align-items-end">
                            <div class="d-flex gap-2">
                                <a href="<?= \UrlHelper::url('stagiaire/offers') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Effacer
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations sur les résultats -->
        <?php if (!empty($offres)): ?>
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong><?= number_format($totalOffres) ?></strong> offre(s) trouvée(s)
                            <?php if (!empty(array_filter($filters))): ?>
                                <span class="badge bg-primary ms-2">Filtres appliqués</span>
                            <?php endif; ?>
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-file-alt me-1"></i>Page <?= $currentPage ?> sur <?= $totalPages ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste des offres -->
        <div class="row">
            <?php if (empty($offres)): ?>
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h3 class="card-title">Aucune offre trouvée</h3>
                            <p class="card-text text-muted">Essayez de modifier vos critères de recherche ou consultez toutes les offres disponibles.</p>
                            <a href="<?= \UrlHelper::url('stagiaire/offers') ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Voir toutes les offres
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($offres as $offre): ?>
                    <?php $candidature = hasApplied($offre['id'], $candidatures); ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-2 text-primary"><?= htmlspecialchars($offre['titre']) ?></h5>
                                        <div class="d-flex align-items-center mb-2">
                                            <?php if (!empty($offre['logo_path'])): ?>
                                                <img src="<?= htmlspecialchars($offre['logo_path']) ?>"
                                                     alt="Logo <?= htmlspecialchars($offre['nom_entreprise']) ?>"
                                                     class="rounded me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-building text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="text-muted"><?= htmlspecialchars($offre['nom_entreprise']) ?></span>
                                        </div>
                                    </div>

                                    <?php if ($candidature): ?>
                                        <span class="badge bg-<?= $candidature['statut'] === 'en_attente' ? 'warning' :
                                                                  ($candidature['statut'] === 'acceptee' ? 'success' :
                                                                  ($candidature['statut'] === 'refusee' ? 'danger' : 'info')) ?>">
                                            <i class="fas fa-<?= $candidature['statut'] === 'en_attente' ? 'clock' :
                                                              ($candidature['statut'] === 'acceptee' ? 'check' :
                                                              ($candidature['statut'] === 'refusee' ? 'times' : 'handshake')) ?> me-1"></i>
                                            <?= ucfirst(str_replace('_', ' ', $candidature['statut'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-body pt-2">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?= htmlspecialchars($offre['ville'] ?? 'Non spécifié') ?>
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= formatDuree($offre['duree']) ?>
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?= formatDate($offre['date_debut']) ?>
                                        </small>
                                    </div>
                                    <?php if (!empty($offre['remuneration'])): ?>
                                        <div class="col-6">
                                            <small class="text-success d-flex align-items-center">
                                                <i class="fas fa-euro-sign me-1"></i>
                                                <?= htmlspecialchars($offre['remuneration']) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <p class="card-text text-muted small mb-3">
                                    <?= nl2br(htmlspecialchars(substr($offre['description'], 0, 150))) ?>
                                    <?php if (strlen($offre['description']) > 150): ?>
                                        <span class="text-primary">... Lire la suite</span>
                                    <?php endif; ?>
                                </p>

                                <div class="mb-3">
                                    <?php if (!empty($offre['domaine'])): ?>
                                        <span class="badge bg-primary me-1"><?= htmlspecialchars($offre['domaine']) ?></span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars($offre['type_contrat']) ?></span>
                                    <?php if (!empty($offre['niveau_etude'])): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($offre['niveau_etude']) ?></span>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <div class="card-footer bg-white border-0 pt-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>Publié le <?= formatDate($offre['date_creation']) ?>
                                        <?php if (!empty($offre['date_limite_candidature'])): ?>
                                            <br><i class="fas fa-clock me-1"></i>Limite: <?= formatDate($offre['date_limite_candidature']) ?>
                                        <?php endif; ?>
                                    </small>

                                    <div class="d-flex gap-2">
                                        <a href="<?= \UrlHelper::url('stagiaire/offers/' . $offre['id']) ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Détails
                                        </a>

                                        <?php if ($candidature): ?>
                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="fas fa-check me-1"></i> Candidature envoyée
                                            </button>
                                        <?php else: ?>
                                            <?php
                                            $expired = !empty($offre['date_limite_candidature']) &&
                                                      strtotime($offre['date_limite_candidature']) < time();
                                            ?>
                                            <?php if ($expired): ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="fas fa-clock me-1"></i> Expiré
                                                </button>
                                            <?php else: ?>
                                                <a href="<?= \UrlHelper::url('stagiaire/offers/' . $offre['id'] . '/apply') ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-paper-plane me-1"></i> Postuler
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navigation des pages" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => 1])) ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i == $currentPage): ?>
                            <li class="page-item active">
                                <span class="page-link"><?= $i ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $totalPages])) ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 JobBoard. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-muted me-3">Aide</a>
                    <a href="#" class="text-muted me-3">Contact</a>
                    <a href="#" class="text-muted">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Auto-submit form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.filters-section form');
            const selects = form.querySelectorAll('select');

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    // Auto-submit after a short delay to allow multiple selections
                    setTimeout(() => {
                        form.submit();
                    }, 100);
                });
            });

            // Auto-hide alerts
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
    </script>
</body>
</html>