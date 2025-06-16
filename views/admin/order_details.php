<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="admin-order-details-container">
    <div class="order-details-header">
        <a href="javascript:history.back()" class="btn btn-secondary">← Retour</a>
        <h2>Détails de la commande #<?= $_GET['order_id'] ?></h2>
    </div>

    <?php if(empty($order_details)): ?>
        <div class="no-details">
            <p>Aucun détail trouvé pour cette commande.</p>
        </div>
    <?php else: ?>
        <div class="order-details-content">
            <div class="details-table-admin">
                <div class="table-header-admin">
                    <div class="product-name">Produit</div>
                    <div class="product-price">Prix unitaire</div>
                    <div class="product-quantity">Quantité</div>
                    <div class="product-total">Total</div>
                </div>

                <?php 
                $grand_total = 0;
                foreach($order_details as $detail): 
                    $line_total = $detail['prix_unitaire'] * $detail['quantite'];
                    $grand_total += $line_total;
                ?>
                    <div class="table-row-admin">
                        <div class="product-name">
                            <strong><?= htmlspecialchars($detail['product_name']) ?></strong>
                        </div>
                        <div class="product-price">
                            <?= number_format($detail['prix_unitaire'], 2) ?> €
                        </div>
                        <div class="product-quantity">
                            <?= $detail['quantite'] ?>
                        </div>
                        <div class="product-total">
                            <?= number_format($line_total, 2) ?> €
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-summary-admin">
                <div class="summary-row-admin">
                    <strong>Total de la commande : <?= number_format($grand_total, 2) ?> €</strong>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="admin-order-actions">
        <a href="javascript:history.back()" class="btn btn-primary">Retour</a>
        <a href="index.php?url=admin" class="btn btn-secondary">Administration</a>
    </div>
</div>

