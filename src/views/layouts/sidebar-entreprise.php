<?php
// Sidebar pour les pages entreprise
$currentPage = $currentPage ?? '';
$currentRole = $_SESSION['user_role'] ?? 'guest';

// Vérifier que l'utilisateur est bien une entreprise
if ($currentRole !== 'entreprise') {
    return;
}

// Menu items pour entreprise
$menuItems = [
    [
        'url' => \UrlHelper::url('entreprise/dashboard'),
        'icon' => 'fas fa-tachometer-alt',
        'label' => 'Tableau de bord',
        'page' => 'dashboard'
    ],
    [
        'url' => \UrlHelper::url('entreprise/profile'),
        'icon' => 'fas fa-building',
        'label' => 'Mon profil',
        'page' => 'profile'
    ],
    [
        'url' => \UrlHelper::url('entreprise/offers'),
        'icon' => 'fas fa-briefcase',
        'label' => 'Mes offres',
        'page' => 'offers'
    ],
    [
        'url' => \UrlHelper::url('entreprise/offers/create'),
        'icon' => 'fas fa-plus-circle',
        'label' => 'Créer une offre',
        'page' => 'create-offer'
    ],
    [
        'url' => \UrlHelper::url('entreprise/applications'),
        'icon' => 'fas fa-users',
        'label' => 'Candidatures',
        'page' => 'applications'
    ],
    [
        'url' => \UrlHelper::url('entreprise/statistics'),
        'icon' => 'fas fa-chart-bar',
        'label' => 'Statistiques',
        'page' => 'statistics'
    ]
];
?>

<!-- Bouton toggle pour mobile -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Ouvrir/Fermer le menu">
    <i class="fas fa-bars"></i>
</button>

<!-- Overlay pour mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu principal entreprise">
    <!-- Header sidebar -->
    <div class="sidebar-header">
        <a href="<?= \UrlHelper::url('entreprise/dashboard') ?>" class="sidebar-logo">
            <i class="fas fa-building me-2"></i>
            JobBoard
        </a>
        <p class="sidebar-subtitle">Espace Entreprise</p>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a href="<?= htmlspecialchars($item['url']) ?>" 
                       class="<?= $currentPage === $item['page'] ? 'active' : '' ?>"
                       <?= $currentPage === $item['page'] ? 'aria-current="page"' : '' ?>>
                        <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Section utilisateur -->
    <div class="sidebar-user">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <p class="user-name"><?= htmlspecialchars($_SESSION['user_email'] ?? 'Utilisateur') ?></p>
                <p class="user-role">Entreprise</p>
            </div>
        </div>
        
        <div class="sidebar-actions">
            <a href="<?= \UrlHelper::url('entreprise/settings') ?>" class="sidebar-action" title="Paramètres">
                <i class="fas fa-cog"></i>
            </a>
            <form action="<?= \UrlHelper::url('auth/logout') ?>" method="POST" class="logout-form">
                <button type="submit" class="sidebar-action logout-btn" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
/* Styles spécifiques pour la sidebar entreprise */
.sidebar-subtitle {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
    margin: 0.5rem 0 0 0;
    text-align: center;
}

.sidebar-user {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.1);
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.user-avatar {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-right: 0.75rem;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.sidebar-actions {
    display: flex;
    gap: 0.5rem;
}

.sidebar-action {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.sidebar-action:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
}

.logout-form {
    flex: 1;
    margin: 0;
}

.logout-btn {
    width: 100%;
    font-family: inherit;
    font-size: inherit;
}

/* Ajustement pour le contenu avec sidebar */
.sidebar ~ .main-wrapper {
    margin-left: 280px;
}

@media (max-width: 1024px) {
    .sidebar ~ .main-wrapper {
        margin-left: 0;
    }
}
</style>

<script>
// Script pour la sidebar responsive
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
        
        // Fermer la sidebar sur les liens (mobile)
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                }
            });
        });
    }
});
</script>