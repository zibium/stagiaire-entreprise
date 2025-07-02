<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Logs d\'activité - Administration';

// Fonction helper pour formater le temps
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    if ($time < 31536000) return floor($time/2592000) . ' mois';
    return floor($time/31536000) . ' ans';
}

// Fonction helper pour les icônes d'action
function getActionIcon($action) {
    $icons = [
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'create' => 'fas fa-plus',
        'update' => 'fas fa-edit',
        'delete' => 'fas fa-trash',
        'approve' => 'fas fa-check',
        'reject' => 'fas fa-times',
        'upload' => 'fas fa-upload',
        'download' => 'fas fa-download',
        'view' => 'fas fa-eye',
        'email' => 'fas fa-envelope',
        'export' => 'fas fa-file-export',
        'import' => 'fas fa-file-import'
    ];
    return $icons[$action] ?? 'fas fa-info-circle';
}

// Fonction helper pour les couleurs d'action
function getActionColor($action) {
    $colors = [
        'login' => 'success',
        'logout' => 'info',
        'create' => 'primary',
        'update' => 'warning',
        'delete' => 'danger',
        'approve' => 'success',
        'reject' => 'danger',
        'upload' => 'info',
        'download' => 'secondary',
        'view' => 'light',
        'email' => 'info',
        'export' => 'secondary',
        'import' => 'primary'
    ];
    return $colors[$action] ?? 'secondary';
}
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
                <li><a href="/admin/offers" class="nav-link"><i class="fas fa-briefcase"></i> Offres</a></li>
                <li><a href="/admin/statistics" class="nav-link"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <li><a href="/admin/logs" class="nav-link active"><i class="fas fa-list-alt"></i> Logs</a></li>
                <li><a href="/auth/logout" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1><i class="fas fa-list-alt"></i> Logs d'activité</h1>
            <p class="subtitle">Suivi des actions et événements de la plateforme</p>
        </div>

        <!-- Messages de succès/erreur -->
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

        <!-- Filtres et actions -->
        <div class="filters-section">
            <div class="filters-container">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="filterAction">Action :</label>
                        <select id="filterAction" class="filter-select">
                            <option value="">Toutes les actions</option>
                            <option value="login">Connexions</option>
                            <option value="logout">Déconnexions</option>
                            <option value="create">Créations</option>
                            <option value="update">Modifications</option>
                            <option value="delete">Suppressions</option>
                            <option value="approve">Approbations</option>
                            <option value="reject">Rejets</option>
                            <option value="upload">Uploads</option>
                            <option value="email">Emails</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterUser">Utilisateur :</label>
                        <input type="text" id="filterUser" class="filter-input" placeholder="Nom ou email...">
                    </div>

                    <div class="filter-group">
                        <label for="filterLevel">Niveau :</label>
                        <select id="filterLevel" class="filter-select">
                            <option value="">Tous les niveaux</option>
                            <option value="info">Info</option>
                            <option value="warning">Avertissement</option>
                            <option value="error">Erreur</option>
                            <option value="critical">Critique</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filterDate">Date :</label>
                        <input type="date" id="filterDate" class="filter-input">
                    </div>

                    <div class="filter-actions">
                        <button class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <button class="btn btn-outline" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Effacer
                        </button>
                    </div>
                </div>

                <div class="quick-actions">
                    <button class="btn btn-outline" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                    <button class="btn btn-outline" onclick="exportLogs()">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <button class="btn btn-danger" onclick="clearOldLogs()">
                        <i class="fas fa-trash"></i> Nettoyer anciens logs
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="logs-stats">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon info">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">1,234</span>
                        <span class="stat-label">Événements aujourd'hui</span>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">23</span>
                        <span class="stat-label">Avertissements</span>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon error">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">5</span>
                        <span class="stat-label">Erreurs</span>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">89</span>
                        <span class="stat-label">Utilisateurs actifs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des logs -->
        <div class="logs-section">
            <div class="section-header">
                <h3><i class="fas fa-history"></i> Historique des activités</h3>
                <div class="pagination-info">
                    <span>Affichage de 1-50 sur 1,234 entrées</span>
                </div>
            </div>

            <div class="logs-container">
                <div class="logs-table-wrapper">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th>Horodatage</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Détails</th>
                                <th>IP</th>
                                <th>Niveau</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <?php 
                            // Données de démonstration
                            $sampleLogs = [
                                [
                                    'id' => 1,
                                    'timestamp' => '2024-01-15 14:30:25',
                                    'user_name' => 'Admin User',
                                    'user_email' => 'admin@jobboard.com',
                                    'action' => 'approve',
                                    'details' => 'Approbation de l\'offre "Développeur PHP Senior" de TechCorp',
                                    'ip_address' => '192.168.1.100',
                                    'level' => 'info',
                                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                                ],
                                [
                                    'id' => 2,
                                    'timestamp' => '2024-01-15 14:25:12',
                                    'user_name' => 'Marie Dubois',
                                    'user_email' => 'marie@techcorp.com',
                                    'action' => 'create',
                                    'details' => 'Création d\'une nouvelle offre de stage',
                                    'ip_address' => '203.0.113.45',
                                    'level' => 'info',
                                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'
                                ],
                                [
                                    'id' => 3,
                                    'timestamp' => '2024-01-15 14:20:08',
                                    'user_name' => 'Pierre Martin',
                                    'user_email' => 'pierre@email.com',
                                    'action' => 'login',
                                    'details' => 'Connexion réussie',
                                    'ip_address' => '198.51.100.23',
                                    'level' => 'info',
                                    'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64)'
                                ],
                                [
                                    'id' => 4,
                                    'timestamp' => '2024-01-15 14:15:33',
                                    'user_name' => 'Système',
                                    'user_email' => 'system@jobboard.com',
                                    'action' => 'delete',
                                    'details' => 'Suppression automatique des offres expirées (3 offres)',
                                    'ip_address' => '127.0.0.1',
                                    'level' => 'warning',
                                    'user_agent' => 'JobBoard-Cron/1.0'
                                ],
                                [
                                    'id' => 5,
                                    'timestamp' => '2024-01-15 14:10:45',
                                    'user_name' => 'Julie Leroy',
                                    'user_email' => 'julie@email.com',
                                    'action' => 'upload',
                                    'details' => 'Upload du CV (cv_julie_leroy.pdf)',
                                    'ip_address' => '203.0.113.67',
                                    'level' => 'info',
                                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
                                ],
                                [
                                    'id' => 6,
                                    'timestamp' => '2024-01-15 14:05:19',
                                    'user_name' => 'Admin User',
                                    'user_email' => 'admin@jobboard.com',
                                    'action' => 'reject',
                                    'details' => 'Rejet de l\'offre "Stage Marketing" - Contenu inapproprié',
                                    'ip_address' => '192.168.1.100',
                                    'level' => 'warning',
                                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
                                ],
                                [
                                    'id' => 7,
                                    'timestamp' => '2024-01-15 14:00:02',
                                    'user_name' => 'Système',
                                    'user_email' => 'system@jobboard.com',
                                    'action' => 'email',
                                    'details' => 'Envoi de notifications quotidiennes (45 emails)',
                                    'ip_address' => '127.0.0.1',
                                    'level' => 'info',
                                    'user_agent' => 'JobBoard-Mailer/1.0'
                                ],
                                [
                                    'id' => 8,
                                    'timestamp' => '2024-01-15 13:55:28',
                                    'user_name' => 'Thomas Petit',
                                    'user_email' => 'thomas@startup.com',
                                    'action' => 'update',
                                    'details' => 'Modification du profil entreprise',
                                    'ip_address' => '198.51.100.89',
                                    'level' => 'info',
                                    'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'
                                ]
                            ];
                            
                            foreach ($sampleLogs as $log): ?>
                                <tr class="log-row" data-level="<?= $log['level'] ?>" data-action="<?= $log['action'] ?>">
                                    <td class="timestamp-cell">
                                        <div class="timestamp">
                                            <span class="date"><?= date('d/m/Y', strtotime($log['timestamp'])) ?></span>
                                            <span class="time"><?= date('H:i:s', strtotime($log['timestamp'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="user-cell">
                                        <div class="user-info">
                                            <span class="user-name"><?= htmlspecialchars($log['user_name']) ?></span>
                                            <span class="user-email"><?= htmlspecialchars($log['user_email']) ?></span>
                                        </div>
                                    </td>
                                    <td class="action-cell">
                                        <div class="action-badge <?= getActionColor($log['action']) ?>">
                                            <i class="<?= getActionIcon($log['action']) ?>"></i>
                                            <span><?= ucfirst($log['action']) ?></span>
                                        </div>
                                    </td>
                                    <td class="details-cell">
                                        <div class="details-content">
                                            <?= htmlspecialchars($log['details']) ?>
                                        </div>
                                    </td>
                                    <td class="ip-cell">
                                        <code><?= htmlspecialchars($log['ip_address']) ?></code>
                                    </td>
                                    <td class="level-cell">
                                        <span class="level-badge <?= $log['level'] ?>">
                                            <?= ucfirst($log['level']) ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewLogDetails(<?= $log['id'] ?>)" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-icon" onclick="copyLogInfo(<?= $log['id'] ?>)" title="Copier">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    <div class="pagination">
                        <button class="page-btn" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <span class="page-dots">...</span>
                        <button class="page-btn">25</button>
                        <button class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="page-size">
                        <label>Afficher :</label>
                        <select onchange="changePageSize(this.value)">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                        <span>par page</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal détails du log -->
    <div id="logDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> Détails du log</h3>
                <button class="modal-close" onclick="closeLogDetails()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="log-details">
                    <div class="detail-row">
                        <label>ID :</label>
                        <span id="detailId"></span>
                    </div>
                    <div class="detail-row">
                        <label>Horodatage :</label>
                        <span id="detailTimestamp"></span>
                    </div>
                    <div class="detail-row">
                        <label>Utilisateur :</label>
                        <span id="detailUser"></span>
                    </div>
                    <div class="detail-row">
                        <label>Action :</label>
                        <span id="detailAction"></span>
                    </div>
                    <div class="detail-row">
                        <label>Détails :</label>
                        <span id="detailDescription"></span>
                    </div>
                    <div class="detail-row">
                        <label>Adresse IP :</label>
                        <span id="detailIP"></span>
                    </div>
                    <div class="detail-row">
                        <label>User Agent :</label>
                        <span id="detailUserAgent"></span>
                    </div>
                    <div class="detail-row">
                        <label>Niveau :</label>
                        <span id="detailLevel"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeLogDetails()">Fermer</button>
                <button class="btn btn-primary" onclick="exportSingleLog()">Exporter ce log</button>
            </div>
        </div>
    </div>

    <script>
        // Gestion des filtres
        function applyFilters() {
            const action = document.getElementById('filterAction').value;
            const user = document.getElementById('filterUser').value.toLowerCase();
            const level = document.getElementById('filterLevel').value;
            const date = document.getElementById('filterDate').value;
            
            const rows = document.querySelectorAll('.log-row');
            
            rows.forEach(row => {
                let show = true;
                
                // Filtre par action
                if (action && row.dataset.action !== action) {
                    show = false;
                }
                
                // Filtre par utilisateur
                if (user) {
                    const userName = row.querySelector('.user-name').textContent.toLowerCase();
                    const userEmail = row.querySelector('.user-email').textContent.toLowerCase();
                    if (!userName.includes(user) && !userEmail.includes(user)) {
                        show = false;
                    }
                }
                
                // Filtre par niveau
                if (level && row.dataset.level !== level) {
                    show = false;
                }
                
                // Filtre par date
                if (date) {
                    const rowDate = row.querySelector('.date').textContent;
                    const filterDate = new Date(date).toLocaleDateString('fr-FR');
                    if (rowDate !== filterDate) {
                        show = false;
                    }
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        function clearFilters() {
            document.getElementById('filterAction').value = '';
            document.getElementById('filterUser').value = '';
            document.getElementById('filterLevel').value = '';
            document.getElementById('filterDate').value = '';
            
            document.querySelectorAll('.log-row').forEach(row => {
                row.style.display = '';
            });
        }
        
        function refreshLogs() {
            // Simuler le rechargement
            location.reload();
        }
        
        function exportLogs() {
            const filters = {
                action: document.getElementById('filterAction').value,
                user: document.getElementById('filterUser').value,
                level: document.getElementById('filterLevel').value,
                date: document.getElementById('filterDate').value
            };
            
            const params = new URLSearchParams(filters).toString();
            window.location.href = `/admin/logs/export?${params}`;
        }
        
        function clearOldLogs() {
            if (confirm('Êtes-vous sûr de vouloir supprimer les logs de plus de 30 jours ?')) {
                // Simuler la suppression
                alert('Logs anciens supprimés avec succès');
            }
        }
        
        function changePageSize(size) {
            // Simuler le changement de taille de page
            console.log('Changement de taille de page:', size);
        }
        
        // Gestion des détails du log
        function viewLogDetails(logId) {
            // Simuler la récupération des détails
            const sampleData = {
                id: logId,
                timestamp: '2024-01-15 14:30:25',
                user: 'Admin User (admin@jobboard.com)',
                action: 'Approbation',
                description: 'Approbation de l\'offre "Développeur PHP Senior" de TechCorp',
                ip: '192.168.1.100',
                userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                level: 'Info'
            };
            
            document.getElementById('detailId').textContent = sampleData.id;
            document.getElementById('detailTimestamp').textContent = sampleData.timestamp;
            document.getElementById('detailUser').textContent = sampleData.user;
            document.getElementById('detailAction').textContent = sampleData.action;
            document.getElementById('detailDescription').textContent = sampleData.description;
            document.getElementById('detailIP').textContent = sampleData.ip;
            document.getElementById('detailUserAgent').textContent = sampleData.userAgent;
            document.getElementById('detailLevel').textContent = sampleData.level;
            
            document.getElementById('logDetailsModal').style.display = 'flex';
        }
        
        function closeLogDetails() {
            document.getElementById('logDetailsModal').style.display = 'none';
        }
        
        function copyLogInfo(logId) {
            // Simuler la copie
            navigator.clipboard.writeText(`Log ID: ${logId}`).then(() => {
                alert('Informations du log copiées dans le presse-papiers');
            });
        }
        
        function exportSingleLog() {
            const logId = document.getElementById('detailId').textContent;
            window.location.href = `/admin/logs/export/${logId}`;
        }
        
        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('logDetailsModal');
            if (event.target === modal) {
                closeLogDetails();
            }
        }
        
        // Auto-actualisation toutes les 30 secondes
        setInterval(() => {
            // Vérifier s'il y a de nouveaux logs
            console.log('Vérification de nouveaux logs...');
        }, 30000);
        
        // Animation des alertes
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>

    <style>
        .filters-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .filters-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .filters-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
        
        .filter-select,
        .filter-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 150px;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .logs-stats {
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .stat-icon.info { background: #e3f2fd; color: #1976d2; }
        .stat-icon.warning { background: #fff3e0; color: #f57c00; }
        .stat-icon.error { background: #ffebee; color: #d32f2f; }
        .stat-icon.success { background: #e8f5e8; color: #388e3c; }
        
        .stat-content {
            display: flex;
            flex-direction: column;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .logs-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .logs-table-wrapper {
            overflow-x: auto;
        }
        
        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .logs-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }
        
        .logs-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .log-row:hover {
            background: #f8f9fa;
        }
        
        .timestamp-cell {
            min-width: 120px;
        }
        
        .timestamp {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .timestamp .date {
            font-weight: 500;
            font-size: 14px;
        }
        
        .timestamp .time {
            font-size: 12px;
            color: #666;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .user-name {
            font-weight: 500;
            font-size: 14px;
        }
        
        .user-email {
            font-size: 12px;
            color: #666;
        }
        
        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .action-badge.primary { background: #e3f2fd; color: #1976d2; }
        .action-badge.success { background: #e8f5e8; color: #388e3c; }
        .action-badge.warning { background: #fff3e0; color: #f57c00; }
        .action-badge.danger { background: #ffebee; color: #d32f2f; }
        .action-badge.info { background: #e0f7fa; color: #00796b; }
        .action-badge.secondary { background: #f5f5f5; color: #616161; }
        .action-badge.light { background: #fafafa; color: #424242; }
        
        .details-content {
            max-width: 300px;
            word-wrap: break-word;
            font-size: 14px;
        }
        
        .ip-cell code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .level-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .level-badge.info { background: #e3f2fd; color: #1976d2; }
        .level-badge.warning { background: #fff3e0; color: #f57c00; }
        .level-badge.error { background: #ffebee; color: #d32f2f; }
        .level-badge.critical { background: #fce4ec; color: #c2185b; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            background: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-icon:hover {
            background: #007bff;
            color: white;
        }
        
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
        }
        
        .page-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .page-btn:hover:not(:disabled) {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .page-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .page-dots {
            padding: 8px 4px;
            color: #666;
        }
        
        .page-size {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .page-size select {
            padding: 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .log-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .detail-row {
            display: flex;
            gap: 15px;
        }
        
        .detail-row label {
            font-weight: 600;
            min-width: 120px;
            color: #333;
        }
        
        .detail-row span {
            flex: 1;
            word-wrap: break-word;
        }
        
        @media (max-width: 768px) {
            .filters-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-select,
            .filter-input {
                min-width: auto;
                width: 100%;
            }
            
            .quick-actions {
                justify-content: stretch;
            }
            
            .quick-actions .btn {
                flex: 1;
            }
            
            .logs-table {
                font-size: 12px;
            }
            
            .logs-table th,
            .logs-table td {
                padding: 8px;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</body>
</html>