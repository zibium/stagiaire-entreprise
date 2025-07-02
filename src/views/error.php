<?php
// Page d'erreur générique
?>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title text-danger mb-3">Oops ! Une erreur s'est produite</h2>
                    <p class="card-text text-muted mb-4">
                        <?= isset($message) ? htmlspecialchars($message) : 'Une erreur inattendue s\'est produite. Veuillez réessayer plus tard.' ?>
                    </p>
                    <div class="d-grid gap-2">
                        <a href="javascript:history.back()" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <a href="/" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.card {
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

.btn {
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>