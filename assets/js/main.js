// Fonctions utilitaires globales
const Utils = {
    // Afficher une notification toast
    showToast: function(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        const toast = this.createToast(message, type);
        toastContainer.appendChild(toast);
        
        // Afficher le toast avec Bootstrap
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Supprimer automatiquement après fermeture
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    },
    
    createToastContainer: function() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    },
    
    createToast: function(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        return toast;
    },
    
    // Confirmer une action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },
    
    // Formater un prix
    formatPrice: function(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    },
    
    // Faire une requête AJAX
    ajax: function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const config = Object.assign({}, defaults, options);
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                throw error;
            });
    }
};

// Gestion du panier
const Cart = {
    // Ajouter un produit au panier
    addProduct: function(productId, quantity = 1) {
        const data = new FormData();
        data.append('product_id', productId);
        data.append('quantity', quantity);
        
        fetch('index.php?url=shop&action=add_to_cart', {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Utils.showToast(data.message, 'success');
                this.updateCartCount(data.cart_count);
            } else {
                Utils.showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Utils.showToast('Erreur lors de l\'ajout au panier', 'danger');
        });
    },
    
    // Mettre à jour la quantité dans le panier
    updateQuantity: function(productId, quantity) {
        const data = new FormData();
        data.append('product_id', productId);
        data.append('quantity', quantity);
        
        fetch('index.php?url=shop&action=update_cart', {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'affichage
                this.updateCartDisplay(data.cart);
                this.updateCartCount(data.cart_count);
                Utils.showToast('Panier mis à jour', 'success');
            } else {
                Utils.showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Utils.showToast('Erreur lors de la mise à jour', 'danger');
        });
    },
    
    // Supprimer un produit du panier
    removeProduct: function(productId) {
        Utils.confirm('Êtes-vous sûr de vouloir supprimer ce produit ?', () => {
            this.updateQuantity(productId, 0);
        });
    },
    
    // Mettre à jour le compteur du panier
    updateCartCount: function(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    },
    
    // Mettre à jour l'affichage du panier
    updateCartDisplay: function(cart) {
        const cartContainer = document.getElementById('cart-items');
        if (!cartContainer) return;
        
        // Recalculer le total et mettre à jour l'affichage
        let total = 0;
        cart.forEach(item => {
            total += item.price * item.quantity;
            const itemElement = document.querySelector(`[data-product-id="${item.id}"]`);
            if (itemElement) {
                const quantityInput = itemElement.querySelector('.quantity-input');
                const subtotalElement = itemElement.querySelector('.item-subtotal');
                
                if (quantityInput) quantityInput.value = item.quantity;
                if (subtotalElement) subtotalElement.textContent = Utils.formatPrice(item.price * item.quantity);
            }
        });
        
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = Utils.formatPrice(total);
        }
    }
};

// Gestion des filtres de catégorie
const CategoryFilter = {
    init: function() {
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            // Soumission automatique du formulaire lors du changement de catégorie
            categoryFilter.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }
    }
};

// Gestion du chat
const Chat = {
    messageContainer: null,
    messageForm: null,
    messageInput: null,
    
    init: function() {
        this.messageContainer = document.getElementById('chat-messages');
        this.messageForm = document.getElementById('chat-form');
        this.messageInput = document.getElementById('message-input');
        
        if (this.messageForm) {
            this.messageForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }
        
        // Actualiser les messages toutes les 3 secondes
        if (this.messageContainer) {
            setInterval(() => {
                this.loadMessages();
            }, 3000);
            
            // Charger les messages au démarrage
            this.loadMessages();
        }
    },
    
    sendMessage: function() {
        const message = this.messageInput.value.trim();
        if (!message) return;
        
        const data = new FormData();
        data.append('message', message);
        
        fetch('index.php?url=chat&action=send', {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.messageInput.value = '';
                this.loadMessages();
            } else {
                Utils.showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Utils.showToast('Erreur lors de l\'envoi du message', 'danger');
        });
    },
    
    loadMessages: function() {
        fetch('index.php?url=chat&action=get_messages', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.displayMessages(data.messages);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    },
    
    displayMessages: function(messages) {
        this.messageContainer.innerHTML = '';
        
        messages.forEach(message => {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message fade-in';
            
            // Vérifier si c'est notre message
            const currentUserId = document.body.dataset.userId;
            if (message.user_id == currentUserId) {
                messageElement.classList.add('own');
            } else {
                messageElement.classList.add('other');
            }
            
            messageElement.innerHTML = `
                <div class="message-author">${message.user_name}</div>
                <div class="message-content">${message.message}</div>
                <div class="message-time">${message.created_at}</div>
            `;
            
            this.messageContainer.appendChild(messageElement);
        });
        
        // Faire défiler vers le bas
        this.messageContainer.scrollTop = this.messageContainer.scrollHeight;
    }
};

// Gestion des formulaires
const Forms = {
    init: function() {
        // Validation des formulaires
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
        
        // Prévisualisation des images
        const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.previewImage(e.target);
            });
        });
    },
    
    previewImage: function(input) {
        const previewId = input.dataset.preview;
        const preview = document.getElementById(previewId);
        
        if (input.files && input.files[0] && preview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
};

// Gestion des produits
const Products = {
    init: function() {
        // Gestion des filtres
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            const inputs = filterForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.applyFilters();
                });
            });
        }
        
        // Gestion de la recherche
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.search(searchInput.value);
                }, 500);
            });
        }
    },
    
    applyFilters: function() {
        const form = document.getElementById('filter-form');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Recharger la page avec les filtres
        window.location.href = 'index.php?url=shop&' + params.toString();
    },
    
    search: function(query) {
        if (query.length < 2) return;
        
        fetch(`index.php?url=shop&search=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const productContainer = document.getElementById('products-container');
            if (productContainer) {
                productContainer.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Erreur de recherche:', error);
        });
    }
};

// Gestion de l'administration
const Admin = {
    init: function() {
        // Confirmation pour les suppressions
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const message = 'Êtes-vous sûr de vouloir supprimer cet élément ?';
                Utils.confirm(message, () => {
                    window.location.href = button.href;
                });
            });
        });
        
        // Graphiques (si Chart.js est disponible)
        if (typeof Chart !== 'undefined') {
            this.initCharts();
        }
    },
    
    initCharts: function() {
        // Graphique des ventes
        const salesChart = document.getElementById('sales-chart');
        if (salesChart) {
            new Chart(salesChart, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Ventes',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: 'rgb(13, 110, 253)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Évolution des ventes'
                        }
                    }
                }
            });
        }
    }
};

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser tous les modules
    Forms.init();
    Products.init();
    Chat.init();
    Admin.init();
    
    // Initialiser les filtres de catégorie
    CategoryFilter.init();
    
    // Gestion des boutons d'ajout au panier
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            Cart.addProduct(productId, quantity);
        });
    });
    
    // Gestion des inputs de quantité dans le panier
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            
            if (quantity > 0 && quantity <= 10) {
                Cart.updateQuantity(productId, quantity);
            } else {
                this.value = Math.min(Math.max(1, quantity), 10);
            }
        });
    });
    
    // Gestion des boutons de suppression du panier
    const removeButtons = document.querySelectorAll('.btn-remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            Cart.removeProduct(productId);
        });
    });
    
    // Animation d'apparition pour les éléments
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);
    
    // Observer les cartes de produits
    const productCards = document.querySelectorAll('.product-card, .blog-post, .admin-card');
    productCards.forEach(card => {
        observer.observe(card);
    });
    
    // Smooth scroll pour les liens d'ancrage
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Gestion du mode sombre (optionnel)
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });
        
        // Restaurer le mode sombre
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }
    
    // Masquer le loader si présent
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
      // Gestion spécifique du dropdown utilisateur
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        try {
            // Initialiser Bootstrap dropdown explicitement
            const bsDropdown = new bootstrap.Dropdown(userDropdown, {
                autoClose: true,
                boundary: 'clippingParents'
            });
            
            // Attendre que Bootstrap soit entièrement chargé
            setTimeout(() => {
                // Vérifier que le dropdown fonctionne
                const dropdownMenu = userDropdown.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    console.log('✅ Dropdown utilisateur initialisé avec succès');
                }
            }, 100);
            
        } catch (error) {
            console.error('❌ Erreur initialisation dropdown:', error);
            
            // Fallback manuel si Bootstrap ne fonctionne pas
            userDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    // Fermer tous les autres dropdowns ouverts
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });
                    
                    // Toggle le dropdown actuel
                    dropdownMenu.classList.toggle('show');
                }
            });
            
            // Fermer le dropdown en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target)) {
                    const dropdownMenu = userDropdown.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });
        }
    }
    
    console.log('E-Commerce App initialisé avec succès !');
});

// Gestionnaire d'erreurs global
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
    Utils.showToast('Une erreur s\'est produite', 'danger');
});

// Export des modules pour utilisation externe
window.ECommerce = {
    Utils,
    Cart,
    Chat,
    Forms,
    Products,
    Admin
};
