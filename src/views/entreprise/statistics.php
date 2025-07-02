<?php
// Vérification de l'authentification entreprise
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'entreprise') {
    require_once __DIR__ . '/../../utils/UrlHelper.php';
    header('Location: ' . \UrlHelper::url('auth/login'));
    exit;
}

$pageTitle = 'Statistiques - Entreprise';
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
                <i class="fas fa-building"></i>
                <span>JobBoard Entreprise</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/entreprise/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                <li><a href="/entreprise/profile" class="nav-link"><i class="fas fa-user-circle"></i> Profil</a></li>
                <li><a href="/entreprise/offers" class="nav-link"><i class="fas fa-briefcase"></i> Offres</a></li>
                <li><a href="/entreprise/applications" class="nav-link"><i class="fas fa-file-alt"></i> Candidatures</a></li>
                <li><a href="/entreprise/statistics" class="nav-link active"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <li><a href="/auth/logout" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-bar"></i> Statistiques de votre entreprise</h1>
            <p class="subtitle">Analyse de vos offres et candidatures</p>
        </div>

        <!-- Cartes de statistiques -->
        <div class="stats-grid">
            <!-- Statistiques des offres -->
            <div class="stat-card">
                <div class="stat-icon offers">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <h3>Offres publiées</h3>
                    <div class="stat-number"><?= $stats['offers']['total'] ?? 0 ?></div>
                    <div class="stat-details">
                        <span class="active">Actives: <?= $stats['offers']['active'] ?? 0 ?></span>
                        <span class="pending">En attente: <?= $stats['offers']['pending'] ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <!-- Statistiques des candidatures -->
            <div class="stat-card">
                <div class="stat-icon applications">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Candidatures reçues</h3>
                    <div class="stat-number"><?= $stats['applications']['total'] ?? 0 ?></div>
                    <div class="stat-details">
                        <span class="pending">En attente: <?= $stats['applications']['pending'] ?? 0 ?></span>
                        <span class="accepted">Acceptées: <?= $stats['applications']['accepted'] ?? 0 ?></span>
                        <span class="rejected">Refusées: <?= $stats['applications']['rejected'] ?? 0 ?></span>
                    </div>
                </div>
            </div>

            <!-- Taux de réponse -->
            <div class="stat-card">
                <div class="stat-icon response">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3>Taux de réponse</h3>
                    <?php 
                    $total = $stats['applications']['total'] ?? 0;
                    $responded = ($stats['applications']['accepted'] ?? 0) + ($stats['applications']['rejected'] ?? 0);
                    $responseRate = $total > 0 ? round(($responded / $total) * 100, 1) : 0;
                    ?>
                    <div class="stat-number"><?= $responseRate ?>%</div>
                    <div class="stat-details">
                        <span>Candidatures traitées: <?= $responded ?>/<?= $total ?></span>
                    </div>
                </div>
            </div>

            <!-- Taux d'acceptation -->
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Taux d'acceptation</h3>
                    <?php 
                    $accepted = $stats['applications']['accepted'] ?? 0;
                    $acceptanceRate = $responded > 0 ? round(($accepted / $responded) * 100, 1) : 0;
                    ?>
                    <div class="stat-number"><?= $acceptanceRate ?>%</div>
                    <div class="stat-details">
                        <span>Sur les candidatures traitées</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-container">
            <!-- Répartition des candidatures -->
            <div class="chart-card">
                <h3><i class="fas fa-pie-chart"></i> Répartition des candidatures</h3>
                <div class="chart-wrapper">
                    <canvas id="applicationsChart"></canvas>
                </div>
            </div>

            <!-- Statut des offres -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar"></i> Statut des offres</h3>
                <div class="chart-wrapper">
                    <canvas id="offersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau récapitulatif -->
        <div class="summary-table">
            <h3><i class="fas fa-table"></i> Résumé détaillé</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Métrique</th>
                        <th>Valeur</th>
                        <th>Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-briefcase"></i> Offres totales</td>
                        <td><?= $stats['offers']['total'] ?? 0 ?></td>
                        <td>100%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-check-circle"></i> Offres actives</td>
                        <td><?= $stats['offers']['active'] ?? 0 ?></td>
                        <td><?= $stats['offers']['total'] > 0 ? round(($stats['offers']['active'] / $stats['offers']['total']) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-clock"></i> Offres en attente</td>
                        <td><?= $stats['offers']['pending'] ?? 0 ?></td>
                        <td><?= $stats['offers']['total'] > 0 ? round(($stats['offers']['pending'] / $stats['offers']['total']) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-file-alt"></i> Candidatures totales</td>
                        <td><?= $stats['applications']['total'] ?? 0 ?></td>
                        <td>100%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-hourglass-half"></i> Candidatures en attente</td>
                        <td><?= $stats['applications']['pending'] ?? 0 ?></td>
                        <td><?= $total > 0 ? round(($stats['applications']['pending'] / $total) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-thumbs-up"></i> Candidatures acceptées</td>
                        <td><?= $stats['applications']['accepted'] ?? 0 ?></td>
                        <td><?= $total > 0 ? round(($stats['applications']['accepted'] / $total) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-thumbs-down"></i> Candidatures refusées</td>
                        <td><?= $stats['applications']['rejected'] ?? 0 ?></td>
                        <td><?= $total > 0 ? round(($stats['applications']['rejected'] / $total) * 100, 1) : 0 ?>%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Graphique en camembert pour les candidatures
        const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
        new Chart(applicationsCtx, {
            type: 'doughnut',
            data: {
                labels: ['En attente', 'Acceptées', 'Refusées'],
                datasets: [{
                    data: [
                        <?= $stats['applications']['pending'] ?? 0 ?>,
                        <?= $stats['applications']['accepted'] ?? 0 ?>,
                        <?= $stats['applications']['rejected'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
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

        // Graphique en barres pour les offres
        const offersCtx = document.getElementById('offersChart').getContext('2d');
        new Chart(offersCtx, {
            type: 'bar',
            data: {
                labels: ['Actives', 'En attente'],
                datasets: [{
                    label: 'Nombre d\'offres',
                    data: [
                        <?= $stats['offers']['active'] ?? 0 ?>,
                        <?= $stats['offers']['pending'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107'
                    ],
                    borderColor: [
                        '#1e7e34',
                        '#e0a800'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>