<?php
session_start();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Session</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .session-info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Debug des Variables de Session</h1>
    
    <div class="session-info">
        <h2>Variables de Session Actuelles:</h2>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    
    <div class="session-info">
        <h2>Tests d'Authentification:</h2>
        
        <p><strong>Session ID:</strong> <?= session_id() ?></p>
        
        <p><strong>logged_in:</strong> 
            <?php if (isset($_SESSION['logged_in'])): ?>
                <span class="<?= $_SESSION['logged_in'] ? 'success' : 'error' ?>">
                    <?= $_SESSION['logged_in'] ? 'true' : 'false' ?>
                </span>
            <?php else: ?>
                <span class="error">Non défini</span>
            <?php endif; ?>
        </p>
        
        <p><strong>user_id:</strong> 
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="success"><?= $_SESSION['user_id'] ?></span>
            <?php else: ?>
                <span class="error">Non défini</span>
            <?php endif; ?>
        </p>
        
        <p><strong>user_role:</strong> 
            <?php if (isset($_SESSION['user_role'])): ?>
                <span class="success"><?= $_SESSION['user_role'] ?></span>
            <?php else: ?>
                <span class="error">Non défini</span>
            <?php endif; ?>
        </p>
        
        <p><strong>user_email:</strong> 
            <?php if (isset($_SESSION['user_email'])): ?>
                <span class="success"><?= $_SESSION['user_email'] ?></span>
            <?php else: ?>
                <span class="error">Non défini</span>
            <?php endif; ?>
        </p>
        
        <p><strong>login_time:</strong> 
            <?php if (isset($_SESSION['login_time'])): ?>
                <span class="success"><?= date('Y-m-d H:i:s', $_SESSION['login_time']) ?></span>
            <?php else: ?>
                <span class="error">Non défini</span>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="session-info">
        <h2>Test du Middleware:</h2>
        
        <?php
        // Simuler les vérifications du middleware
        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']);
        $userRole = $_SESSION['user_role'] ?? '';
        $isEntreprise = $userRole === 'entreprise';
        
        // Vérifier l'expiration de session
        $maxLifetime = 3600; // 1 heure
        $isExpired = false;
        if (isset($_SESSION['login_time'])) {
            $isExpired = (time() - $_SESSION['login_time']) > $maxLifetime;
        } else {
            $isExpired = true;
        }
        ?>
        
        <p><strong>Est connecté:</strong> 
            <span class="<?= $isLoggedIn ? 'success' : 'error' ?>">
                <?= $isLoggedIn ? 'OUI' : 'NON' ?>
            </span>
        </p>
        
        <p><strong>Est entreprise:</strong> 
            <span class="<?= $isEntreprise ? 'success' : 'error' ?>">
                <?= $isEntreprise ? 'OUI' : 'NON' ?>
            </span>
        </p>
        
        <p><strong>Session expirée:</strong> 
            <span class="<?= $isExpired ? 'error' : 'success' ?>">
                <?= $isExpired ? 'OUI' : 'NON' ?>
            </span>
            <?php if (isset($_SESSION['login_time'])): ?>
                (connecté depuis <?= round((time() - $_SESSION['login_time']) / 60) ?> minutes)
            <?php endif; ?>
        </p>
        
        <p><strong>Peut accéder aux statistiques:</strong> 
            <span class="<?= ($isLoggedIn && $isEntreprise && !$isExpired) ? 'success' : 'error' ?>">
                <?= ($isLoggedIn && $isEntreprise && !$isExpired) ? 'OUI' : 'NON' ?>
            </span>
        </p>
    </div>
    
    <div class="session-info">
        <h2>Actions:</h2>
        <p><a href="/entreprise/dashboard">Aller au Dashboard Entreprise</a></p>
        <p><a href="/entreprise/statistics">Aller aux Statistiques</a></p>
        <p><a href="/auth/login">Page de Connexion</a></p>
        <p><a href="/auth/logout">Se Déconnecter</a></p>
    </div>
</body>
</html>