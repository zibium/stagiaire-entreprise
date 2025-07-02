<?php
// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Statistiques - Administration';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li><a href="/admin/statistics" class="nav-link active"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <li><a href="/admin/logs" class="nav-link"><i class="fas fa-list-alt"></i> Logs</a></li>
                <li><a href="/auth/logout" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-bar"></i> Statistiques de la plateforme</h1>
            <p class="subtitle">Analyse des performances et tendances</p>
        </div>

        <!-- Période de sélection -->
        <div class="period-selector">
            <div class="period-buttons">
                <button class="period-btn active" data-period="7">7 jours</button>
                <button class="period-btn" data-period="30">30 jours</button>
                <button class="period-btn" data-period="90">3 mois</button>
                <button class="period-btn" data-period="365">1 an</button>
            </div>
            <div class="export-actions">
                <button class="btn btn-outline" onclick="exportReport()">
                    <i class="fas fa-download"></i> Exporter rapport
                </button>
                <button class="btn btn-primary" onclick="generatePDF()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-overview">
            <div class="stats-grid">
                <div class="stat-card users">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($globalStats['total_users'] ?? 0) ?></h3>
                        <p>Utilisateurs inscrits</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card offers">
                    <div class="stat-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($globalStats['total_offers'] ?? 0) ?></h3>
                        <p>Offres publiées</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+8% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card applications">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($globalStats['total_applications'] ?? 0) ?></h3>
                        <p>Candidatures soumises</p>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+15% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $globalStats['success_rate'] ?? 0 ?>%</h3>
                        <p>Taux de réussite</p>
                        <div class="stat-trend neutral">
                            <i class="fas fa-minus"></i>
                            <span>Stable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques principaux -->
        <div class="charts-section">
            <div class="charts-grid">
                <!-- Évolution des inscriptions -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-line"></i> Évolution des inscriptions</h3>
                        <div class="chart-controls">
                            <select id="userChartType">
                                <option value="total">Total</option>
                                <option value="companies">Entreprises</option>
                                <option value="interns">Stagiaires</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>

                <!-- Distribution des offres -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Répartition par domaine</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="domainsChart"></canvas>
                    </div>
                </div>

                <!-- Tendances des candidatures -->
                <div class="chart-container full-width">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-area"></i> Tendances des candidatures</h3>
                        <div class="chart-legend">
                            <span class="legend-item"><span class="legend-color applications"></span> Candidatures</span>
                            <span class="legend-item"><span class="legend-color accepted"></span> Acceptées</span>
                            <span class="legend-item"><span class="legend-color rejected"></span> Rejetées</span>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="applicationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableaux de données -->
        <div class="data-tables">
            <div class="tables-grid">
                <!-- Top domaines -->
                <div class="table-section">
                    <div class="section-header">
                        <h3><i class="fas fa-trophy"></i> Top domaines</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table compact">
                            <thead>
                                <tr>
                                    <th>Domaine</th>
                                    <th>Offres</th>
                                    <th>Candidatures</th>
                                    <th>Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $topDomains = $globalStats['top_domains'] ?? [
                                    ['domaine' => 'Informatique', 'offres' => 45, 'candidatures' => 234, 'taux' => 5.2],
                                    ['domaine' => 'Marketing', 'offres' => 32, 'candidatures' => 156, 'taux' => 4.9],
                                    ['domaine' => 'Finance', 'offres' => 28, 'candidatures' => 98, 'taux' => 3.5],
                                    ['domaine' => 'RH', 'offres' => 21, 'candidatures' => 87, 'taux' => 4.1],
                                    ['domaine' => 'Commercial', 'offres' => 19, 'candidatures' => 76, 'taux' => 4.0]
                                ];
                                foreach ($topDomains as $domain): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($domain['domaine']) ?></td>
                                        <td><?= $domain['offres'] ?></td>
                                        <td><?= $domain['candidatures'] ?></td>
                                        <td><?= $domain['taux'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Entreprises actives -->
                <div class="table-section">
                    <div class="section-header">
                        <h3><i class="fas fa-building"></i> Entreprises les plus actives</h3>
                    </div>
                    <div class="table-container">
                        <table class="data-table compact">
                            <thead>
                                <tr>
                                    <th>Entreprise</th>
                                    <th>Offres</th>
                                    <th>Candidatures</th>
                                    <th>Embauches</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TechCorp</td>
                                    <td>12</td>
                                    <td>89</td>
                                    <td>8</td>
                                </tr>
                                <tr>
                                    <td>InnovateLab</td>
                                    <td>9</td>
                                    <td>67</td>
                                    <td>6</td>
                                </tr>
                                <tr>
                                    <td>DigitalSoft</td>
                                    <td>8</td>
                                    <td>54</td>
                                    <td>5</td>
                                </tr>
                                <tr>
                                    <td>StartupXYZ</td>
                                    <td>7</td>
                                    <td>43</td>
                                    <td>4</td>
                                </tr>
                                <tr>
                                    <td>WebAgency</td>
                                    <td>6</td>
                                    <td>38</td>
                                    <td>3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métriques avancées -->
        <div class="advanced-metrics">
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <h4><i class="fas fa-clock"></i> Temps moyen de recrutement</h4>
                    </div>
                    <div class="metric-value">
                        <span class="value">18</span>
                        <span class="unit">jours</span>
                    </div>
                    <div class="metric-trend positive">
                        <i class="fas fa-arrow-down"></i>
                        <span>-2 jours vs mois dernier</span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <h4><i class="fas fa-percentage"></i> Taux de conversion</h4>
                    </div>
                    <div class="metric-value">
                        <span class="value">12.5</span>
                        <span class="unit">%</span>
                    </div>
                    <div class="metric-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+1.2% vs mois dernier</span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <h4><i class="fas fa-star"></i> Satisfaction moyenne</h4>
                    </div>
                    <div class="metric-value">
                        <span class="value">4.2</span>
                        <span class="unit">/5</span>
                    </div>
                    <div class="metric-trend neutral">
                        <i class="fas fa-minus"></i>
                        <span>Stable</span>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <h4><i class="fas fa-redo"></i> Taux de retour</h4>
                    </div>
                    <div class="metric-value">
                        <span class="value">68</span>
                        <span class="unit">%</span>
                    </div>
                    <div class="metric-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+5% vs mois dernier</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration des graphiques
        const chartColors = {
            primary: '#007bff',
            success: '#28a745',
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8',
            light: '#f8f9fa',
            dark: '#343a40'
        };

        // Graphique évolution des utilisateurs
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        const usersChart = new Chart(usersCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: [12, 19, 25, 32, 28, 35, 42, 38, 45, 52, 48, 55],
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primary + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique répartition par domaine
        const domainsCtx = document.getElementById('domainsChart').getContext('2d');
        const domainsChart = new Chart(domainsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Informatique', 'Marketing', 'Finance', 'RH', 'Commercial', 'Autres'],
                datasets: [{
                    data: [35, 25, 18, 12, 8, 2],
                    backgroundColor: [
                        chartColors.primary,
                        chartColors.success,
                        chartColors.warning,
                        chartColors.info,
                        chartColors.danger,
                        chartColors.light
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique tendances des candidatures
        const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
        const applicationsChart = new Chart(applicationsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Candidatures',
                        data: [45, 52, 48, 61, 55, 67, 73, 69, 78, 85, 82, 89],
                        borderColor: chartColors.primary,
                        backgroundColor: chartColors.primary + '20',
                        tension: 0.4
                    },
                    {
                        label: 'Acceptées',
                        data: [8, 12, 9, 15, 11, 18, 22, 19, 25, 28, 26, 31],
                        borderColor: chartColors.success,
                        backgroundColor: chartColors.success + '20',
                        tension: 0.4
                    },
                    {
                        label: 'Rejetées',
                        data: [15, 18, 16, 22, 19, 25, 28, 24, 31, 35, 33, 38],
                        borderColor: chartColors.danger,
                        backgroundColor: chartColors.danger + '20',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gestion des périodes
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const period = this.dataset.period;
                updateCharts(period);
            });
        });

        // Gestion du type de graphique utilisateurs
        document.getElementById('userChartType').addEventListener('change', function() {
            updateUsersChart(this.value);
        });

        function updateCharts(period) {
            // Simuler la mise à jour des données selon la période
            console.log('Mise à jour des graphiques pour la période:', period + ' jours');
        }

        function updateUsersChart(type) {
            // Simuler la mise à jour du graphique utilisateurs selon le type
            console.log('Mise à jour du graphique utilisateurs pour le type:', type);
        }

        function exportReport() {
            const period = document.querySelector('.period-btn.active').dataset.period;
            window.location.href = `/admin/export?type=statistics&period=${period}&format=csv`;
        }

        function generatePDF() {
            const period = document.querySelector('.period-btn.active').dataset.period;
            window.open(`/admin/reports/statistics?period=${period}&format=pdf`, '_blank');
        }

        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .chart-container, .metric-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <style>
        .period-selector {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .period-buttons {
            display: flex;
            gap: 10px;
        }

        .period-btn {
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .period-btn.active,
        .period-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .export-actions {
            display: flex;
            gap: 10px;
        }

        .stats-overview {
            margin-bottom: 30px;
        }

        .charts-section {
            margin-bottom: 30px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .chart-container.full-width {
            grid-column: 1 / -1;
        }

        .chart-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .chart-controls select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .chart-legend {
            display: flex;
            gap: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .legend-color.applications { background: #007bff; }
        .legend-color.accepted { background: #28a745; }
        .legend-color.rejected { background: #dc3545; }

        .chart-body {
            padding: 20px;
            height: 300px;
        }

        .data-tables {
            margin-bottom: 30px;
        }

        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .table-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .data-table.compact {
            font-size: 14px;
        }

        .data-table.compact th,
        .data-table.compact td {
            padding: 8px 12px;
        }

        .advanced-metrics {
            margin-bottom: 30px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .metric-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .metric-header h4 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .metric-value {
            margin-bottom: 10px;
        }

        .metric-value .value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .metric-value .unit {
            font-size: 16px;
            color: #666;
            margin-left: 5px;
        }

        .metric-trend,
        .stat-trend {
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .metric-trend.positive,
        .stat-trend.positive {
            color: #28a745;
        }

        .metric-trend.negative,
        .stat-trend.negative {
            color: #dc3545;
        }

        .metric-trend.neutral,
        .stat-trend.neutral {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .charts-grid,
            .tables-grid {
                grid-template-columns: 1fr;
            }
            
            .period-selector {
                flex-direction: column;
                gap: 15px;
            }
            
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>