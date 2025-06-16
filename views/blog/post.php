<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="container">
    <div class="blog-container">
        <div class="post-container" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); margin-bottom: 2rem;">    
            <div class="post-header" style="border-bottom: 2px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 2rem;">
                <div style="margin-bottom: 1rem;">
                    <a href="index.php?url=blog" class="btn btn-secondary" style="background: linear-gradient(135deg, #6c757d, #495057); border: none; padding: 0.5rem 1.5rem; border-radius: 0.375rem; color: white; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                        ← Retour au blog
                    </a>
                </div>
                <h1 class="post-title" style="color: #2d3748; font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2;"><?= htmlspecialchars($post['titre']) ?></h1>
                <div class="post-meta" style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; font-size: 1rem; color: #718096;">
                    <span class="post-author" style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                        ✍️ Par <?= htmlspecialchars($post['author_pseudo']) ?>
                    </span>
                    <span class="post-date" style="display: flex; align-items: center; gap: 0.5rem;">
                        📅 <?= date('d/m/Y à H:i', strtotime($post['date_creation'])) ?>
                    </span>
                </div>
            </div>

            <div class="post-content" style="color: #4a5568; line-height: 1.8; font-size: 1.1rem; margin-bottom: 3rem;">
                <?= nl2br(htmlspecialchars($post['contenu'])) ?>
            </div>
        </div>

        <div class="comments-section" id="comments" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
            <h3 style="color: #2d3748; font-size: 1.8rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                💬 Commentaires (<?= count($comments) ?>)
            </h3>            <?php if(isLoggedIn()): ?>
                <div class="add-comment" style="background: #f7fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem; border: 2px dashed #cbd5e0;">
                    <h4 style="color: #2d3748; font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        ✏️ Ajouter un commentaire
                    </h4>                
                    <form action="index.php?url=blog&action=add_comment" method="POST" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <textarea name="content" placeholder="Partagez votre avis, vos questions ou vos remarques..." 
                                      required rows="4" maxlength="1000" 
                                      style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 1rem; font-family: inherit; resize: vertical; transition: border-color 0.3s ease; background: white;"></textarea>
                            <small style="color: #718096; font-size: 0.85rem;">Maximum 1000 caractères</small>
                        </div>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #74b9ff, #0984e3); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            Publier le commentaire
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-prompt" style="background: #fff5f5; border: 2px solid #feb2b2; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem; text-align: center;">                
                    <p style="color: #718096; margin: 0; font-size: 1rem;">
                        <strong>💡 Envie de participer ?</strong><br>
                        <a href="index.php?url=user&action=login" style="color: #74b9ff; text-decoration: none; font-weight: 600;">Connectez-vous</a> ou 
                        <a href="index.php?url=user&action=register" style="color: #74b9ff; text-decoration: none; font-weight: 600;">créez un compte</a> 
                        pour ajouter votre commentaire.
                    </p>
                </div>
            <?php endif; ?>

            <div class="comments-list">
                <?php if(empty($comments)): ?>
                    <div class="no-comments" style="text-align: center; padding: 3rem 2rem; color: #718096; font-size: 1.1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
                        <p style="margin: 0;">Aucun commentaire pour le moment.<br>Soyez le premier à partager votre avis !</p>
                    </div>
                <?php else: ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="comment" style="background: white; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border-left: 4px solid #74b9ff;">
                            <div class="comment-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #74b9ff, #0984e3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.9rem;">
                                        <?= strtoupper(substr($comment['pseudo'], 0, 1)) ?>
                                    </div>
                                    <strong class="comment-author" style="color: #2d3748; font-weight: 600;"><?= htmlspecialchars($comment['pseudo']) ?></strong>
                                </div>
                                <span class="comment-date" style="color: #718096; font-size: 0.9rem;">
                                    📅 <?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?>
                                </span>                            </div>
                            <div class="comment-content" style="color: #4a5568; line-height: 1.6; margin-top: 0.5rem;">
                                <?= nl2br(htmlspecialchars($comment['contenu'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

