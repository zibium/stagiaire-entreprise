<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Gestion des utilisateurs - Administration';
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
                <li><a href="/admin/users" class="nav-link active"><i class="fas fa-users"></i> Utilisateurs</a></li>
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
            <h1><i class="fas fa-users"></i> Gestion des utilisateurs</h1>
            <p class="subtitle">Administration et modération des comptes utilisateurs</p>
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

        <!-- Statistiques utilisateurs -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total']) ?></h3>
                    <p>Total utilisateurs</p>
                </div>
            </div>

            <div class="stat-card active">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['active']) ?></h3>
                    <p>Actifs</p>
                </div>
            </div>

            <div class="stat-card inactive">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['inactive']) ?></h3>
                    <p>Inactifs</p>
                </div>
            </div>

            <div class="stat-card companies">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['companies']) ?></h3>
                    <p>Entreprises</p>
                </div>
            </div>

            <div class="stat-card interns">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['interns']) ?></h3>
                    <p>Stagiaires</p>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <div class="filters-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchUsers" placeholder="Rechercher par nom, email...">
                </div>
                
                <div class="filter-group">
                    <select id="roleFilter">
                        <option value="">Tous les rôles</option>
                        <option value="entreprise">Entreprises</option>
                        <option value="stagiaire">Stagiaires</option>
                        <option value="admin">Administrateurs</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select id="statusFilter">
                        <option value="">Tous les statuts</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                </div>
                
                <button class="btn btn-outline" onclick="exportUsers()">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Liste des utilisateurs</h2>
                <div class="header-actions">
                    <span class="results-count">Affichage de <span id="displayedCount"><?= count($users) ?></span> utilisateurs</span>
                </div>
            </div>
            
            <div class="table-container">
                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Dernière connexion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr data-role="<?= $user['role'] ?>" data-status="<?= $user['status'] ?>">
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></strong>
                                        <?php if ($user['role'] === 'entreprise' && !empty($user['entreprise_nom'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($user['entreprise_nom']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </a>
                                    <?php if ($user['email_verified']): ?>
                                        <i class="fas fa-check-circle text-success" title="Email vérifié"></i>
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-circle text-warning" title="Email non vérifié"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="role-badge <?= $user['role'] ?>">
                                        <i class="fas fa-<?= getRoleIcon($user['role']) ?>"></i>
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?= $user['status'] ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['last_login']): ?>
                                        <?= timeAgo($user['last_login']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Jamais</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                            <form method="POST" action="/admin/toggle-user-status" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Désactiver" onclick="return confirm('Désactiver cet utilisateur ?')">
                                                        <i class="fas fa-user-times"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Activer" onclick="return confirm('Activer cet utilisateur ?')">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                            
                                            <button class="btn btn-sm btn-info" onclick="viewUserDetails(<?= $user['id'] ?>)" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-primary" onclick="contactUser('<?= htmlspecialchars($user['email']) ?>')" title="Contacter">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">Vous</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal détails utilisateur -->
    <div id="userDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user"></i> Détails de l'utilisateur</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        // Recherche et filtrage
        document.getElementById('searchUsers').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);
        document.getElementById('statusFilter').addEventListener('change', filterUsers);

        function filterUsers() {
            const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#usersTable tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const role = row.dataset.role;
                const status = row.dataset.status;

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesRole = !roleFilter || role === roleFilter;
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesRole && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('displayedCount').textContent = visibleCount;
        }

        // Voir détails utilisateur
        function viewUserDetails(userId) {
            // Simulation - à remplacer par un appel AJAX
            document.getElementById('userDetailsContent').innerHTML = `
                <div class="loading">Chargement des détails...</div>
            `;
            document.getElementById('userDetailsModal').style.display = 'block';
            
            // Simuler le chargement
            setTimeout(() => {
                document.getElementById('userDetailsContent').innerHTML = `
                    <div class="user-details">
                        <p><strong>ID:</strong> ${userId}</p>
                        <p><strong>Profil complet:</strong> En cours de développement</p>
                        <p><strong>Activité récente:</strong> Connexions, actions...</p>
                    </div>
                `;
            }, 500);
        }

        // Contacter utilisateur
        function contactUser(email) {
            window.location.href = `mailto:${email}`;
        }

        // Exporter utilisateurs
        function exportUsers() {
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            let url = '/admin/export?type=users&format=csv';
            if (roleFilter) url += `&role=${roleFilter}`;
            if (statusFilter) url += `&status=${statusFilter}`;
            
            window.location.href = url;
        }

        // Fermer modal
        function closeModal() {
            document.getElementById('userDetailsModal').style.display = 'none';
        }

        // Fermer modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('userDetailsModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Animation des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 20px;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #000;
        }

        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filters-container {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            min-width: 150px;
        }

        .user-info strong {
            display: block;
        }

        .text-muted {
            color: #666;
            font-size: 12px;
        }

        .text-success {
            color: #28a745;
        }

        .text-warning {
            color: #ffc107;
        }

        .results-count {
            font-size: 14px;
            color: #666;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</body>
</html>

<?php
// Fonctions helper
function getRoleIcon($role) {
    switch ($role) {
        case 'admin': return 'shield-alt';
        case 'entreprise': return 'building';
        case 'stagiaire': return 'graduation-cap';
        default: return 'user';
    }
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    
    return date('d/m/Y', strtotime($datetime));
}
?>