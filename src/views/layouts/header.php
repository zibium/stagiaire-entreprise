<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?? 'Plateforme de stages - Trouvez votre stage idéal' ?>">
    <meta name="keywords" content="<?= $pageKeywords ?? 'stage, emploi, entreprise, étudiant, formation' ?>">
    <meta name="author" content="JobBoard">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $_SERVER['REQUEST_URI'] ?? '' ?>">
    <meta property="og:title" content="<?= $pageTitle ?? 'JobBoard - Plateforme de stages' ?>">
    <meta property="og:description" content="<?= $pageDescription ?? 'Trouvez votre stage idéal' ?>">
    <meta property="og:image" content="/public/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= $_SERVER['REQUEST_URI'] ?? '' ?>">
    <meta property="twitter:title" content="<?= $pageTitle ?? 'JobBoard - Plateforme de stages' ?>">
    <meta property="twitter:description" content="<?= $pageDescription ?? 'Trouvez votre stage idéal' ?>">
    <meta property="twitter:image" content="/public/images/og-image.jpg">
    
    <title><?= $pageTitle ?? 'JobBoard - Plateforme de stages' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/public/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/public/images/apple-touch-icon.png">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="/css/style.css" as="style">
    <link rel="preload" href="/js/app.js" as="script">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/css/style.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom CSS for specific pages -->
    <?php if (isset($customCSS)): ?>
        <style><?= $customCSS ?></style>
    <?php endif; ?>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="<?= $bodyClass ?? '' ?>" data-theme="<?= $_SESSION['theme'] ?? 'light' ?>">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>
    
    <!-- Loading indicator -->
    <div id="loading-indicator" class="loading-indicator" aria-hidden="true">
        <div class="loading-spinner"></div>
        <span class="sr-only">Chargement en cours...</span>
    </div>
    
    <!-- Header -->
    <header class="header" role="banner">
        <?php include __DIR__ . '/navbar.php'; ?>
        <div class="dashboard-container">
    </header>
    
    <!-- Main content -->
    <main id="main-content" class="main-content" role="main">
        <!-- Breadcrumb -->
        <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
            <nav class="breadcrumb-nav" aria-label="Fil d'Ariane">
                <div class="container">
                    <ol class="breadcrumb">
                        <?php foreach ($breadcrumb as $index => $item): ?>
                            <li class="breadcrumb-item <?= $index === count($breadcrumb) - 1 ? 'active' : '' ?>">
                                <?php if ($index === count($breadcrumb) - 1): ?>
                                    <span aria-current="page"><?= htmlspecialchars($item['title']) ?></span>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['title']) ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </nav>
        <?php endif; ?>
        
        <!-- Flash messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
                <button type="button" class="alert-close" aria-label="Fermer">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                <button type="button" class="alert-close" aria-label="Fermer">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <span><?= htmlspecialchars($_SESSION['warning']) ?></span>
                <button type="button" class="alert-close" aria-label="Fermer">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info alert-dismissible" role="alert">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                <span><?= htmlspecialchars($_SESSION['info']) ?></span>
                <button type="button" class="alert-close" aria-label="Fermer">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>
        
        <!-- Page content -->