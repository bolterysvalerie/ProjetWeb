<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Modifier le produit</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">                        <li class="breadcrumb-item"><a href="index.php?url=admin">Admin</a></li>
                        <li class="breadcrumb-item"><a href="index.php?url=admin&action=products">Produits</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </nav>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-box"></i> Informations du produit</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="index.php?url=admin&action=edit_product">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom du produit *</label>
                                            <input type="text" class="form-control" id="nom" name="nom" 
                                                   value="<?= htmlspecialchars($product['nom']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prix" class="form-label">Prix (€) *</label>
                                            <input type="number" step="0.01" class="form-control" id="prix" name="prix" 
                                                   value="<?= $product['prix'] ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Catégorie *</label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Sélectionner une catégorie</option>
                                                <?php foreach($categories as $category): ?>
                                                    <option value="<?= $category['id'] ?>" 
                                                            <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($category['nom']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   value="<?= $product['stock'] ?>" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                                </div>                                <div class="mb-3">
                                    <label for="image" class="form-label">Image du produit</label>
                                    <?php if($product['image']): ?>
                                        <div class="mb-2">
                                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Image du produit" 
                                                 class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    <?php else: ?>
                                        <div class="mb-2">
                                            <span class="text-muted">Aucune image</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="url" class="form-control" id="image" name="image" 
                                           value="<?= htmlspecialchars($product['image'] ?? '') ?>" 
                                           placeholder="https://exemple.com/image.jpg">
                                    <div class="form-text">URL de l'image - Formats acceptés: JPG, JPEG, PNG, GIF, WEBP</div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="index.php?url=admin&action=products" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.img-thumbnail {
    border: 2px solid #dee2e6;
}

.btn {
    border-radius: 0.375rem;
}
</style>
