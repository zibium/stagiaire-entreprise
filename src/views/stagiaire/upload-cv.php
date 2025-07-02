<?php
use JobBoard\Utils\UrlHelper;
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= UrlHelper::url('stagiaire/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= UrlHelper::url('stagiaire/profile') ?>">
                            <i class="fas fa-user me-2"></i>
                            Mon Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= UrlHelper::url('stagiaire/upload-cv') ?>">
                            <i class="fas fa-file-upload me-2"></i>
                            Upload CV
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= UrlHelper::url('stagiaire/offres') ?>">
                            <i class="fas fa-search me-2"></i>
                            Chercher un stage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= UrlHelper::url('stagiaire/candidatures') ?>">
                            <i class="fas fa-paper-plane me-2"></i>
                            Mes candidatures
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Upload CV</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-upload me-2"></i>
                                Télécharger votre CV
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($profil['cv_path'])): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Vous avez déjà un CV téléchargé. Vous pouvez le remplacer en téléchargeant un nouveau fichier.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">CV actuel :</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span><?= basename($profil['cv_path']) ?></span>
                                        <a href="<?= UrlHelper::url('uploads/' . $profil['cv_path']) ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                    </div>
                                </div>
                                <hr>
                            <?php endif; ?>

                            <form action="<?= UrlHelper::url('stagiaire/upload-cv') ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= \JobBoard\Middleware\AuthMiddleware::generateCsrfToken() ?>">
                                
                                <div class="mb-3">
                                    <label for="cv_file" class="form-label">Sélectionner votre CV (PDF uniquement)</label>
                                    <input type="file" class="form-control" id="cv_file" name="cv_file" accept=".pdf" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Formats acceptés : PDF uniquement. Taille maximale : 5 MB.
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= UrlHelper::url('stagiaire/dashboard') ?>" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-arrow-left me-1"></i>Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i>Télécharger
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Conseils pour votre CV
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Format PDF uniquement
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Taille maximale : 5 MB
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Nom de fichier clair
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    CV à jour et complet
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Mise en page professionnelle
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>