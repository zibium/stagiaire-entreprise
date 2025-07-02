<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= UrlHelper::asset('css/auth.css') ?>" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .selection-card {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        .selection-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .selection-card.entreprise:hover {
            box-shadow: 0 20px 40px rgba(40, 167, 69, 0.2);
        }
        .selection-card.stagiaire:hover {
            box-shadow: 0 20px 40px rgba(0, 123, 255, 0.2);
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .btn-entreprise {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
        }
        .btn-stagiaire {
            background: linear-gradient(135deg, #007bff, #6f42c1);
            border: none;
            color: white;
        }
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        .shape {
            position: absolute;
            opacity: 0.1;
        }
        .shape-1 {
            top: 10%;
            left: 10%;
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        .shape-2 {
            top: 60%;
            right: 15%;
            width: 60px;
            height: 60px;
            background: white;
            transform: rotate(45deg);
            animation: float 8s ease-in-out infinite reverse;
        }
        .shape-3 {
            bottom: 20%;
            left: 20%;
            width: 40px;
            height: 40px;
            background: white;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            animation: float 7s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="container-fluid vh-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold text-white mb-3">
                        <i class="fas fa-briefcase me-3"></i>JobBoard
                    </h1>
                    <p class="lead text-white-50 fs-4">Choisissez votre type de connexion</p>
                </div>
                
                <div class="row g-4 justify-content-center">
                    <!-- Connexion Entreprise -->
                    <div class="col-md-6 col-lg-5">
                        <div class="card selection-card entreprise shadow-lg h-100" onclick="window.location.href='<?= UrlHelper::url('auth/login-entreprise') ?>'">
                            <div class="card-body p-5 text-center">
                                <div class="icon-circle bg-success bg-opacity-10">
                                    <i class="fas fa-building fa-3x text-success"></i>
                                </div>
                                <h3 class="fw-bold mb-3 text-success">Entreprise</h3>
                                <p class="text-muted mb-4">Accédez à votre espace recruteur pour gérer vos offres de stage et candidatures</p>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users text-success me-2"></i>
                                            <small>Gestion candidatures</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-bullhorn text-success me-2"></i>
                                            <small>Publication d'offres</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chart-line text-success me-2"></i>
                                            <small>Statistiques</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-handshake text-success me-2"></i>
                                            <small>Matching</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn btn-entreprise btn-lg px-4 py-2 rounded-pill">
                                    <i class="fas fa-sign-in-alt me-2"></i>Connexion Entreprise
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Connexion Stagiaire -->
                    <div class="col-md-6 col-lg-5">
                        <div class="card selection-card stagiaire shadow-lg h-100" onclick="window.location.href='<?= UrlHelper::url('auth/login-stagiaire') ?>'">
                            <div class="card-body p-5 text-center">
                                <div class="icon-circle bg-primary bg-opacity-10">
                                    <i class="fas fa-user-graduate fa-3x text-primary"></i>
                                </div>
                                <h3 class="fw-bold mb-3 text-primary">Stagiaire</h3>
                                <p class="text-muted mb-4">Accédez à votre espace personnel pour rechercher et postuler aux offres de stage</p>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-search text-primary me-2"></i>
                                            <small>Recherche avancée</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <small>Candidature facile</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-bell text-primary me-2"></i>
                                            <small>Alertes</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chart-bar text-primary me-2"></i>
                                            <small>Suivi candidatures</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn btn-stagiaire btn-lg px-4 py-2 rounded-pill">
                                    <i class="fas fa-sign-in-alt me-2"></i>Connexion Stagiaire
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Liens supplémentaires -->
                <div class="text-center mt-5">
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <a href="<?= UrlHelper::url('auth/register') ?>" class="text-white text-decoration-none me-4">
                                <i class="fas fa-user-plus me-1"></i>Créer un compte
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="<?= UrlHelper::url('auth/forgot-password') ?>" class="text-white text-decoration-none me-4">
                                <i class="fas fa-key me-1"></i>Mot de passe oublié
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="<?= UrlHelper::url('') ?>" class="text-white text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>