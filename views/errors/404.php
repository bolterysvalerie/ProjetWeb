<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="error-page">
                <i class="fas fa-exclamation-triangle display-1 text-warning mb-4"></i>
                <h1 class="display-4 mb-3">404</h1>
                <h2 class="mb-4">Page non trouvée</h2>
                <p class="lead text-muted mb-5">
                    Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
                </p>
                <div class="d-grid gap-2 d-md-block">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>Retour à l'accueil
                    </a>
                    <a href="index.php?url=shop" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Voir la boutique
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
