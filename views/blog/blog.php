<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="container">
    <div class="blog-container">
        <div class="blog-header">
            <h2>📰 Blog & Actualités</h2>
            <p>Découvrez nos derniers articles, conseils et actualités</p>
        </div>

        <div class="blog-search">        <form action="index.php" method="GET" class="search-form">
            <input type="hidden" name="url" value="blog">
            <input type="text" name="search" placeholder="Rechercher dans les titres..." 
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                   class="search-input">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if(isset($_GET['search'])): ?>
                <a href="index.php?url=blog" class="btn btn-secondary">Effacer</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(isAdmin()): ?>
        <div class="admin-actions">
            <a href="index.php?url=blog&action=create" class="btn btn-success">Créer un nouvel article</a>
        </div>
    <?php endif; ?>

    <div class="blog-posts">
        <?php if(empty($posts)): ?>
            <div class="no-posts">
                <?php if(isset($_GET['search'])): ?>
                    <p>Aucun article trouvé pour "<?= htmlspecialchars($_GET['search']) ?>"</p>
                <?php else: ?>
                    <p>Aucun article publié pour le moment.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <article class="blog-post">
                    <div class="post-header">                        <h3 class="post-title">
                            <a href="index.php?url=blog&action=post&id=<?= $post['id'] ?>">
                                <?= htmlspecialchars($post['titre']) ?>
                            </a>
                        </h3>
                        <div class="post-meta">
                            <span class="post-author">Par <?= htmlspecialchars($post['author_pseudo']) ?></span>
                            <span class="post-date">
                                le <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?>
                            </span>
                        </div>
                    </div>
                      <div class="post-excerpt">
                        <?= substr(htmlspecialchars($post['contenu']), 0, 250) ?>
                        <?php if(strlen($post['contenu']) > 250): ?>...<?php endif; ?>
                    </div>
                      
                    <div class="post-actions">
                        <a href="index.php?url=blog&action=post&id=<?= $post['id'] ?>" class="read-more">
                            Lire la suite
                        </a>
                        <div class="post-stats">
                            <span class="stat-item">
                                💬 <?= $post['comments_count'] ?? '0' ?> commentaires
                            </span>
                            <span class="stat-item">
                                👁️ <?= $post['views_count'] ?? '0' ?> vues
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if(!isLoggedIn()): ?>
        <div class="blog-info" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; margin-top: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); text-align: center;">
            <div class="info-card">
                <h3 style="color: #2d3748; margin-bottom: 1rem;">💬 Rejoignez la conversation</h3>
                <p style="color: #718096; margin-bottom: 1.5rem;">Connectez-vous pour pouvoir commenter et participer aux discussions.</p>
                <div class="auth-buttons" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">                    
                    <a href="index.php?url=user&action=login" class="btn btn-primary" style="background: linear-gradient(135deg, #74b9ff, #0984e3); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; transition: all 0.3s ease;">Se connecter</a>
                    <a href="index.php?url=user&action=register" class="btn btn-secondary" style="background: linear-gradient(135deg, #6c757d, #495057); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; transition: all 0.3s ease;">Créer un compte</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

