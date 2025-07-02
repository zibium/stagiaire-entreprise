<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Tableau de bord - Administration';
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
                <li><a href="/admin/dashboard" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="/admin/users" class="nav-link"><i class="fas fa-users"></i> Utilisateurs</a></li>
                <li><a href="/admin/offers" class="nav-link"><i class="fas fa-briefcase"></i> Offres</a></li>
                <li><a href="/admin/statistics" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <li><a href="/admin/logs" class="nav-link"><i class="fas fa-list-alt"></i> Logs</a></li>
                <li><a href="/auth/logout" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord administrateur</h1>
            <p class="welcome-text">Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></p>
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

        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card users">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_users']) ?></h3>
                    <p>Utilisateurs total</p>
                    <span class="stat-change positive">+<?= $stats['new_users_today'] ?> aujourd'hui</span>
                </div>
            </div>

            <div class="stat-card companies">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_companies']) ?></h3>
                    <p>Entreprises</p>
                    <span class="stat-detail"><?= number_format($stats['total_interns']) ?> stagiaires</span>
                </div>
            </div>

            <div class="stat-card offers">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_offers']) ?></h3>
                    <p>Offres de stage</p>
                    <span class="stat-change positive">+<?= $stats['new_offers_today'] ?> aujourd'hui</span>
                </div>
            </div>

            <div class="stat-card applications">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_applications']) ?></h3>
                    <p>Candidatures</p>
                    <span class="stat-detail"><?= number_format($stats['pending_offers']) ?> offres en attente</span>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
            <div class="actions-grid">
                <a href="/admin/users" class="action-card">
                    <i class="fas fa-user-plus"></i>
                    <span>Gérer les utilisateurs</span>
                </a>
                <a href="/admin/offers" class="action-card">
                    <i class="fas fa-check-circle"></i>
                    <span>Valider les offres</span>
                </a>
                <a href="/admin/statistics" class="action-card">
                    <i class="fas fa-chart-line"></i>
                    <span>Voir les statistiques</span>
                </a>
                <a href="/admin/export?type=users&format=csv" class="action-card">
                    <i class="fas fa-download"></i>
                    <span>Exporter les données</span>
                </a>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="main-content">
            <!-- Utilisateurs récents -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-clock"></i> Nouveaux utilisateurs</h2>
                    <a href="/admin/users" class="btn btn-outline">Voir tous</a>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="role-badge <?= $user['role'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user['status'] ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Offres en attente -->
            <div class="content-section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Offres en attente de validation</h2>
                    <a href="/admin/offers" class="btn btn-outline">Voir toutes</a>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Entreprise</th>
                                <th>Domaine</th>
                                <th>Créée le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingOffers as $offer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($offer['titre']) ?></td>
                                    <td><?= htmlspecialchars($offer['entreprise_nom']) ?></td>
                                    <td><?= htmlspecialchars($offer['domaine']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($offer['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="POST" action="/admin/moderate-offer" style="display: inline;">
                                                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="/admin/moderate-offer" style="display: inline;">
                                                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Rejeter">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Activité récente -->
            <div class="sidebar-section">
                <h3><i class="fas fa-history"></i> Activité récente</h3>
                <div class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <p class="activity-action"><?= htmlspecialchars($activity['action']) ?></p>
                                <p class="activity-user"><?= htmlspecialchars($activity['user']) ?></p>
                                <span class="activity-time"><?= timeAgo($activity['time']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Alertes système -->
            <div class="sidebar-section">
                <h3><i class="fas fa-exclamation-triangle"></i> Alertes</h3>
                <div class="alerts-list">
                    <?php if ($stats['pending_offers'] > 0): ?>
                        <div class="alert-item warning">
                            <i class="fas fa-clock"></i>
                            <span><?= $stats['pending_offers'] ?> offre(s) en attente de validation</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert-item info">
                        <i class="fas fa-chart-line"></i>
                        <span>Système fonctionnel</span>
                    </div>
                </div>
            </div>

            <!-- Raccourcis -->
            <div class="sidebar-section">
                <h3><i class="fas fa-link"></i> Raccourcis</h3>
                <div class="shortcuts-list">
                    <a href="/admin/export?type=users&format=csv" class="shortcut-item">
                        <i class="fas fa-users"></i>
                        <span>Exporter utilisateurs</span>
                    </a>
                    <a href="/admin/export?type=offers&format=csv" class="shortcut-item">
                        <i class="fas fa-briefcase"></i>
                        <span>Exporter offres</span>
                    </a>
                    <a href="/admin/logs" class="shortcut-item">
                        <i class="fas fa-list-alt"></i>
                        <span>Consulter les logs</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // Confirmation pour les actions de modération
        document.querySelectorAll('form[action="/admin/moderate-offer"] button').forEach(button => {
            button.addEventListener('click', function(e) {
                const action = this.closest('form').querySelector('input[name="action"]').value;
                const actionText = action === 'approve' ? 'approuver' : 'rejeter';
                
                if (!confirm(`Êtes-vous sûr de vouloir ${actionText} cette offre ?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

<?php
// Fonction helper pour formater le temps
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    
    return date('d/m/Y', strtotime($datetime));
}
?>