<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="user-profile-admin-container">
    <div class="profile-header">
        <a href="index.php?url=admin" class="btn btn-secondary">← Retour à l'administration</a>
        <h2>Profil de <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
    </div>

    <div class="profile-content">
        <div class="profile-basic-info">
            <h3>Informations Personnelles</h3>
            <?php if($user['image_profil']): ?>
                <img src="uploads/profiles/<?= htmlspecialchars($user['image_profil']) ?>" 
                     alt="Photo de profil" class="profile-photo-admin">
            <?php endif; ?>
            
            <div class="info-grid">
                <div class="info-item">
                    <strong>Nom complet :</strong> 
                    <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                </div>
                <div class="info-item">
                    <strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo']) ?>
                </div>
                <div class="info-item">
                    <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?>
                </div>
                <div class="info-item">
                    <strong>Date de naissance :</strong> 
                    <?= date('d/m/Y', strtotime($user['date_naissance'])) ?>
                </div>
                <div class="info-item">
                    <strong>Adresse :</strong> 
                    <?= htmlspecialchars($user['adresse']) ?>, <?= htmlspecialchars($user['code_postal']) ?>
                </div>
                <div class="info-item">
                    <strong>Date d'inscription :</strong> 
                    <?= date('d/m/Y à H:i', strtotime($user['date_inscription'])) ?>
                </div>
                <div class="info-item">
                    <strong>Statut :</strong> 
                    <?php if($user['is_blocked']): ?>
                        <span class="status-blocked">Bloqué</span>
                    <?php else: ?>
                        <span class="status-active">Actif</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="connection-stats">
            <h3>Statistiques de Connexion</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Aujourd'hui</h4>
                    <p class="stat-number"><?= $connections_today ?></p>
                    <small>connexion(s)</small>
                </div>
                <div class="stat-card">
                    <h4>7 derniers jours</h4>
                    <p class="stat-number"><?= $connections_week ?></p>
                    <small>connexion(s)</small>
                </div>
            </div>
        </div>

        <div class="user-orders">
            <h3>Historique des Commandes</h3>
            <?php if(empty($orders)): ?>
                <p>Aucune commande passée.</p>
            <?php else: ?>
                <div class="orders-summary-stats">
                    <p><strong>Nombre de commandes :</strong> <?= count($orders) ?></p>
                    <p><strong>Total dépensé :</strong> 
                       <?= number_format(array_sum(array_column($orders, 'total')), 2) ?> €</p>
                </div>
                
                <div class="orders-list-admin">
                    <?php foreach(array_slice($orders, 0, 5) as $order): ?>
                        <div class="order-item-admin">
                            <div class="order-info">
                                <strong>Commande #<?= $order['id'] ?></strong>
                                <span><?= date('d/m/Y', strtotime($order['date_commande'])) ?></span>
                            </div>
                            <div class="order-total">
                                <?= number_format($order['total'], 2) ?> €
                            </div>
                            <div class="order-actions">
                                <a href="index.php?page=view_user_order&order_id=<?= $order['id'] ?>" 
                                   class="btn btn-info btn-sm">Détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if(count($orders) > 5): ?>
                        <p><em>... et <?= count($orders) - 5 ?> autre(s) commande(s)</em></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="user-comments">
            <h3>Derniers Commentaires sur le Blog</h3>
            <?php if(empty($comments)): ?>
                <p>Aucun commentaire posté.</p>
            <?php else: ?>
                <div class="comments-list-admin">
                    <?php foreach($comments as $comment): ?>
                        <div class="comment-item-admin">
                            <div class="comment-header-admin">
                                <strong>Sur : "<?= htmlspecialchars($comment['post_title']) ?>"</strong>
                                <span><?= date('d/m/Y à H:i', strtotime($comment['date_creation'])) ?></span>
                            </div>
                            <div class="comment-content-admin">
                                <?= htmlspecialchars(substr($comment['contenu'], 0, 200)) ?>
                                <?php if(strlen($comment['contenu']) > 200): ?>...<?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-actions">
        <form action="index.php?action=toggle_user_block" method="POST" style="display: inline;">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <?php if($user['is_blocked']): ?>
                <button type="submit" class="btn btn-success" 
                        onclick="return confirm('Débloquer cet utilisateur ?')">
                    Débloquer l'utilisateur
                </button>
            <?php else: ?>
                <input type="hidden" name="block" value="1">
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Bloquer cet utilisateur ?')">
                    Bloquer l'utilisateur
                </button>
            <?php endif; ?>
        </form>
        
        <a href="index.php?url=admin" class="btn btn-primary">Retour à l'administration</a>
    </div>
</div>

