<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="orders-container">
    <div class="orders-header">
        <h2>Mes Commandes</h2>
        <p>Historique de vos achats classés par date</p>
    </div>

    <?php if(empty($orders)): ?>
        <div class="no-orders">
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="index.php?page=shop" class="btn btn-primary">Découvrir nos produits</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Commande #<?= $order['id'] ?></h3>
                            <p class="order-date">
                                <?= date('d/m/Y à H:i', strtotime($order['date_commande'])) ?>
                            </p>
                        </div>
                        <div class="order-total">
                            <strong><?= number_format($order['total'], 2) ?> €</strong>
                        </div>                    </div>
                    
                    <div class="order-actions">
                        <a href="index.php?url=shop&action=order_details&id=<?= $order['id'] ?>" 
                           class="btn btn-info">Voir les détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="orders-summary">
            <div class="summary-card">
                <h3>Résumé</h3>
                <p><strong>Nombre de commandes :</strong> <?= count($orders) ?></p>
                <p><strong>Total dépensé :</strong> 
                   <?= number_format(array_sum(array_column($orders, 'total')), 2) ?> €
                </p>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="orders-navigation">
        <a href="index.php?page=shop" class="btn btn-primary">Continuer mes achats</a>
        <a href="index.php?page=profile" class="btn btn-secondary">Mon profil</a>
    </div>
</div>

