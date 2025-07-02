<?php
require_once __DIR__ . '/../../utils/UrlHelper.php';
?>

<!-- Messages d'alerte -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- En-tête -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <h1 class="card-title mb-2">
                    <i class="fas fa-user-edit me-2"></i>Mon Profil
                </h1>
                <p class="card-text mb-0">
                    Complétez votre profil pour maximiser vos chances de trouver un stage
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Formulaire principal -->
    <div class="col-lg-8">
        <form method="POST" action="/stagiaire/profile" id="profileForm">
            <input type="hidden" name="csrf_token" value="<?= \JobBoard\Controllers\StagiaireController::generateCsrfToken() ?>">

            <!-- Informations personnelles -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informations personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                   value="<?= htmlspecialchars($profil['nom'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom"
                                   value="<?= htmlspecialchars($profil['prenom'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                   value="<?= htmlspecialchars($profil['date_naissance'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone"
                                   value="<?= htmlspecialchars($profil['telephone'] ?? '') ?>"
                                   placeholder="06 12 34 56 78">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adresse -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Adresse
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse"
                               value="<?= htmlspecialchars($profil['adresse'] ?? '') ?>"
                               placeholder="123 rue de la République">
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville"
                                   value="<?= htmlspecialchars($profil['ville'] ?? '') ?>"
                                   placeholder="Paris">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="code_postal" class="form-label">Code postal</label>
                            <input type="text" class="form-control" id="code_postal" name="code_postal"
                                   value="<?= htmlspecialchars($profil['code_postal'] ?? '') ?>"
                                   placeholder="75001" pattern="[0-9]{5}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Formation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="niveau_etudes" class="form-label">Niveau d'études</label>
                            <select class="form-select" id="niveau_etudes" name="niveau_etudes">
                                <option value="">Sélectionnez votre niveau</option>
                                <option value="Bac" <?= ($profil['niveau_etudes'] ?? '') === 'Bac' ? 'selected' : '' ?>>Bac</option>
                                <option value="Bac+1" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+1' ? 'selected' : '' ?>>Bac+1</option>
                                <option value="Bac+2" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+2' ? 'selected' : '' ?>>Bac+2</option>
                                <option value="Bac+3" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+3' ? 'selected' : '' ?>>Bac+3</option>
                                <option value="Bac+4" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+4' ? 'selected' : '' ?>>Bac+4</option>
                                <option value="Bac+5" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+5' ? 'selected' : '' ?>>Bac+5</option>
                                <option value="Bac+8" <?= ($profil['niveau_etudes'] ?? '') === 'Bac+8' ? 'selected' : '' ?>>Bac+8</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="domaine_etudes" class="form-label">Domaine d'études</label>
                            <input type="text" class="form-control" id="domaine_etudes" name="domaine_etudes"
                                   value="<?= htmlspecialchars($profil['domaine_etudes'] ?? '') ?>"
                                   placeholder="Informatique, Marketing, etc.">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ecole" class="form-label">École/Université</label>
                        <input type="text" class="form-control" id="ecole" name="ecole"
                               value="<?= htmlspecialchars($profil['ecole'] ?? '') ?>"
                               placeholder="Nom de votre établissement">
                    </div>
                </div>
            </div>

            <!-- Lettre de motivation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-envelope me-2"></i>Lettre de motivation par défaut
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="lettre_motivation" class="form-label">Lettre de motivation</label>
                        <textarea class="form-control" id="lettre_motivation" name="lettre_motivation" rows="6"
                                  placeholder="Rédigez une lettre de motivation générale que vous pourrez personnaliser pour chaque candidature..."><?= htmlspecialchars($profil['lettre_motivation'] ?? '') ?></textarea>
                        <div class="form-text">Cette lettre servira de base pour vos candidatures</div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="/stagiaire/dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer le profil
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Upload CV -->
        <div class="card mb-4" id="cv-section">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-pdf me-2"></i>Mon CV
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($profil['cv_path'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>CV uploadé avec succès
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Fichier actuel:</span>
                        <a href="/<?= htmlspecialchars($profil['cv_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Voir
                        </a>
                    </div>

                    <form method="POST" action="/stagiaire/delete-cv" class="mb-3">
                        <input type="hidden" name="csrf_token" value="<?= \JobBoard\Controllers\StagiaireController::generateCsrfToken() ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre CV ?')">
                            <i class="fas fa-trash me-1"></i>Supprimer le CV
                        </button>
                    </form>
                <?php endif; ?>

                <form method="POST" action="/stagiaire/upload-cv" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= \JobBoard\Controllers\StagiaireController::generateCsrfToken() ?>">

                    <div class="mb-3">
                        <label for="cv" class="form-label">
                            <?= !empty($profil['cv_path']) ? 'Remplacer le CV' : 'Uploader un CV' ?>
                        </label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                        <div class="form-text">Format PDF uniquement, taille max: 5MB</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload me-2"></i>
                        <?= !empty($profil['cv_path']) ? 'Remplacer' : 'Uploader' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Conseils -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Conseils
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Complétez tous les champs pour un profil attractif
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Uploadez un CV au format PDF
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Rédigez une lettre de motivation personnalisée
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Mettez à jour régulièrement vos informations
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Scripts spécifiques à la page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('profileForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();

            if (!nom || !prenom) {
                e.preventDefault();
                alert('Veuillez remplir au minimum votre nom et prénom.');
                return false;
            }
        });
    }

    // Auto-resize des textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});
</script>