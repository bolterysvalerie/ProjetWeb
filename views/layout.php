<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>E-Commerce</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <?php if (isset($additionalHead)) echo $additionalHead; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-store me-2"></i>E-Commerce
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=shop">
                            <i class="fas fa-shopping-bag me-1"></i>Boutique
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=blog">
                            <i class="fas fa-blog me-1"></i>Blog
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=chat">
                            <i class="fas fa-comments me-1"></i>Chat
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Panier -->
                    <li class="nav-item dropdown me-3">                        <a class="nav-link" href="index.php?url=shop&action=cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-warning text-dark cart-count">
                                <?php 
                                    $cartCount = 0;
                                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $item) {
                                            $cartCount += $item['quantity'];
                                        }
                                    }
                                    echo $cartCount;
                                ?>
                            </span>
                        </a>
                    </li>
                      <?php if (isset($_SESSION['user_id'])): ?>                    <!-- Utilisateur connecté -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['pseudo'] ?? 'Utilisateur') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?url=user&action=profile">
                                <i class="fas fa-user-edit me-2"></i>Mon Profil
                            </a></li>
                            <li><a class="dropdown-item" href="index.php?url=shop&action=orders">
                                <i class="fas fa-list me-2"></i>Mes Commandes
                            </a></li>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?url=admin">
                                <i class="fas fa-cog me-2"></i>Administration
                            </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?url=user&action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=user&action=login">
                            <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?url=user&action=register">
                            <i class="fas fa-user-plus me-1"></i>Inscription
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php 
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    endif; ?>

    <!-- Contenu principal -->
    <main class="content-wrapper">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-store me-2"></i>E-Commerce</h5>
                    <p class="text-muted">Votre boutique en ligne de confiance pour tous vos besoins.</p>
                </div>
                <div class="col-md-2">
                    <h6>Navigation</h6>
                    <ul class="list-unstyled text-muted">
                        <li><a href="index.php" class="text-muted text-decoration-none">Accueil</a></li>
                        <li><a href="index.php?url=shop" class="text-muted text-decoration-none">Boutique</a></li>
                        <li><a href="index.php?url=blog" class="text-muted text-decoration-none">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Compte</h6>
                    <ul class="list-unstyled text-muted">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?url=user&action=profile" class="text-muted text-decoration-none">Mon Profil</a></li>
                        <li><a href="index.php?url=shop&action=orders" class="text-muted text-decoration-none">Mes Commandes</a></li>
                        <?php else: ?>
                        <li><a href="index.php?url=user&action=login" class="text-muted text-decoration-none">Connexion</a></li>
                        <li><a href="index.php?url=user&action=register" class="text-muted text-decoration-none">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Contact</h6>
                    <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>contact@e-commerce.com</p>
                    <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>+33 1 23 45 67 89</p>
                    <div class="mt-3">
                        <a href="#" class="text-muted me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">                <div class="col-md-6">
                    <p class="text-white mb-0">&copy; <?= date('Y') ?> E-Commerce. Tous droits réservés.</p>
                </div><div class="col-md-6 text-md-end">
                    <p class="text-white mb-0">Développé par Adrian et Valérie</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Scripts personnalisés -->
    <script src="assets/js/main.js"></script>
    
    <?php if (isset($additionalScripts)) echo $additionalScripts; ?>
</body>
</html>
