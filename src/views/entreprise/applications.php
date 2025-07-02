<?php
require_once __DIR__ . '/../../utils/UrlHelper.php';

// Vérifier l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'entreprise') {
    header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

// Configuration de la page
$pageTitle = 'Candidatures reçues - Entreprise';
$currentPage = 'applications';

// Récupérer les données passées par le contrôleur
$candidatures = $candidatures ?? [];
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
                        <i class="fas fa-users me-2"></i>
                        Candidatures reçues
                    </h1>
                    <p class="text-muted mb-0">Gérez les candidatures pour vos offres de stage</p>
                </div>
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

        <!-- Contenu principal -->
        <div class="row">
            <div class="col-12">
        <!-- Statistiques rapides -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Candidatures reçues</h6>
                                <h3 class="mb-0">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-inbox fa-2x opacity-75"></i>
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
                                <h3 class="mb-0">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
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
                                <h6 class="card-title">Acceptées</h6>
                                <h3 class="mb-0">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Refusées</h6>
                                <h3 class="mb-0">0</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres et recherche</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="offre_id" class="form-label">Offre de stage</label>
                        <select id="offre_id" name="offre_id" class="form-select">
                                            <option value="">Toutes les offres</option>
                                            <?php foreach ($offres as $offre): ?>
                                                <option value="<?= $offre['id'] ?>" <?= (isset($_GET['offre_id']) && $_GET['offre_id'] == $offre['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($offre['titre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select id="statut" name="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?= (isset($_GET['statut']) && $_GET['statut'] == 'en_attente') ? 'selected' : '' ?>>En attente</option>
                            <option value="acceptee" <?= (isset($_GET['statut']) && $_GET['statut'] == 'acceptee') ? 'selected' : '' ?>>Acceptée</option>
                            <option value="refusee" <?= (isset($_GET['statut']) && $_GET['statut'] == 'refusee') ? 'selected' : '' ?>>Refusée</option>
                            <option value="en_cours" <?= (isset($_GET['statut']) && $_GET['statut'] == 'en_cours') ? 'selected' : '' ?>>En cours d'examen</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date du</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">au</label>
                        <input type="date" id="date_to" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="<?= \UrlHelper::url('entreprise/applications') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                            </form>
                        </div>
                    </div>

        <!-- Liste des candidatures -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Candidatures (<?= count($candidatures ?? []) ?>)</h5>
                <?php if (!empty($candidatures)): ?>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportCandidatures()">
                        <i class="fas fa-download me-1"></i>Exporter
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($candidatures)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucune candidature</h4>
                        <p class="text-muted mb-3">Vous n'avez pas encore reçu de candidatures pour vos offres.</p>
                        <a href="<?= \UrlHelper::url('entreprise/offers') ?>" class="btn btn-primary">
                            <i class="fas fa-clipboard-list me-1"></i>Voir mes offres
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Candidat</th>
                                    <th>Offre</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidatures as $candidature): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($candidature['prenom'] . ' ' . $candidature['nom']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($candidature['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($candidature['offre_titre']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($candidature['date_candidature'])) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = match($candidature['statut']) {
                                                'en_attente' => 'warning',
                                                'acceptee' => 'success',
                                                'refusee' => 'danger',
                                                'en_cours' => 'info',
                                                default => 'secondary'
                                            };
                                            $statusText = match($candidature['statut']) {
                                                'en_attente' => 'En attente',
                                                'acceptee' => 'Acceptée',
                                                'refusee' => 'Refusée',
                                                'en_cours' => 'En cours',
                                                default => 'Inconnu'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewApplication(<?= $candidature['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="acceptApplication(<?= $candidature['id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectApplication(<?= $candidature['id'] ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour les animations des cartes -->
    <script>
        // Animation des cartes de statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
            });
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

</body>
</html>

<?php
function getApplicationStatusLabel($status) {
    return match($status) {
        'en_attente' => 'En attente',
        'acceptee' => 'Acceptée',
        'refusee' => 'Refusée',
        'en_cours' => 'En cours d\'examen',
        default => 'Inconnu'
    };
}

function formatTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    if ($time < 31536000) return floor($time/2592000) . ' mois';
    return floor($time/31536000) . ' an' . (floor($time/31536000) > 1 ? 's' : '');
}
?>

            </div>
        </div>
    </div>

<?php include '../layouts/footer.php'; ?>