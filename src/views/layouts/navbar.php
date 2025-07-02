<?php
// Navigation principale responsive
$currentRole = $_SESSION['user_role'] ?? 'guest';
?>
<nav class="navbar" role="navigation" aria-label="Navigation principale">
    <div class="container">
        <!-- Logo -->
        <div class="navbar-brand">
            <a href="/" class="logo" aria-label="Retour à l'accueil">
                <img src="/public/images/logo.svg" alt="JobBoard" width="120" height="40">
            </a>
        </div>

        <!-- Menu selon le rôle -->
        <div class="navbar-menu" id="navbar-menu">
            <ul class="navbar-nav">
                <?php if ($currentRole === 'stagiaire'): ?>
                    <li class="nav-item"><a href="/stagiaire/dashboard" class="nav-link">Tableau de bord</a></li>
                    <li class="nav-item"><a href="/stagiaire/offres" class="nav-link">Offres</a></li>
                    <li class="nav-item"><a href="/stagiaire/candidatures" class="nav-link">Candidatures</a></li>
                <?php elseif ($currentRole === 'entreprise'): ?>
                    <li class="nav-item"><a href="/entreprise/dashboard" class="nav-link">Tableau de bord</a></li>
                    <li class="nav-item"><a href="/entreprise/offres" class="nav-link">Mes offres</a></li>
                <?php endif; ?>
            </ul>

            <!-- Menu utilisateur -->
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button">
                            <?= htmlspecialchars($_SESSION['user_email']) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="/profil" class="dropdown-item">Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/auth/logout" method="POST">
                                    <button type="submit" class="dropdown-item">Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= UrlHelper::url('auth/login') ?>" class="btn btn-primary">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>