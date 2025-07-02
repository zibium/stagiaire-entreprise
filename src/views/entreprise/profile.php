<?php
// Configuration de la page
$pageTitle = 'Mon Profil Entreprise - JobBoard';
$pageDescription = 'Gérez et mettez à jour les informations de votre entreprise';
$additionalCSS = ['/css/entreprise.css'];
$currentPage = 'profile';

// Inclure le header
include __DIR__ . '/../layouts/header.php';

// Inclure la sidebar entreprise
include __DIR__ . '/../layouts/sidebar-entreprise.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-building me-2"></i>
                        Mon Profil Entreprise
                    </h1>
                    <p class="text-muted mb-0">Gérez et mettez à jour les informations de votre entreprise</p>
                </div>
                <a href="/entreprise/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour au dashboard
                </a>
            </div>
        </div>

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

        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Informations de l'entreprise
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/entreprise/profile" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nom_entreprise" class="form-label">Nom de l'entreprise *</label>
                                        <input type="text" class="form-control" id="nom_entreprise" name="nom_entreprise" 
                                               value="<?= htmlspecialchars($profil['nom_entreprise'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="secteur_activite" class="form-label">Secteur d'activité</label>
                                        <select class="form-control" id="secteur_activite" name="secteur_activite">
                                            <option value="">Sélectionnez un secteur</option>
                                            <?php foreach ($secteurs as $secteur): ?>
                                                <option value="<?= htmlspecialchars($secteur) ?>" 
                                                        <?= ($profil['secteur_activite'] ?? '') === $secteur ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($secteur) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="taille_entreprise" class="form-label">Taille de l'entreprise</label>
                                        <select class="form-control" id="taille_entreprise" name="taille_entreprise">
                                            <option value="">Sélectionnez une taille</option>
                                            <?php foreach ($tailles as $taille): ?>
                                                <option value="<?= htmlspecialchars($taille) ?>" 
                                                        <?= ($profil['taille_entreprise'] ?? '') === $taille ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($taille) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="site_web" class="form-label">Site web</label>
                                        <input type="url" class="form-control" id="site_web" name="site_web" 
                                               value="<?= htmlspecialchars($profil['site_web'] ?? '') ?>" 
                                               placeholder="https://www.exemple.com">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description de l'entreprise</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Décrivez votre entreprise, ses activités, sa culture..."><?= htmlspecialchars($profil['description'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="adresse" class="form-label">Adresse</label>
                                        <input type="text" class="form-control" id="adresse" name="adresse" 
                                               value="<?= htmlspecialchars($profil['adresse'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="code_postal" class="form-label">Code postal</label>
                                        <input type="text" class="form-control" id="code_postal" name="code_postal" 
                                               value="<?= htmlspecialchars($profil['code_postal'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="ville" class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="ville" name="ville" 
                                               value="<?= htmlspecialchars($profil['ville'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone" 
                                               value="<?= htmlspecialchars($profil['telephone'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="logo" class="form-label">Logo de l'entreprise</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <?php if (!empty($profil['logo'])): ?>
                                            <small class="form-text text-muted">
                                                Logo actuel: <?= htmlspecialchars($profil['logo']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour le profil
                                </button>
                                <a href="/entreprise/dashboard" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Retour au dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Aperçu du profil
                        </h5>
                        <small class="text-muted">Voici comment votre profil apparaît aux candidats</small>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($profil['logo'])): ?>
                            <div class="text-center mb-3">
                                <img src="/uploads/logos/<?= htmlspecialchars($profil['logo']) ?>" 
                                     alt="Logo" class="img-fluid" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        
                        <h6 class="mb-2"><?= htmlspecialchars($profil['nom_entreprise'] ?? 'Nom de l\'entreprise') ?></h6>
                            
                            <?php if (!empty($profil['secteur_activite'])): ?>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-industry"></i> <?= htmlspecialchars($profil['secteur_activite']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($profil['taille_entreprise'])): ?>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-users"></i> <?= htmlspecialchars($profil['taille_entreprise']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($profil['ville'])): ?>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($profil['ville']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($profil['site_web'])): ?>
                                <p class="mb-1">
                                    <a href="<?= htmlspecialchars($profil['site_web']) ?>" target="_blank" class="text-primary">
                                        <i class="fas fa-globe"></i> Site web
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($profil['description'])): ?>
                                <hr>
                                <p class="small"><?= nl2br(htmlspecialchars($profil['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le footer
include __DIR__ . '/../layouts/footer.php';
?>
