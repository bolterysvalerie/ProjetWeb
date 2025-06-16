<?php ob_start(); ?>
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">            <div class="col-lg-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                <h1 class="hero-title">Bon retour, <?= htmlspecialchars($_SESSION['pseudo'] ?? $_SESSION['username'] ?? 'Utilisateur') ?> !</h1>
                <p class="hero-subtitle">Continuez votre expérience d'achat et découvrez nos dernières nouveautés</p>
                <?php else: ?>
                <h1 class="hero-title">Bienvenue sur notre E-Commerce</h1>
                <p class="hero-subtitle">Découvrez nos produits de qualité et profitez d'une expérience d'achat exceptionnelle</p>
                <?php endif; ?><div class="mt-4">
                    <a href="index.php?url=shop" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-shopping-bag me-2"></i>Découvrir la boutique
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="index.php?url=user&action=register" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                    </a>
                    <?php else: ?>
                    <a href="index.php?url=user&action=logout" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-store display-1 text-white opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Nos Services</h2>
        <p class="text-muted">Découvrez tout ce que notre plateforme a à vous offrir</p>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card service-card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Boutique en ligne</h5>
                    <p class="card-text">Parcourez nos produits dans différentes catégories : informatique, livres et hi-fi.</p>
                    <p class="text-muted small">
                        <i class="fas fa-eye me-1"></i>Accessible à tous<br>
                        <i class="fas fa-lock me-1"></i>Achat réservé aux membres
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="index.php?url=shop" class="btn btn-primary">Voir la boutique</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card service-card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-blog fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Blog & Actualités</h5>
                    <p class="card-text">Consultez nos articles et participez aux discussions avec la communauté.</p>
                    <p class="text-muted small">
                        <i class="fas fa-eye me-1"></i>Lecture libre<br>
                        <i class="fas fa-comments me-1"></i>Commentaires pour les membres
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="index.php?url=blog" class="btn btn-success">Lire le blog</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card service-card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-comments fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Mini-Chat</h5>
                    <p class="card-text">Discutez en temps réel avec les autres membres de la communauté.</p>
                    <p class="text-muted small">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <i class="fas fa-check-circle text-success me-1"></i>Vous avez accès au chat
                        <?php else: ?>
                        <i class="fas fa-lock me-1"></i>Réservé aux membres connectés
                        <?php endif; ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?url=chat" class="btn btn-info">Accéder au chat</a>
                    <?php else: ?>
                    <a href="index.php?url=user&action=login" class="btn btn-outline-info">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<?php if (isset($featuredProducts) && !empty($featuredProducts)): ?>
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Produits en vedette</h2>
            <p class="text-muted">Découvrez nos meilleures ventes</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-4">                <div class="card product-card h-100">
                    <div class="product-image">
                        <?php if (!empty($product['image'])): ?>
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
                      <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['nom']) ?></h5>
                        <p class="card-text text-muted"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price"><?= number_format($product['prix'], 2) ?> €</span>
                            <small class="text-muted"><?= htmlspecialchars($product['category']) ?></small>
                        </div>
                        <div class="mt-2">
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
                    </div>                    <div class="card-footer bg-transparent">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if($product['stock'] > 0): ?>
                        <form action="index.php?url=shop&action=add_to_cart" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
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
        </div>
          <div class="text-center mt-4">
            <a href="index.php?url=shop" class="btn btn-primary btn-lg">
                <i class="fas fa-eye me-2"></i>Voir tous les produits
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Blog Posts -->
<?php if (isset($recentPosts) && !empty($recentPosts)): ?>
<div class="container my-5">
    <div class="text-center mb-5">
        <h2>Derniers articles du blog</h2>
        <p class="text-muted">Restez informé de nos actualités</p>
    </div>
    
    <div class="row g-4">
        <?php foreach ($recentPosts as $post): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    <div class="blog-meta">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($post['author_name']) ?>
                            <i class="fas fa-calendar ms-3 me-1"></i><?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="index.php?url=blog&action=post&id=<?= $post['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Lire l'article
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="index.php?url=blog" class="btn btn-success btn-lg">
            <i class="fas fa-blog me-2"></i>Voir tous les articles
        </a>
    </div>
</div>
<?php endif; ?>

<!-- User Status Section -->
<div class="bg-primary text-white py-5">
    <div class="container text-center">        <?php if (isset($_SESSION['user_id'])): ?>
        <h3>Bonjour <?= htmlspecialchars($_SESSION['pseudo'] ?? 'Utilisateur') ?> !</h3>
        <p class="mb-4">Bienvenue dans votre espace membre. Profitez de toutes nos fonctionnalités.</p>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="index.php?url=user&action=profile" class="btn btn-light w-100">
                            <i class="fas fa-user-edit d-block mb-2"></i>Mon Profil
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="index.php?url=shop&action=orders" class="btn btn-light w-100">
                            <i class="fas fa-list d-block mb-2"></i>Mes Commandes
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="index.php?url=shop&action=cart" class="btn btn-light w-100">
                            <i class="fas fa-shopping-cart d-block mb-2"></i>Mon Panier
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="index.php?url=chat" class="btn btn-light w-100">
                            <i class="fas fa-comments d-block mb-2"></i>Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="mt-4">
            <a href="index.php?url=admin" class="btn btn-warning btn-lg">
                <i class="fas fa-cog me-2"></i>Administration
            </a>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <h3>Rejoignez notre communauté !</h3>
        <p class="mb-4">Inscrivez-vous dès maintenant pour profiter de tous nos services et faire vos achats en ligne.</p>
        <a href="index.php?url=user&action=register" class="btn btn-light btn-lg me-3">
            <i class="fas fa-user-plus me-2"></i>S'inscrire gratuitement
        </a>
        <a href="index.php?url=user&action=login" class="btn btn-outline-light btn-lg">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
        </a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
