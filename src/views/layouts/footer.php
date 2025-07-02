</main>
    
    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">JobBoard</h3>
                    <p class="footer-description">
                        La plateforme de référence pour trouver votre stage idéal.
                        Connectez-vous avec les meilleures entreprises.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Étudiants</h4>
                    <ul class="footer-links">
                        <li><a href="/offres">Offres de stage</a></li>
                        <li><a href="/entreprises">Entreprises</a></li>
                        <li><a href="/conseils">Conseils carrière</a></li>
                        <li><a href="/aide">Centre d'aide</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Entreprises</h4>
                    <ul class="footer-links">
                        <li><a href="/auth/register">Publier une offre</a></li>
                        <li><a href="/entreprise/pricing">Tarifs</a></li>
                        <li><a href="/entreprise/guide">Guide recruteur</a></li>
                        <li><a href="/contact">Nous contacter</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Légal</h4>
                    <ul class="footer-links">
                        <li><a href="/legal/terms">Conditions d'utilisation</a></li>
                        <li><a href="/legal/privacy">Politique de confidentialité</a></li>
                        <li><a href="/legal/cookies">Politique des cookies</a></li>
                        <li><a href="/legal/mentions">Mentions légales</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?= date('Y') ?> JobBoard. Tous droits réservés.</p>
                </div>
                <div class="footer-meta">
                    <span class="footer-version">v1.0.0</span>
                    <span class="footer-separator">•</span>
                    <span class="footer-status">🟢 Tous les systèmes opérationnels</span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to top button -->
    <button class="back-to-top" type="button" aria-label="Retour en haut">
        <i class="fas fa-chevron-up" aria-hidden="true"></i>
    </button>
    
    <!-- JavaScript Files -->
    <script src="/js/app.js" defer></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= htmlspecialchars($js) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Custom JavaScript for specific pages -->
    <?php if (isset($customJS)): ?>
        <script><?= $customJS ?></script>
    <?php endif; ?>
    
    <!-- Initialize app -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof JobBoard !== 'undefined') {
                JobBoard.init();
            }
        });
    </script>
</body>
</html>