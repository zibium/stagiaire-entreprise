<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Stagiaire - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= UrlHelper::asset('css/auth.css') ?>" rel="stylesheet">
    <style>
        .gradient-bg-stagiaire {
            background: linear-gradient(135deg, #007bff 0%, #6f42c1 50%, #e83e8c 100%);
            min-height: 100vh;
        }
        .card-stagiaire {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 123, 255, 0.1);
        }
        .btn-stagiaire {
            background: linear-gradient(135deg, #007bff, #6f42c1);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-stagiaire:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .text-stagiaire {
            color: #007bff;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="gradient-bg-stagiaire">
    <div class="floating-elements">
        <i class="fas fa-graduation-cap floating-icon" style="top: 10%; left: 10%; font-size: 3rem; animation-delay: 0s;"></i>
        <i class="fas fa-laptop-code floating-icon" style="top: 20%; right: 15%; font-size: 2rem; animation-delay: 1s;"></i>
        <i class="fas fa-lightbulb floating-icon" style="bottom: 30%; left: 20%; font-size: 2.5rem; animation-delay: 2s;"></i>
        <i class="fas fa-rocket floating-icon" style="bottom: 20%; right: 10%; font-size: 2rem; animation-delay: 3s;"></i>
    </div>
    
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Section gauche - Branding Stagiaire -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div class="text-center text-white">
                    <div class="mb-5">
                        <i class="fas fa-user-graduate fa-5x mb-4 text-white"></i>
                        <h1 class="display-4 fw-bold mb-3">Espace Stagiaire</h1>
                        <p class="lead fs-4">Trouvez le stage de vos rêves et lancez votre carrière</p>
                    </div>
                    
                    <div class="row g-4 mt-4">
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <h5 class="fw-bold">Recherche avancée</h5>
                                <p class="mb-0">Filtrez les offres selon vos critères et préférences</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <h5 class="fw-bold">Candidature facile</h5>
                                <p class="mb-0">Postulez en un clic avec votre profil optimisé</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-bell fa-3x mb-3"></i>
                                <h5 class="fw-bold">Alertes personnalisées</h5>
                                <p class="mb-0">Recevez les nouvelles offres qui vous correspondent</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <h5 class="fw-bold">Suivi des candidatures</h5>
                                <p class="mb-0">Gérez et suivez toutes vos candidatures</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <div class="row text-center">
                            <div class="col-4">
                                <h3 class="fw-bold">500+</h3>
                                <p class="mb-0">Offres de stage</p>
                            </div>
                            <div class="col-4">
                                <h3 class="fw-bold">200+</h3>
                                <p class="mb-0">Entreprises partenaires</p>
                            </div>
                            <div class="col-4">
                                <h3 class="fw-bold">95%</h3>
                                <p class="mb-0">Taux de satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section droite - Formulaire de connexion -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center py-5">
                <div class="w-100" style="max-width: 450px;">
                    <div class="card card-stagiaire shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-graduate fa-2x text-stagiaire"></i>
                                </div>
                                <h3 class="card-title mb-1 text-stagiaire fw-bold">Connexion Stagiaire</h3>
                                <p class="text-muted">Accédez à votre espace personnel</p>
                            </div>
                            
                            <!-- Messages d'erreur/succès -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?= htmlspecialchars($_SESSION['error']) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?= htmlspecialchars($_SESSION['success']) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?= UrlHelper::url('auth/login') ?>" id="loginForm">
                                <input type="hidden" name="csrf_token" value="<?= $authController->generateCsrfToken() ?>">
                                <input type="hidden" name="user_type" value="stagiaire">
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2 text-stagiaire"></i>Adresse email
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email" 
                                           name="email" 
                                           placeholder="votre@email.com"
                                           required 
                                           autocomplete="email">
                                </div>
                                
                                <!-- Mot de passe -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-stagiaire"></i>Mot de passe
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Votre mot de passe"
                                               required 
                                               autocomplete="current-password">
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="togglePassword"
                                                title="Afficher/Masquer le mot de passe">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Options -->
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Se souvenir de moi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <a href="<?= UrlHelper::url('auth/forgot-password') ?>" class="text-decoration-none text-stagiaire">
                                            <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Bouton de connexion -->
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-stagiaire btn-lg text-white" id="submitBtn">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Liens -->
                            <div class="text-center">
                                <hr class="my-4">
                                <p class="mb-3">
                                    Pas encore de compte ?
                                    <a href="<?= UrlHelper::url('auth/register') ?>" class="text-decoration-none fw-bold text-stagiaire">
                                        <i class="fas fa-user-plus me-1"></i>Créer un compte
                                    </a>
                                </p>
                                <p class="mb-0">
                                    Vous êtes une entreprise ?
                                    <a href="<?= UrlHelper::url('auth/login-entreprise') ?>" class="text-decoration-none text-success">
                                        <i class="fas fa-building me-1"></i>Connexion entreprise
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Retour à l'accueil -->
                    <div class="text-center mt-4">
                        <a href="<?= UrlHelper::url('') ?>" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation and submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs.');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Veuillez saisir une adresse email valide.');
                return;
            }
            
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connexion en cours...';
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Auto-focus on email field
        document.getElementById('email').focus();
        
        // Add staggered animation to feature cards
        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 150);
        });
        
        // Animate floating icons
        document.querySelectorAll('.floating-icon').forEach((icon, index) => {
            icon.style.animationDelay = `${index * 0.5}s`;
        });
    </script>
</body>
</html>