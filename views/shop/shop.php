<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="shop-container">
    <div class="shop-header">
        <h2>Boutique</h2>
        <p>Découvrez nos produits dans différentes catégories</p>
    </div>    <div class="shop-filters">
        <h3>Filtrer par catégorie :</h3>
        <div class="category-filters">
            <form action="index.php" method="GET" class="category-filter-form">
                <input type="hidden" name="url" value="shop">
                <input type="hidden" name="action" value="index">                <div class="form-group d-flex">
                    <select name="category" class="form-select" id="categoryFilter" aria-label="Sélectionner une catégorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($category['nom'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Le bouton de filtrage a été supprimé car le formulaire est soumis automatiquement -->
                </div>
            </form>            
            <!-- Affichage de la catégorie actuelle -->
            <?php if(isset($_GET['category']) && !empty($_GET['category'])): 
                $currentCategoryName = '';
                foreach($categories as $cat) {
                    if($cat['id'] == $_GET['category']) {
                        $currentCategoryName = $cat['nom'];
                        break;
                    }
                }
            ?>
                <div class="current-filter mt-3">
                    <p class="mb-0">
                        <span><i class="fas fa-filter me-2"></i>Catégorie actuelle : <strong><?= ucfirst(htmlspecialchars($currentCategoryName)) ?></strong></span>
                        <a href="index.php?url=shop&action=index" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>    <div class="products-grid">
        <?php if(empty($products)): ?>
            <div class="no-products text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <p class="h5">Aucun produit trouvé dans cette catégorie.</p>
            </div>
        <?php else: ?>
            <?php 
            $current_category = '';
            foreach($products as $product): 
                if($product['category'] !== $current_category):
                    if($current_category !== ''): ?>
                        </div></div> <!-- Fermer la rangée et section précédentes -->
                    <?php endif; ?>
                    <div class="category-section mb-5">
                        <h3 class="category-title mb-4">
                            <i class="fas fa-tag me-2"></i><?= ucfirst(htmlspecialchars($product['category'])) ?>
                        </h3>
                        <div class="row g-4">
                    <?php 
                    $current_category = $product['category'];
                endif; 
            ?>
            
            <div class="col-md-4">
                <div class="card product-card h-100">
                    <div class="product-image">
                        <?php if(!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['nom']) ?>" 
                                 class="img-fluid">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image"></i>
                                <span>Pas d'image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body product-info">
                        <h5 class="card-title"><?= htmlspecialchars($product['nom']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="product-price"><?= number_format($product['prix'], 2) ?> €</span>
                            <small class="text-muted"><?= htmlspecialchars($product['category']) ?></small>
                        </div>
                        <div class="mb-3">
                            <?php if($product['stock'] > 0): ?>
                                <small class="badge bg-success">
                                    <i class="fas fa-box me-1"></i><?= $product['stock'] ?> en stock
                                </small>
                            <?php else: ?>
                                <small class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Rupture de stock
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <?php if(isLoggedIn()): ?>
                            <?php if($product['stock'] > 0): ?>
                                <form action="index.php?url=shop&action=add_to_cart" method="POST" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="row g-2 mb-2">
                                        <div class="col">
                                            <select name="quantity" class="form-select form-select-sm">
                                                <?php for($i = 1; $i <= min(10, $product['stock']); $i++): ?>
                                                    <option value="<?= $i ?>">Qté: <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times me-2"></i>Produit indisponible
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="index.php?url=user&action=login" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter pour acheter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
            </div></div> <!-- Fermer la dernière catégorie -->
        <?php endif; ?>
    </div>

    <?php if(!isLoggedIn()): ?>
        <div class="shop-info">
            <div class="info-card">
                <h3>Pour effectuer des achats</h3>
                <p>Vous devez être connecté pour ajouter des produits à votre panier et effectuer des achats.</p>
                <div class="auth-buttons">
                    <a href="index.php?page=login" class="btn btn-primary">Se connecter</a>
                    <a href="index.php?page=register" class="btn btn-secondary">S'inscrire</a>
                </div>
            </div>        </div>
    <?php endif; ?>
</div>
