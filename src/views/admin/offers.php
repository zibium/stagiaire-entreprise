<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Gestion des offres - Administration';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-shield-alt"></i>
                <span>JobBoard Admin</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="/admin/users" class="nav-link"><i class="fas fa-users"></i> Utilisateurs</a></li>
                <li><a href="/admin/offers" class="nav-link active"><i class="fas fa-briefcase"></i> Offres</a></li>
                <li><a href="/admin/statistics" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <li><a href="/admin/logs" class="nav-link"><i class="fas fa-list-alt"></i> Logs</a></li>
                <li><a href="/auth/logout" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1><i class="fas fa-briefcase"></i> Gestion des offres de stage</h1>
            <p class="subtitle">Modération et validation des offres publiées</p>
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

        <!-- Statistiques offres -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total']) ?></h3>
                    <p>Total offres</p>
                </div>
            </div>

            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['pending']) ?></h3>
                    <p>En attente</p>
                </div>
            </div>

            <div class="stat-card published">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['published']) ?></h3>
                    <p>Publiées</p>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['rejected']) ?></h3>
                    <p>Rejetées</p>
                </div>
            </div>

            <div class="stat-card expired">
                <div class="stat-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['expired']) ?></h3>
                    <p>Expirées</p>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <div class="filters-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchOffers" placeholder="Rechercher par titre, entreprise...">
                </div>
                
                <div class="filter-group">
                    <select id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="published">Publiées</option>
                        <option value="rejected">Rejetées</option>
                        <option value="expired">Expirées</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="domainFilter">
                        <option value="">Tous les domaines</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Finance">Finance</option>
                        <option value="RH">Ressources Humaines</option>
                        <option value="Commercial">Commercial</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="dateFilter">
                        <option value="">Toutes les dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                    </select>
                </div>
                
                <button class="btn btn-outline" onclick="exportOffers()">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
            <div class="actions-grid">
                <button class="action-card" onclick="bulkApprove()">
                    <i class="fas fa-check-double"></i>
                    <span>Approuver en lot</span>
                </button>
                <button class="action-card" onclick="showPendingOnly()">
                    <i class="fas fa-clock"></i>
                    <span>Voir en attente</span>
                </button>
                <button class="action-card" onclick="showExpiringSoon()">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Expirent bientôt</span>
                </button>
                <button class="action-card" onclick="generateReport()">
                    <i class="fas fa-chart-pie"></i>
                    <span>Rapport mensuel</span>
                </button>
            </div>
        </div>

        <!-- Liste des offres -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Liste des offres</h2>
                <div class="header-actions">
                    <span class="results-count">Affichage de <span id="displayedCount"><?= count($offers) ?></span> offres</span>
                </div>
            </div>
            
            <div class="table-container">
                <table class="data-table" id="offersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Entreprise</th>
                            <th>Domaine</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th>Expire le</th>
                            <th>Candidatures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offers as $offer): ?>
                            <tr data-status="<?= $offer['status'] ?>" data-domain="<?= $offer['domaine'] ?>" data-date="<?= $offer['created_at'] ?>">
                                <td><input type="checkbox" class="offer-checkbox" value="<?= $offer['id'] ?>"></td>
                                <td><?= $offer['id'] ?></td>
                                <td>
                                    <div class="offer-title">
                                        <strong><?= htmlspecialchars($offer['titre']) ?></strong>
                                        <?php if ($offer['status'] === 'pending'): ?>
                                            <span class="badge new">Nouveau</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="company-info">
                                        <?= htmlspecialchars($offer['entreprise_nom']) ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($offer['entreprise_email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="domain-badge"><?= htmlspecialchars($offer['domaine']) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $offer['status'] ?>">
                                        <i class="fas fa-<?= getStatusIcon($offer['status']) ?>"></i>
                                        <?= getStatusLabel($offer['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($offer['created_at'])) ?></td>
                                <td>
                                    <?php if ($offer['date_limite']): ?>
                                        <?php 
                                        $daysLeft = floor((strtotime($offer['date_limite']) - time()) / 86400);
                                        $class = $daysLeft <= 7 ? 'text-danger' : ($daysLeft <= 14 ? 'text-warning' : 'text-success');
                                        ?>
                                        <span class="<?= $class ?>">
                                            <?= date('d/m/Y', strtotime($offer['date_limite'])) ?>
                                            <br><small>(<?= $daysLeft ?> jours)</small>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Non définie</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="applications-count">
                                        <?= $offer['candidatures_count'] ?? 0 ?>
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-info" onclick="viewOffer(<?= $offer['id'] ?>)" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($offer['status'] === 'pending'): ?>
                                            <form method="POST" action="/admin/moderate-offer" style="display: inline;">
                                                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approuver" onclick="return confirm('Approuver cette offre ?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <button class="btn btn-sm btn-danger" onclick="rejectOffer(<?= $offer['id'] ?>)" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif ($offer['status'] === 'published'): ?>
                                            <button class="btn btn-sm btn-warning" onclick="pauseOffer(<?= $offer['id'] ?>)" title="Suspendre">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        <?php elseif ($offer['status'] === 'rejected'): ?>
                                            <button class="btn btn-sm btn-success" onclick="reactivateOffer(<?= $offer['id'] ?>)" title="Réactiver">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-primary" onclick="contactCompany('<?= htmlspecialchars($offer['entreprise_email']) ?>')" title="Contacter entreprise">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal détails offre -->
    <div id="offerDetailsModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3><i class="fas fa-briefcase"></i> Détails de l'offre</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="offerDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Modal rejet offre -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-times-circle"></i> Rejeter l'offre</h3>
                <span class="close" onclick="closeRejectModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="rejectForm" method="POST" action="/admin/moderate-offer">
                    <input type="hidden" name="offer_id" id="rejectOfferId">
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="form-group">
                        <label for="rejectReason">Motif du rejet :</label>
                        <textarea name="reason" id="rejectReason" rows="4" class="form-control" placeholder="Expliquez pourquoi cette offre est rejetée..." required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Annuler</button>
                        <button type="submit" class="btn btn-danger">Rejeter l'offre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Recherche et filtrage
        document.getElementById('searchOffers').addEventListener('input', filterOffers);
        document.getElementById('statusFilter').addEventListener('change', filterOffers);
        document.getElementById('domainFilter').addEventListener('change', filterOffers);
        document.getElementById('dateFilter').addEventListener('change', filterOffers);

        function filterOffers() {
            const searchTerm = document.getElementById('searchOffers').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const domainFilter = document.getElementById('domainFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('#offersTable tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.cells[2].textContent.toLowerCase();
                const company = row.cells[3].textContent.toLowerCase();
                const status = row.dataset.status;
                const domain = row.dataset.domain;
                const date = new Date(row.dataset.date);
                const now = new Date();

                const matchesSearch = title.includes(searchTerm) || company.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesDomain = !domainFilter || domain === domainFilter;
                
                let matchesDate = true;
                if (dateFilter === 'today') {
                    matchesDate = date.toDateString() === now.toDateString();
                } else if (dateFilter === 'week') {
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = date >= weekAgo;
                } else if (dateFilter === 'month') {
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = date >= monthAgo;
                }

                if (matchesSearch && matchesStatus && matchesDomain && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('displayedCount').textContent = visibleCount;
        }

        // Sélection multiple
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.offer-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Voir détails offre
        function viewOffer(offerId) {
            document.getElementById('offerDetailsContent').innerHTML = `
                <div class="loading">Chargement des détails...</div>
            `;
            document.getElementById('offerDetailsModal').style.display = 'block';
            
            // Simuler le chargement
            setTimeout(() => {
                document.getElementById('offerDetailsContent').innerHTML = `
                    <div class="offer-details">
                        <h4>Détails complets de l'offre #${offerId}</h4>
                        <p><strong>Description:</strong> Contenu détaillé de l'offre...</p>
                        <p><strong>Compétences requises:</strong> Liste des compétences...</p>
                        <p><strong>Conditions:</strong> Durée, rémunération, etc.</p>
                        <div class="mt-3">
                            <button class="btn btn-success" onclick="approveOfferFromModal(${offerId})">Approuver</button>
                            <button class="btn btn-danger" onclick="rejectOfferFromModal(${offerId})">Rejeter</button>
                        </div>
                    </div>
                `;
            }, 500);
        }

        // Rejeter offre
        function rejectOffer(offerId) {
            document.getElementById('rejectOfferId').value = offerId;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function rejectOfferFromModal(offerId) {
            closeModal();
            rejectOffer(offerId);
        }

        function approveOfferFromModal(offerId) {
            if (confirm('Approuver cette offre ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/moderate-offer';
                form.innerHTML = `
                    <input type="hidden" name="offer_id" value="${offerId}">
                    <input type="hidden" name="action" value="approve">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Actions rapides
        function showPendingOnly() {
            document.getElementById('statusFilter').value = 'pending';
            filterOffers();
        }

        function showExpiringSoon() {
            // Logique pour afficher les offres qui expirent bientôt
            alert('Fonctionnalité en développement');
        }

        function bulkApprove() {
            const selected = document.querySelectorAll('.offer-checkbox:checked');
            if (selected.length === 0) {
                alert('Veuillez sélectionner au moins une offre.');
                return;
            }
            
            if (confirm(`Approuver ${selected.length} offre(s) sélectionnée(s) ?`)) {
                // Logique d'approbation en lot
                alert('Fonctionnalité en développement');
            }
        }

        function generateReport() {
            window.open('/admin/reports/offers', '_blank');
        }

        function exportOffers() {
            const statusFilter = document.getElementById('statusFilter').value;
            const domainFilter = document.getElementById('domainFilter').value;
            
            let url = '/admin/export?type=offers&format=csv';
            if (statusFilter) url += `&status=${statusFilter}`;
            if (domainFilter) url += `&domain=${domainFilter}`;
            
            window.location.href = url;
        }

        function contactCompany(email) {
            window.location.href = `mailto:${email}`;
        }

        function pauseOffer(offerId) {
            if (confirm('Suspendre cette offre ?')) {
                // Logique de suspension
                alert('Fonctionnalité en développement');
            }
        }

        function reactivateOffer(offerId) {
            if (confirm('Réactiver cette offre ?')) {
                // Logique de réactivation
                alert('Fonctionnalité en développement');
            }
        }

        // Fermer modals
        function closeModal() {
            document.getElementById('offerDetailsModal').style.display = 'none';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectForm').reset();
        }

        // Fermer modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const detailsModal = document.getElementById('offerDetailsModal');
            const rejectModal = document.getElementById('rejectModal');
            
            if (event.target === detailsModal) {
                detailsModal.style.display = 'none';
            }
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
        }

        // Animation des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Auto-hide des alertes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>

    <style>
        .modal-content.large {
            width: 90%;
            max-width: 800px;
        }

        .offer-title strong {
            display: block;
            margin-bottom: 5px;
        }

        .badge.new {
            background: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .company-info {
            line-height: 1.4;
        }

        .domain-badge {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .applications-count {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-success { color: #28a745; }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .mt-3 {
            margin-top: 1rem;
        }
    </style>
</body>
</html>

<?php
// Fonctions helper
function getStatusIcon($status) {
    switch ($status) {
        case 'pending': return 'clock';
        case 'published': return 'check-circle';
        case 'rejected': return 'times-circle';
        case 'expired': return 'calendar-times';
        default: return 'question-circle';
    }
}

function getStatusLabel($status) {
    switch ($status) {
        case 'pending': return 'En attente';
        case 'published': return 'Publiée';
        case 'rejected': return 'Rejetée';
        case 'expired': return 'Expirée';
        default: return 'Inconnu';
    }
}
?>