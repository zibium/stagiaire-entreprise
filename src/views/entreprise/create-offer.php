<?php
// Vérifier que l'utilisateur est connecté et est une entreprise
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'entreprise') {
    header('Location: /auth/login');
    exit;
}

// Configuration de la page
$pageTitle = 'Créer une offre - Entreprise';
$currentPage = 'offers';
?>

<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .quick-date-buttons {
        margin-top: 5px;
    }
    .quick-date-buttons .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }
    
    /* Amélioration de la lisibilité des champs */
    .form-control, .form-select {
        font-size: 1.1rem !important;
        padding: 0.75rem 1rem !important;
        line-height: 1.5 !important;
    }
    
    .form-label {
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        margin-bottom: 0.75rem !important;
    }
    
    .form-text {
        font-size: 0.95rem !important;
    }
    
    textarea.form-control {
        min-height: 120px !important;
        font-size: 1.05rem !important;
    }
</style>
<?php include __DIR__ . '/../layouts/sidebar-entreprise.php'; ?>

    <div class="main-content">
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

        <!-- Contenu principal -->
        <div class="row">
            <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Créer une nouvelle offre</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= UrlHelper::url('entreprise/offers') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux offres
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations de l'offre</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= UrlHelper::url('entreprise/offers') ?>" method="POST">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="titre" class="form-label">Titre de l'offre *</label>
                                        <input type="text" class="form-control" id="titre" name="titre" 
                                               value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type_contrat" class="form-label">Type de contrat *</label>
                                        <select class="form-select" id="type_contrat" name="type_contrat" required>
                                            <option value="">Sélectionner un type</option>
                                            <?php foreach ($types_contrat as $type): ?>
                                                <option value="<?= htmlspecialchars($type) ?>" 
                                                        <?= (($_POST['type_contrat'] ?? '') === $type) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($type) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="duree" class="form-label">Durée *</label>
                                        <select class="form-select" id="duree" name="duree" required>
                                            <option value="">Sélectionner une durée</option>
                                            <option value="1 mois" <?= (($_POST['duree'] ?? '') === '1 mois') ? 'selected' : '' ?>>1 mois</option>
                                            <option value="2 mois" <?= (($_POST['duree'] ?? '') === '2 mois') ? 'selected' : '' ?>>2 mois</option>
                                            <option value="3 mois" <?= (($_POST['duree'] ?? '') === '3 mois') ? 'selected' : '' ?>>3 mois</option>
                                            <option value="4 mois" <?= (($_POST['duree'] ?? '') === '4 mois') ? 'selected' : '' ?>>4 mois</option>
                                            <option value="5 mois" <?= (($_POST['duree'] ?? '') === '5 mois') ? 'selected' : '' ?>>5 mois</option>
                                            <option value="6 mois" <?= (($_POST['duree'] ?? '') === '6 mois') ? 'selected' : '' ?>>6 mois</option>
                                            <option value="12 mois" <?= (($_POST['duree'] ?? '') === '12 mois') ? 'selected' : '' ?>>12 mois</option>
                                            <option value="Autre" <?= (($_POST['duree'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="lieu" class="form-label">Lieu de travail *</label>
                                        <select class="form-select" id="lieu" name="lieu" required>
                                            <option value="">Sélectionner un lieu</option>
                                            <option value="Sur site" <?= (($_POST['lieu'] ?? '') === 'Sur site') ? 'selected' : '' ?>>Sur site</option>
                                            <option value="Télétravail" <?= (($_POST['lieu'] ?? '') === 'Télétravail') ? 'selected' : '' ?>>Télétravail</option>
                                            <option value="Hybride" <?= (($_POST['lieu'] ?? '') === 'Hybride') ? 'selected' : '' ?>>Hybride</option>
                                            <option value="Mixte" <?= (($_POST['lieu'] ?? '') === 'Mixte') ? 'selected' : '' ?>>Mixte</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ville" class="form-label">Ville *</label>
                                        <select class="form-select" id="ville" name="ville" required>
                                             <option value="">Sélectionner une ville</option>
                                             <option value="Yaoundé" <?= (($_POST['ville'] ?? '') === 'Yaoundé') ? 'selected' : '' ?>>Yaoundé</option>
                                             <option value="Douala" <?= (($_POST['ville'] ?? '') === 'Douala') ? 'selected' : '' ?>>Douala</option>
                                             <option value="Garoua" <?= (($_POST['ville'] ?? '') === 'Garoua') ? 'selected' : '' ?>>Garoua</option>
                                             <option value="Bamenda" <?= (($_POST['ville'] ?? '') === 'Bamenda') ? 'selected' : '' ?>>Bamenda</option>
                                             <option value="Maroua" <?= (($_POST['ville'] ?? '') === 'Maroua') ? 'selected' : '' ?>>Maroua</option>
                                             <option value="Bafoussam" <?= (($_POST['ville'] ?? '') === 'Bafoussam') ? 'selected' : '' ?>>Bafoussam</option>
                                             <option value="Ngaoundéré" <?= (($_POST['ville'] ?? '') === 'Ngaoundéré') ? 'selected' : '' ?>>Ngaoundéré</option>
                                             <option value="Bertoua" <?= (($_POST['ville'] ?? '') === 'Bertoua') ? 'selected' : '' ?>>Bertoua</option>
                                             <option value="Edéa" <?= (($_POST['ville'] ?? '') === 'Edéa') ? 'selected' : '' ?>>Edéa</option>
                                             <option value="Loum" <?= (($_POST['ville'] ?? '') === 'Loum') ? 'selected' : '' ?>>Loum</option>
                                             <option value="Kumba" <?= (($_POST['ville'] ?? '') === 'Kumba') ? 'selected' : '' ?>>Kumba</option>
                                             <option value="Nkongsamba" <?= (($_POST['ville'] ?? '') === 'Nkongsamba') ? 'selected' : '' ?>>Nkongsamba</option>
                                             <option value="Mbouda" <?= (($_POST['ville'] ?? '') === 'Mbouda') ? 'selected' : '' ?>>Mbouda</option>
                                             <option value="Dschang" <?= (($_POST['ville'] ?? '') === 'Dschang') ? 'selected' : '' ?>>Dschang</option>
                                             <option value="Ebolowa" <?= (($_POST['ville'] ?? '') === 'Ebolowa') ? 'selected' : '' ?>>Ebolowa</option>
                                             <option value="Mbalmayo" <?= (($_POST['ville'] ?? '') === 'Mbalmayo') ? 'selected' : '' ?>>Mbalmayo</option>
                                             <option value="Sangmélima" <?= (($_POST['ville'] ?? '') === 'Sangmélima') ? 'selected' : '' ?>>Sangmélima</option>
                                             <option value="Limbe" <?= (($_POST['ville'] ?? '') === 'Limbe') ? 'selected' : '' ?>>Limbe</option>
                                             <option value="Kribi" <?= (($_POST['ville'] ?? '') === 'Kribi') ? 'selected' : '' ?>>Kribi</option>
                                             <option value="Tiko" <?= (($_POST['ville'] ?? '') === 'Tiko') ? 'selected' : '' ?>>Tiko</option>
                                             <option value="Buea" <?= (($_POST['ville'] ?? '') === 'Buea') ? 'selected' : '' ?>>Buea</option>
                                             <option value="Foumban" <?= (($_POST['ville'] ?? '') === 'Foumban') ? 'selected' : '' ?>>Foumban</option>
                                             <option value="Mokolo" <?= (($_POST['ville'] ?? '') === 'Mokolo') ? 'selected' : '' ?>>Mokolo</option>
                                             <option value="Kousséri" <?= (($_POST['ville'] ?? '') === 'Kousséri') ? 'selected' : '' ?>>Kousséri</option>
                                             <option value="Wum" <?= (($_POST['ville'] ?? '') === 'Wum') ? 'selected' : '' ?>>Wum</option>
                                             <option value="Mbanga" <?= (($_POST['ville'] ?? '') === 'Mbanga') ? 'selected' : '' ?>>Mbanga</option>
                                             <option value="Bafang" <?= (($_POST['ville'] ?? '') === 'Bafang') ? 'selected' : '' ?>>Bafang</option>
                                             <option value="Bandjoun" <?= (($_POST['ville'] ?? '') === 'Bandjoun') ? 'selected' : '' ?>>Bandjoun</option>
                                             <option value="Batouri" <?= (($_POST['ville'] ?? '') === 'Batouri') ? 'selected' : '' ?>>Batouri</option>
                                             <option value="Yokadouma" <?= (($_POST['ville'] ?? '') === 'Yokadouma') ? 'selected' : '' ?>>Yokadouma</option>
                                             <option value="Autre" <?= (($_POST['ville'] ?? '') === 'Autre') ? 'selected' : '' ?>>Autre</option>
                                         </select>
                                         <div class="form-text">Si votre ville n'est pas dans la liste, sélectionnez "Autre"</div>
                                         <input type="text" class="form-control mt-2" id="ville_autre" name="ville_autre" 
                                                placeholder="Saisir le nom de la ville" style="display: none;">
                                     </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="domaine" class="form-label">Domaine *</label>
                                        <select class="form-select" id="domaine" name="domaine" required>
                                            <option value="">Sélectionner un domaine</option>
                                            <?php foreach ($domaines as $domaine): ?>
                                                <option value="<?= htmlspecialchars($domaine) ?>" 
                                                        <?= (($_POST['domaine'] ?? '') === $domaine) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($domaine) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="niveau_etude" class="form-label">Niveau d'étude requis *</label>
                                        <select class="form-select" id="niveau_etude" name="niveau_etude" required>
                                            <option value="">Sélectionner un niveau</option>
                                            <?php foreach ($niveaux_etude as $niveau): ?>
                                                <option value="<?= htmlspecialchars($niveau) ?>" 
                                                        <?= (($_POST['niveau_etude'] ?? '') === $niveau) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($niveau) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="code_postal" class="form-label">Code postal *</label>
                                        <input type="text" class="form-control" id="code_postal" name="code_postal" 
                                               pattern="[0-9]{5}" maxlength="5" 
                                               value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description de l'offre *</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="6" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                    <div class="form-text">Décrivez les missions, l'environnement de travail, etc.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="competences_requises" class="form-label">Compétences requises *</label>
                                    <textarea class="form-control" id="competences_requises" name="competences_requises" 
                                              rows="4" required><?= htmlspecialchars($_POST['competences_requises'] ?? '') ?></textarea>
                                    <div class="form-text">Listez les compétences techniques et soft skills requises</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_debut" class="form-label">Date de début souhaitée</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                                   value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>">
                                            <button class="btn btn-outline-secondary" type="button" id="btn_date_debut">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            <small>
                                                <a href="#" class="text-decoration-none" onclick="setDateDebut('dans_1_mois')">Dans 1 mois</a> | 
                                                <a href="#" class="text-decoration-none" onclick="setDateDebut('dans_2_mois')">Dans 2 mois</a> | 
                                                <a href="#" class="text-decoration-none" onclick="setDateDebut('dans_3_mois')">Dans 3 mois</a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="date_limite_candidature" class="form-label">Date limite de candidature</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="date_limite_candidature" name="date_limite_candidature" 
                                                   value="<?= htmlspecialchars($_POST['date_limite_candidature'] ?? '') ?>">
                                            <button class="btn btn-outline-secondary" type="button" id="btn_date_limite">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            <small>
                                                <a href="#" class="text-decoration-none" onclick="setDateLimite('dans_2_semaines')">Dans 2 semaines</a> | 
                                                <a href="#" class="text-decoration-none" onclick="setDateLimite('dans_1_mois')">Dans 1 mois</a> | 
                                                <a href="#" class="text-decoration-none" onclick="setDateLimite('dans_2_mois')">Dans 2 mois</a>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= UrlHelper::url('entreprise/offers') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Créer l'offre
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Conseils pour votre offre</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> Conseils</h6>
                                <ul class="mb-0 small">
                                    <li>Rédigez un titre clair et attractif</li>
                                    <li>Détaillez les missions concrètes</li>
                                    <li>Précisez les compétences attendues</li>
                                    <li>Mentionnez les avantages (formation, encadrement...)</li>
                                    <li>Indiquez clairement la durée et les modalités</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Fonctions pour la sélection rapide des dates
function setDateDebut(periode) {
    const dateInput = document.getElementById('date_debut');
    const today = new Date();
    let targetDate = new Date(today);
    
    switch(periode) {
        case 'dans_1_mois':
            targetDate.setMonth(today.getMonth() + 1);
            break;
        case 'dans_2_mois':
            targetDate.setMonth(today.getMonth() + 2);
            break;
        case 'dans_3_mois':
            targetDate.setMonth(today.getMonth() + 3);
            break;
    }
    
    dateInput.value = targetDate.toISOString().split('T')[0];
    event.preventDefault();
}

function setDateLimite(periode) {
    const dateInput = document.getElementById('date_limite_candidature');
    const today = new Date();
    let targetDate = new Date(today);
    
    switch(periode) {
        case 'dans_2_semaines':
            targetDate.setDate(today.getDate() + 14);
            break;
        case 'dans_1_mois':
            targetDate.setMonth(today.getMonth() + 1);
            break;
        case 'dans_2_mois':
            targetDate.setMonth(today.getMonth() + 2);
            break;
    }
    
    dateInput.value = targetDate.toISOString().split('T')[0];
    event.preventDefault();
}

// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const codePostalInput = document.getElementById('code_postal');
    
    // Amélioration des sélecteurs de dates
    const dateDebutInput = document.getElementById('date_debut');
    const dateLimiteInput = document.getElementById('date_limite_candidature');
    
    // Définir la date minimale à aujourd'hui
      const today = new Date().toISOString().split('T')[0];
      dateDebutInput.min = today;
      dateLimiteInput.min = today;
      
      // Gestion du champ ville "Autre"
      const villeSelect = document.getElementById('ville');
      const villeAutreInput = document.getElementById('ville_autre');
      
      villeSelect.addEventListener('change', function() {
          if (this.value === 'Autre') {
              villeAutreInput.style.display = 'block';
              villeAutreInput.required = true;
          } else {
              villeAutreInput.style.display = 'none';
              villeAutreInput.required = false;
              villeAutreInput.value = '';
          }
      });
      
      // Validation du code postal
    codePostalInput.addEventListener('input', function() {
        const value = this.value;
        if (value.length > 0 && !/^[0-9]{1,5}$/.test(value)) {
            this.setCustomValidity('Le code postal doit contenir 5 chiffres');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires');
        }
    });
});
</script>

            </div>
        </div>
    </div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>