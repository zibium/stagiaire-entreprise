/**
 * JobBoard - Script JavaScript principal
 * Gestion de l'interactivité et des fonctionnalités côté client
 */

// Configuration globale
const JobBoard = {
    config: {
        debounceDelay: 300,
        animationDuration: 300,
        maxFileSize: 5 * 1024 * 1024, // 5MB
        allowedFileTypes: ['application/pdf']
    },
    
    // Cache pour les éléments DOM fréquemment utilisés
    elements: {},
    
    // Initialisation de l'application
    init() {
        this.cacheElements();
        this.bindEvents();
        this.initComponents();
        console.log('JobBoard initialized');
    },
    
    // Cache des éléments DOM
    cacheElements() {
        this.elements = {
            navbar: document.querySelector('.navbar'),
            navbarToggle: document.querySelector('.navbar-toggle'),
            navbarNav: document.querySelector('.navbar-nav'),
            alerts: document.querySelectorAll('.alert'),
            forms: document.querySelectorAll('form'),
            fileInputs: document.querySelectorAll('input[type="file"]'),
            searchForms: document.querySelectorAll('.search-form'),
            modals: document.querySelectorAll('.modal'),
            tooltips: document.querySelectorAll('[data-tooltip]')
        };
    },
    
    // Liaison des événements
    bindEvents() {
        // Navigation mobile
        if (this.elements.navbarToggle) {
            this.elements.navbarToggle.addEventListener('click', this.toggleMobileNav.bind(this));
        }
        
        // Gestion des alertes
        this.elements.alerts.forEach(alert => {
            this.initAlert(alert);
        });
        
        // Validation des formulaires
        this.elements.forms.forEach(form => {
            this.initFormValidation(form);
        });
        
        // Upload de fichiers
        this.elements.fileInputs.forEach(input => {
            this.initFileUpload(input);
        });
        
        // Recherche en temps réel
        this.elements.searchForms.forEach(form => {
            this.initSearchForm(form);
        });
        
        // Fermeture des modales avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
        
        // Gestion du scroll pour la navbar sticky
        window.addEventListener('scroll', this.debounce(this.handleScroll.bind(this), 100));
    },
    
    // Initialisation des composants
    initComponents() {
        this.initTooltips();
        this.initAnimations();
        this.initAccessibility();
    },
    
    // Navigation mobile
    toggleMobileNav() {
        if (this.elements.navbarNav) {
            this.elements.navbarNav.classList.toggle('active');
            this.elements.navbarToggle.setAttribute(
                'aria-expanded',
                this.elements.navbarNav.classList.contains('active')
            );
        }
    },
    
    // Gestion des alertes
    initAlert(alert) {
        // Auto-hide après 5 secondes
        setTimeout(() => {
            this.hideAlert(alert);
        }, 5000);
        
        // Bouton de fermeture
        const closeBtn = alert.querySelector('.alert-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideAlert(alert));
        }
    },
    
    hideAlert(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, this.config.animationDuration);
    },
    
    // Validation des formulaires
    initFormValidation(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // Validation en temps réel
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', this.debounce(() => this.validateField(input), this.config.debounceDelay));
        });
        
        // Validation à la soumission
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
            }
        });
    },
    
    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        let isValid = true;
        let message = '';
        
        // Suppression des messages d'erreur précédents
        this.clearFieldError(field);
        
        // Validation des champs requis
        if (required && !value) {
            isValid = false;
            message = 'Ce champ est obligatoire.';
        }
        
        // Validation par type
        if (value && isValid) {
            switch (type) {
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        message = 'Veuillez saisir une adresse email valide.';
                    }
                    break;
                    
                case 'tel':
                    const phoneRegex = /^[0-9+\-\s()]+$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        message = 'Veuillez saisir un numéro de téléphone valide.';
                    }
                    break;
                    
                case 'password':
                    if (value.length < 8) {
                        isValid = false;
                        message = 'Le mot de passe doit contenir au moins 8 caractères.';
                    }
                    break;
            }
        }
        
        // Validation personnalisée
        const minLength = field.getAttribute('data-min-length');
        if (minLength && value.length < parseInt(minLength)) {
            isValid = false;
            message = `Ce champ doit contenir au moins ${minLength} caractères.`;
        }
        
        const maxLength = field.getAttribute('data-max-length');
        if (maxLength && value.length > parseInt(maxLength)) {
            isValid = false;
            message = `Ce champ ne peut pas dépasser ${maxLength} caractères.`;
        }
        
        // Affichage du résultat
        if (!isValid) {
            this.showFieldError(field, message);
        }
        
        return isValid;
    },
    
    validateForm(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.color = 'var(--danger-color)';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        
        field.parentNode.appendChild(errorDiv);
    },
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    },
    
    // Upload de fichiers
    initFileUpload(input) {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.validateFile(file, input);
            }
        });
        
        // Drag & Drop
        const dropZone = input.closest('.file-upload-zone');
        if (dropZone) {
            this.initDragAndDrop(dropZone, input);
        }
    },
    
    validateFile(file, input) {
        let isValid = true;
        let message = '';
        
        // Vérification de la taille
        if (file.size > this.config.maxFileSize) {
            isValid = false;
            message = 'Le fichier est trop volumineux (maximum 5MB).';
        }
        
        // Vérification du type
        if (input.accept && !this.isFileTypeAllowed(file, input.accept)) {
            isValid = false;
            message = 'Type de fichier non autorisé.';
        }
        
        if (!isValid) {
            this.showFileError(input, message);
            input.value = '';
        } else {
            this.showFileSuccess(input, file);
        }
    },
    
    isFileTypeAllowed(file, accept) {
        const acceptedTypes = accept.split(',').map(type => type.trim());
        return acceptedTypes.some(type => {
            if (type.startsWith('.')) {
                return file.name.toLowerCase().endsWith(type.toLowerCase());
            }
            return file.type === type;
        });
    },
    
    showFileError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'file-error';
        errorDiv.textContent = message;
        errorDiv.style.color = 'var(--danger-color)';
        errorDiv.style.marginTop = '0.5rem';
        
        input.parentNode.appendChild(errorDiv);
        
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 5000);
    },
    
    showFileSuccess(input, file) {
        const successDiv = document.createElement('div');
        successDiv.className = 'file-success';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Fichier sélectionné: ${file.name} (${this.formatFileSize(file.size)})
        `;
        successDiv.style.color = 'var(--success-color)';
        successDiv.style.marginTop = '0.5rem';
        
        // Supprimer les messages précédents
        const existingMessages = input.parentNode.querySelectorAll('.file-success, .file-error');
        existingMessages.forEach(msg => msg.remove());
        
        input.parentNode.appendChild(successDiv);
    },
    
    initDragAndDrop(dropZone, input) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, this.preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
        });
        
        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                this.validateFile(files[0], input);
            }
        });
    },
    
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    },
    
    // Recherche en temps réel
    initSearchForm(form) {
        const searchInput = form.querySelector('input[type="search"], input[name*="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', 
                this.debounce(() => this.handleSearch(form), this.config.debounceDelay)
            );
        }
    },
    
    handleSearch(form) {
        // Implémentation de la recherche AJAX si nécessaire
        console.log('Search triggered');
    },
    
    // Tooltips
    initTooltips() {
        this.elements.tooltips.forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip.bind(this));
            element.addEventListener('mouseleave', this.hideTooltip.bind(this));
        });
    },
    
    showTooltip(e) {
        const element = e.target;
        const text = element.getAttribute('data-tooltip');
        
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: var(--dark-gray);
            color: var(--white);
            padding: 0.5rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        
        setTimeout(() => tooltip.style.opacity = '1', 10);
        
        element._tooltip = tooltip;
    },
    
    hideTooltip(e) {
        const element = e.target;
        if (element._tooltip) {
            element._tooltip.remove();
            delete element._tooltip;
        }
    },
    
    // Animations
    initAnimations() {
        // Observer pour les animations au scroll
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            });
            
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        }
    },
    
    // Accessibilité
    initAccessibility() {
        // Gestion du focus au clavier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
        
        // Skip links
        const skipLink = document.querySelector('.skip-link');
        if (skipLink) {
            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(skipLink.getAttribute('href'));
                if (target) {
                    target.focus();
                    target.scrollIntoView();
                }
            });
        }
    },
    
    // Gestion du scroll
    handleScroll() {
        const scrolled = window.scrollY > 50;
        if (this.elements.navbar) {
            this.elements.navbar.classList.toggle('scrolled', scrolled);
        }
    },
    
    // Modales
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus sur le premier élément focusable
            const focusable = modal.querySelector('button, input, textarea, select, a[href]');
            if (focusable) {
                focusable.focus();
            }
        }
    },
    
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    },
    
    closeAllModals() {
        this.elements.modals.forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    },
    
    // Utilitaires
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    // API pour les autres scripts
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            this.hideAlert(notification);
        }, 5000);
    },
    
    confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
};

// Initialisation au chargement du DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => JobBoard.init());
} else {
    JobBoard.init();
}

// Export pour utilisation dans d'autres scripts
window.JobBoard = JobBoard;