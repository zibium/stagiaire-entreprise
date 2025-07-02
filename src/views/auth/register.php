<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - JobBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= UrlHelper::asset('css/auth.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Section gauche - Image/Branding -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-success">
                <div class="text-center text-white">
                    <i class="fas fa-user-plus fa-5x mb-4"></i>
                    <h2 class="mb-3">Rejoignez JobBoard</h2>
                    <p class="lead">Créez votre compte et accédez aux meilleures opportunités de stage</p>
                    <div class="mt-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card bg-transparent border-light text-white">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                        <h6>Stagiaires</h6>
                                        <small>Trouvez le stage de vos rêves</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-transparent border-light text-white">
                                    <div class="card-body text-center py-3">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <h6>Entreprises</h6>
                                        <small>Recrutez les meilleurs talents</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section droite - Formulaire -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center py-4">
                <div class="w-100" style="max-width: 450px;">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h3 class="card-title mb-1">Inscription</h3>
                                <p class="text-muted">Créez votre compte gratuitement</p>
                            </div>
                            
                            <!-- Messages d'erreur/succès -->
                            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <ul class="mb-0">
                                        <?php foreach ($_SESSION['errors'] as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php unset($_SESSION['errors']); ?>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?= htmlspecialchars($_SESSION['error']) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?= UrlHelper::url('auth/register') ?>" id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?= $authController->generateCsrfToken() ?>">
                                
                                <!-- Type de compte -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-users me-1"></i>Type de compte
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="role" id="role_stagiaire" value="stagiaire" 
                                                   <?= (($_SESSION['old_input']['role'] ?? '') === 'stagiaire') ? 'checked' : '' ?> required>
                                            <label class="btn btn-outline-primary w-100 py-3" for="role_stagiaire">
                                                <i class="fas fa-user-graduate d-block mb-1"></i>
                                                <small>Stagiaire</small>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="role" id="role_entreprise" value="entreprise"
                                                   <?= (($_SESSION['old_input']['role'] ?? '') === 'entreprise') ? 'checked' : '' ?> required>
                                            <label class="btn btn-outline-success w-100 py-3" for="role_entreprise">
                                                <i class="fas fa-building d-block mb-1"></i>
                                                <small>Entreprise</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Adresse email
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-lg" 
                                           id="email" 
                                           name="email" 
                                           placeholder="votre@email.com"
                                           value="<?= htmlspecialchars($_SESSION['old_input']['email'] ?? '') ?>"
                                           required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Utilisez une adresse email valide
                                    </div>
                                </div>
                                
                                <!-- Mot de passe -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Mot de passe
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Minimum 8 caractères"
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-shield-alt me-1"></i>Au moins 8 caractères
                                    </div>
                                </div>
                                
                                <!-- Confirmation mot de passe -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Confirmer le mot de passe
                                    </label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control form-control-lg" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               placeholder="Répétez votre mot de passe"
                                               required>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordMatch" class="form-text"></div>
                                </div>
                                
                                <!-- Conditions d'utilisation -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                                        <label class="form-check-label" for="accept_terms">
                                            J'accepte les 
                                            <a href="<?= UrlHelper::url('terms') ?>" target="_blank" class="text-decoration-none">conditions d'utilisation</a>
                                            et la 
                                            <a href="<?= UrlHelper::url('privacy') ?>" target="_blank" class="text-decoration-none">politique de confidentialité</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                        <i class="fas fa-user-plus me-2"></i>Créer mon compte
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center">
                                <hr class="my-3">
                                <p class="mb-0">
                                    Déjà un compte ?
                                    <a href="<?= UrlHelper::url('auth/login') ?>" class="text-decoration-none fw-bold">
                                        <i class="fas fa-sign-in-alt me-1"></i>Se connecter
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Retour à l'accueil -->
                    <div class="text-center mt-3">
                        <a href="<?= UrlHelper::url('') ?>" class="text-muted text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function setupPasswordToggle(passwordId, toggleId) {
            document.getElementById(toggleId).addEventListener('click', function() {
                const password = document.getElementById(passwordId);
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
        }
        
        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('confirm_password', 'toggleConfirmPassword');
        
        // Password confirmation validation
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<i class="fas fa-check text-success me-1"></i><span class="text-success">Les mots de passe correspondent</span>';
            } else {
                matchDiv.innerHTML = '<i class="fas fa-times text-danger me-1"></i><span class="text-danger">Les mots de passe ne correspondent pas</span>';
            }
        }
        
        document.getElementById('password').addEventListener('input', checkPasswordMatch);
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // You can add a strength indicator here if needed
        });
        
        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }
        
        // Clear old input session data
        <?php unset($_SESSION['old_input']); ?>
        
        // Form validation
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', function(e) {
            // Récupérer les valeurs du formulaire
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const role = document.querySelector('input[name="role"]:checked');
            const acceptTerms = document.getElementById('accept_terms').checked;
            
            // Validation côté client
            const errors = [];
            
            if (!email || !isValidEmail(email)) {
                errors.push('Email invalide');
            }
            
            if (!password || password.length < 8) {
                errors.push('Le mot de passe doit contenir au moins 8 caractères');
            }
            
            if (password !== confirmPassword) {
                errors.push('Les mots de passe ne correspondent pas');
            }
            
            if (!role) {
                errors.push('Veuillez sélectionner un rôle');
            }
            
            if (!acceptTerms) {
                errors.push('Vous devez accepter les conditions d\'utilisation');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Erreurs de validation:\n' + errors.join('\n'));
                return;
            }
            
            // Désactiver le bouton pour éviter les doubles soumissions
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Création en cours...';
            
            // Le formulaire se soumettra naturellement
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Role selection animation
        document.querySelectorAll('input[name="role"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label[for^="role_"]').forEach(function(label) {
                    label.classList.remove('shadow-sm');
                });
                if (this.checked) {
                    document.querySelector('label[for="' + this.id + '"]').classList.add('shadow-sm');
                }
            });
        });
    </script>
</body>
</html>