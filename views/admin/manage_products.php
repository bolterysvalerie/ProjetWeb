<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="manage-products-container">
    <div class="products-header">
        <a href="index.php?url=admin" class="btn btn-secondary">← Retour à l'administration</a>
        <h2>Gestion des Produits</h2>
    </div>

    <div class="add-product-section">
        <h3>Ajouter un Nouveau Produit</h3>
        <form action="index.php?url=admin&action=add_product" method="POST" class="add-product-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom du produit * :</label>
                    <input type="text" id="nom" name="nom" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Catégorie * :</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= ucfirst(htmlspecialchars($category['nom'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="prix">Prix (€) * :</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">URL de l'image :</label>
                <input type="url" id="image" name="image" placeholder="https://exemple.com/image.jpg">
                <small class="form-help">Formats acceptés: JPG, JPEG, PNG, GIF, WEBP</small>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock initial :</label>
                <input type="number" id="stock" name="stock" value="100" min="0">
            </div>
            
            <button type="submit" class="btn btn-success">Ajouter le produit</button>
        </form>
    </div>

    <div class="products-list-section">
        <h3>Liste des Produits Existants</h3>
        
        <?php if(empty($products)): ?>
            <p>Aucun produit en base.</p>
        <?php else: ?>            <?php 
            $current_category = '';
            foreach($products as $product):
                if($product['category'] !== $current_category):
                    if($current_category !== ''): ?>
                        </div>
                    <?php endif; ?>
                    <div class="category-products-section">
                        <h4><?= ucfirst(htmlspecialchars($product['category'])) ?></h4>
                        <div class="products-grid-admin">
                    <?php 
                    $current_category = $product['category'];
                endif;
            ?>
            
            <div class="product-card-admin">
                <div class="product-info-admin">
                    <h5><?= htmlspecialchars($product['nom']) ?></h5>
                    <p class="product-description-admin">
                        <?= htmlspecialchars($product['description']) ?>
                    </p>
                    <div class="product-details-admin">
                        <span class="product-price-admin"><?= number_format($product['prix'], 2) ?> €</span>
                        <span class="product-stock-admin">Stock: <?= $product['stock'] ?></span>
                        <span class="product-date-admin">
                            Ajouté le <?= date('d/m/Y', strtotime($product['date_ajout'])) ?>
                        </span>
                    </div>
                </div>
                  <div class="product-actions-admin">
                    <a href="index.php?url=admin&action=edit_product&id=<?= $product['id'] ?>" 
                       class="btn btn-primary btn-sm">Modifier</a>
                    <form action="index.php?url=admin&action=delete_product" method="POST" 
                          onsubmit="return confirm('Supprimer ce produit définitivement ?')" 
                          style="display: inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </div>
            </div>

            <?php endforeach; ?>
            </div></div> <!-- Fermer la dernière catégorie -->
        <?php endif; ?>
    </div>

    <div class="products-stats">
        <h3>Statistiques</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Produits</h4>
                <p class="stat-number"><?= count($products) ?></p>
            </div>            <?php 
            $categories_count = [];
            foreach($products as $product) {
                $cat = $product['category'];
                $categories_count[$cat] = ($categories_count[$cat] ?? 0) + 1;
            }
            foreach($categories_count as $cat_name => $count):
            ?>
                <div class="stat-card">
                    <h4><?= ucfirst(htmlspecialchars($cat_name)) ?></h4>
                    <p class="stat-number"><?= $count ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

