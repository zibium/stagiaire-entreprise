<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Entreprise - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= UrlHelper::asset('css/auth.css') ?>" rel="stylesheet">
    <style>
        .gradient-bg-entreprise {
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
            min-height: 100vh;
        }
        .card-entreprise {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(40, 167, 69, 0.1);
        }
        .btn-entreprise {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-entreprise:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .text-entreprise {
            color: #28a745;
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
    </style>
</head>
<body class="gradient-bg-entreprise">
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Section gauche - Branding Entreprise -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div class="text-center text-white">
                    <div class="mb-5">
                        <i class="fas fa-building fa-5x mb-4 text-white"></i>
                        <h1 class="display-4 fw-bold mb-3">Espace Entreprise</h1>
                        <p class="lead fs-4">Recrutez les meilleurs talents pour vos stages</p>
                    </div>
                    
                    <div class="row g-4 mt-4">
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5 class="fw-bold">Gestion des candidatures</h5>
                                <p class="mb-0">Recevez et gérez facilement toutes vos candidatures</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                <h5 class="fw-bold">Diffusion d'offres</h5>
                                <p class="mb-0">Publiez vos offres de stage en quelques clics</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <h5 class="fw-bold">Statistiques</h5>
                                <p class="mb-0">Suivez les performances de vos offres</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card p-4 h-100">
                                <i class="fas fa-handshake fa-3x mb-3"></i>
                                <h5 class="fw-bold">Matching intelligent</h5>
                                <p class="mb-0">Trouvez les profils qui correspondent à vos besoins</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section droite - Formulaire de connexion -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center py-5">
                <div class="w-100" style="max-width: 450px;">
                    <div class="card card-entreprise shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-building fa-2x text-entreprise"></i>
                                </div>
                                <h3 class="card-title mb-1 text-entreprise fw-bold">Connexion Entreprise</h3>
                                <p class="text-muted">Accédez à votre espace recruteur</p>
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
                                <input type="hidden" name="user_type" value="entreprise">
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2 text-entreprise"></i>Adresse email professionnelle
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email" 
                                           name="email" 
                                           placeholder="contact@entreprise.com"
                                           required 
                                           autocomplete="email">
                                </div>
                                
                                <!-- Mot de passe -->
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-entreprise"></i>Mot de passe
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
                                        <a href="<?= UrlHelper::url('auth/forgot-password') ?>" class="text-decoration-none text-entreprise">
                                            <i class="fas fa-key me-1"></i>Mot de passe oublié ?
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Bouton de connexion -->
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-entreprise btn-lg text-white" id="submitBtn">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Liens -->
                            <div class="text-center">
                                <hr class="my-4">
                                <p class="mb-3">
                                    Pas encore de compte entreprise ?
                                    <a href="<?= UrlHelper::url('auth/register') ?>" class="text-decoration-none fw-bold text-entreprise">
                                        <i class="fas fa-user-plus me-1"></i>Créer un compte
                                    </a>
                                </p>
                                <p class="mb-0">
                                    Vous êtes un stagiaire ?
                                    <a href="<?= UrlHelper::url('auth/login-stagiaire') ?>" class="text-decoration-none text-primary">
                                        <i class="fas fa-user-graduate me-1"></i>Connexion stagiaire
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
        
        // Add loading animation to feature cards
        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate__animated', 'animate__fadeInUp');
        });
    </script>
</body>
</html>