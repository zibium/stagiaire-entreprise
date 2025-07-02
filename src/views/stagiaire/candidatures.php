<?php
// Vérification de l'authentification
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'stagiaire') {
    header('Location: /Dev1/public/auth/login');
    exit;
}

// Récupération des données passées par le contrôleur
$candidatures = $candidatures ?? [];
$totalCandidatures = $totalCandidatures ?? 0;
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$filters = $filters ?? [];
$statistiques = $statistiques ?? [];

require_once __DIR__ . '/../../utils/UrlHelper.php';

// Fonction helper pour formater les dates
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Fonction helper pour formater les dates avec heure
function formatDateTime($date) {
    return date('d/m/Y à H:i', strtotime($date));
}

// Fonction helper pour obtenir l'icône du statut
function getStatusIcon($statut) {
    switch ($statut) {
        case 'en_attente':
            return 'fas fa-clock';
        case 'acceptee':
            return 'fas fa-check-circle';
        case 'refusee':
            return 'fas fa-times-circle';
        case 'entretien':
            return 'fas fa-handshake';
        default:
            return 'fas fa-question-circle';
    }
}

// Fonction helper pour obtenir la couleur du statut
function getStatusColor($statut) {
    switch ($statut) {
        case 'en_attente':
            return '#ffc107';
        case 'acceptee':
            return '#28a745';
        case 'refusee':
            return '#dc3545';
        case 'entretien':
            return '#17a2b8';
        default:
            return '#6c757d';
    }
}

// Fonction helper pour obtenir le libellé du statut
function getStatusLabel($statut) {
    switch ($statut) {
        case 'en_attente':
            return 'En attente';
        case 'acceptee':
            return 'Acceptée';
        case 'refusee':
            return 'Refusée';
        case 'entretien':
            return 'Entretien';
        default:
            return 'Inconnu';
    }
}
// Configuration du layout
$pageTitle = 'Mes Candidatures - JobBoard';
$pageDescription = 'Suivez l\'état de vos candidatures et gérez vos postulations';
$bodyClass = 'candidatures-page';
$customCSS = '
        .applications-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-total {
            color: #007bff;
        }

        .stat-pending {
            color: #ffc107;
        }

        .stat-accepted {
            color: #28a745;
        }

        .stat-rejected {
            color: #dc3545;
        }

        .stat-interview {
            color: #17a2b8;
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .applications-grid {
            display: grid;
            gap: 20px;
        }

        .application-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .application-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .application-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin: 0 0 5px 0;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .company-logo {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            object-fit: cover;
        }

        .company-name {
            font-weight: 500;
            color: #666;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: white;
        }

        .application-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #666;
        }

        .meta-item i {
            color: #007bff;
            width: 16px;
        }

        .application-dates {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .dates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .date-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .date-label {
            font-weight: 500;
            color: #333;
        }

        .date-value {
            color: #666;
        }

        .application-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .no-applications {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-applications i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .pagination .current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination a:hover {
            background: #f8f9fa;
        }

        .comment-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .comment-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 5px;
        }

        .comment-text {
            color: #856404;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .application-header {
                flex-direction: column;
                gap: 10px;
            }

            .application-meta {
                grid-template-columns: 1fr;
            }

            .dates-grid {
                grid-template-columns: 1fr;
            }

            .application-actions {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
        }';

// Début du contenu
ob_start();
?>

    <div class="applications-container">
        <div class="page-header">
            <h1><i class="fas fa-file-alt"></i> Mes Candidatures</h1>
            <p>Suivez l'état de vos candidatures et gérez vos postulations</p>
        </div>

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

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-number"><?= $statistiques['total'] ?? 0 ?></div>
                <div class="stat-label">Total candidatures</div>
            </div>

            <div class="stat-card stat-pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?= $statistiques['en_attente'] ?? 0 ?></div>
                <div class="stat-label">En attente</div>
            </div>

            <div class="stat-card stat-interview">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-number"><?= $statistiques['entretiens'] ?? 0 ?></div>
                <div class="stat-label">Entretiens</div>
            </div>

            <div class="stat-card stat-accepted">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?= $statistiques['acceptees'] ?? 0 ?></div>
                <div class="stat-label">Acceptées</div>
            </div>

            <div class="stat-card stat-rejected">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-number"><?= $statistiques['refusees'] ?? 0 ?></div>
                <div class="stat-label">Refusées</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <form method="GET" action="/stagiaire/applications">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="statut">Statut</label>
                        <select id="statut" name="statut" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?= ($filters['statut'] ?? '') === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="entretien" <?= ($filters['statut'] ?? '') === 'entretien' ? 'selected' : '' ?>>Entretien</option>
                            <option value="acceptee" <?= ($filters['statut'] ?? '') === 'acceptee' ? 'selected' : '' ?>>Acceptée</option>
                            <option value="refusee" <?= ($filters['statut'] ?? '') === 'refusee' ? 'selected' : '' ?>>Refusée</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="periode">Période</label>
                        <select id="periode" name="periode" onchange="this.form.submit()">
                            <option value="">Toutes les périodes</option>
                            <option value="7" <?= ($filters['periode'] ?? '') === '7' ? 'selected' : '' ?>>7 derniers jours</option>
                            <option value="30" <?= ($filters['periode'] ?? '') === '30' ? 'selected' : '' ?>>30 derniers jours</option>
                            <option value="90" <?= ($filters['periode'] ?? '') === '90' ? 'selected' : '' ?>>3 derniers mois</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des candidatures -->
        <div class="applications-grid">
            <?php if (empty($candidatures)): ?>
                <div class="no-applications">
                    <i class="fas fa-file-alt"></i>
                    <h3>Aucune candidature trouvée</h3>
                    <p>Vous n'avez pas encore postulé à des offres de stage.</p>
                    <a href="/stagiaire/offers" class="btn btn-primary">
                        <i class="fas fa-search"></i> Découvrir les offres
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($candidatures as $candidature): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <div>
                                <h3 class="application-title"><?= htmlspecialchars($candidature['titre']) ?></h3>
                                <div class="company-info">
                                    <?php if (!empty($candidature['logo_path'])): ?>
                                        <img src="<?= htmlspecialchars($candidature['logo_path']) ?>"
                                             alt="Logo <?= htmlspecialchars($candidature['nom_entreprise']) ?>"
                                             class="company-logo">
                                    <?php else: ?>
                                        <div class="company-logo" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-building" style="color: #666;"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="company-name"><?= htmlspecialchars($candidature['nom_entreprise']) ?></span>
                                </div>
                            </div>

                            <div class="status-badge" style="background-color: <?= getStatusColor($candidature['statut']) ?>">
                                <i class="<?= getStatusIcon($candidature['statut']) ?>"></i>
                                <?= getStatusLabel($candidature['statut']) ?>
                            </div>
                        </div>

                        <div class="application-meta">
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($candidature['ville'] ?? 'Non spécifié') ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-briefcase"></i>
                                <span><?= htmlspecialchars($candidature['type_contrat']) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span><?= htmlspecialchars($candidature['duree']) ?> mois</span>
                            </div>
                        </div>

                        <div class="application-dates">
                            <div class="dates-grid">
                                <div class="date-item">
                                    <span class="date-label">Candidature envoyée :</span>
                                    <span class="date-value"><?= formatDateTime($candidature['date_candidature']) ?></span>
                                </div>
                                <?php if (!empty($candidature['date_reponse'])): ?>
                                    <div class="date-item">
                                        <span class="date-label">Réponse reçue :</span>
                                        <span class="date-value"><?= formatDateTime($candidature['date_reponse']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($candidature['commentaire_entreprise'])): ?>
                            <div class="comment-section">
                                <div class="comment-title">
                                    <i class="fas fa-comment"></i> Commentaire de l'entreprise :
                                </div>
                                <div class="comment-text">
                                    <?= nl2br(htmlspecialchars($candidature['commentaire_entreprise'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="application-actions">
                            <div>
                                <small class="text-muted">
                                    ID: #<?= $candidature['id'] ?>
                                </small>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <a href="/stagiaire/offers/<?= $candidature['offre_id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-eye"></i> Voir l'offre
                                </a>

                                <?php if ($candidature['statut'] === 'en_attente'): ?>
                                    <button onclick="confirmWithdraw(<?= $candidature['id'] ?>)" class="btn btn-danger">
                                        <i class="fas fa-times"></i> Retirer
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => 1])) ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);

                for ($i = $start; $i <= $end; $i++):
                ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?<?= http_build_query(array_merge($filters, ['page' => $totalPages])) ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Confirmation pour retirer une candidature
        function confirmWithdraw(candidatureId) {
            if (confirm('Êtes-vous sûr de vouloir retirer cette candidature ? Cette action est irréversible.')) {
                // Créer un formulaire pour envoyer la requête DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/stagiaire/applications/' + candidatureId + '/withdraw';

                // Ajouter un token CSRF si nécessaire
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = csrfToken.getAttribute('content');
                    form.appendChild(tokenInput);
                }

                document.body.appendChild(form);
                form.submit();
            }
        }

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
    </script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>