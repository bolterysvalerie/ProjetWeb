<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="admin-container">
    <div class="admin-header">
        <h2>Statistiques</h2>
        <p>Vue d'ensemble de l'activité du site</p>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total_users'] ?></h3>
                    <p>Utilisateurs</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total_products'] ?></h3>
                    <p>Produits</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total_orders'] ?></h3>
                    <p>Commandes</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3><?= count($stats['recent_orders']) ?></h3>
                    <p>Commandes récentes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-orders-section mt-4">
        <h3>Commandes récentes</h3>
        <?php if(empty($stats['recent_orders'])): ?>
            <p>Aucune commande récente.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['recent_orders'] as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['user_pseudo']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['date_commande'])) ?></td>
                            <td><?= number_format($order['total'], 2) ?> €</td>                            <td>
                                <?php 
                                $statut = $order['statut'] ?? 'en_attente';
                                $badgeClass = '';
                                $badgeStyle = '';
                                
                                switch($statut) {
                                    case 'en_attente':
                                        $badgeClass = 'badge-warning';
                                        $badgeStyle = 'background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7;';
                                        break;
                                    case 'confirmee':
                                        $badgeClass = 'badge-info';
                                        $badgeStyle = 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;';
                                        break;
                                    case 'expediee':
                                        $badgeClass = 'badge-success';
                                        $badgeStyle = 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;';
                                        break;
                                    case 'livree':
                                        $badgeClass = 'badge-success';
                                        $badgeStyle = 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;';
                                        break;
                                    case 'annulee':
                                        $badgeClass = 'badge-danger';
                                        $badgeStyle = 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;';
                                        break;
                                    default:
                                        $badgeClass = 'badge-secondary';
                                        $badgeStyle = 'background-color: #e2e3e5; color: #383d41; border: 1px solid #d6d8db;';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>" style="<?= $badgeStyle ?>">
                                    <?= ucfirst(str_replace('_', ' ', $statut)) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
