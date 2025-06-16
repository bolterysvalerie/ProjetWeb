<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="container">
    <div class="admin-container">
        <div class="admin-header">
            <h2>Panneau d'Administration</h2>
            <p>Gestion complète de votre site e-commerce</p>
        </div>

        <div class="admin-navigation">
            <div class="admin-nav-grid">
                <a href="index.php?url=admin&action=products" class="admin-nav-card">
                    <h3>🛍️ Gestion des Produits</h3>
                    <p>Ajouter, modifier ou supprimer des produits de votre catalogue</p>
                </a>
                
                <a href="index.php?url=admin&action=orders" class="admin-nav-card">
                    <h3>📦 Gestion des Commandes</h3>
                    <p>Suivre et gérer toutes les commandes clients</p>
                </a>
                
                <a href="index.php?url=blog&action=create" class="admin-nav-card">
                    <h3>📝 Créer un Article</h3>
                    <p>Publier un nouvel article sur votre blog</p>
                </a>
                
                <a href="index.php?url=blog" class="admin-nav-card">
                    <h3>� Gérer le Blog</h3>
                    <p>Voir et modérer les articles et commentaires</p>
                </a>
                
                <a href="index.php?url=admin&action=statistics" class="admin-nav-card">
                    <h3>📊 Statistiques</h3>
                    <p>Analyser les performances de votre site</p>
                </a>
                
                <a href="index.php?url=chat" class="admin-nav-card">
                    <h3>� Chat Support</h3>
                    <p>Répondre aux questions des clients</p>
                </a>
            </div>
        </div>

        <div class="users-management">
            <h3>Gestion des Utilisateurs</h3>            
            <?php if(empty($users)): ?>
                <div class="no-posts">
                    <p>Aucun utilisateur enregistré pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="users-table">
                    <div class="table-header">
                        <div class="user-info">Utilisateur</div>
                        <div class="user-email">Email</div>
                        <div class="user-date">Inscription</div>
                        <div class="user-status">Statut</div>
                        <div class="user-actions">Actions</div>
                    </div>

                    <?php foreach($users as $user): ?>
                        <div class="user-row">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                                </div>
                                <div class="user-details">
                                    <h4><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h4>
                                    <p class="user-role">@<?= htmlspecialchars($user['pseudo']) ?></p>
                                </div>
                            </div>
                            <div class="user-email">
                                <?= htmlspecialchars($user['email']) ?>
                            </div>
                            <div class="user-date">
                                <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                            </div>                            <div class="user-status">
                                <?php if(isset($user['role']) && $user['role'] === 'admin'): ?>
                                    <span class="status-badge status-admin">Admin</span>
                                <?php elseif(isset($user['is_blocked']) && $user['is_blocked']): ?>
                                    <span class="status-badge" style="background-color: #fed7d7; color: #c53030;">Bloqué</span>
                                <?php else: ?>
                                    <span class="status-badge status-active">Actif</span>
                                <?php endif; ?>
                            </div>
                            <div class="user-actions">
                                <button class="action-btn btn-view" title="Voir profil" 
                                        onclick="window.location.href='index.php?url=admin&action=user_profile&id=<?= $user['id'] ?>'">
                                    👁️
                                </button>
                                
                                <?php if(!isset($user['role']) || $user['role'] !== 'admin'): ?>
                                    <form action="index.php?action=toggle_user_block" method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <?php if($user['is_blocked']): ?>
                                            <button type="submit" class="action-btn btn-edit" title="Débloquer"
                                                    onclick="return confirm('Débloquer cet utilisateur ?')">
                                                🔓
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="block" value="1">
                                            <button type="submit" class="action-btn btn-delete" title="Bloquer"
                                                    onclick="return confirm('Bloquer cet utilisateur ?')">
                                                🔒
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
                <p class="stat-number"><?= count(array_filter($users, fn($u) => $u['is_blocked'])) ?></p>
            </div>
            <div class="stat-card">
                <h4>Total Utilisateurs</h4>
                <p class="stat-number"><?= count($users) ?></p>
            </div>
        </div>
    </div>
</div>

