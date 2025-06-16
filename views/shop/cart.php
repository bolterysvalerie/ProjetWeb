<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="cart-container">
    <div class="cart-header">
        <h2>Mon Panier</h2>
    </div>

    <?php if(empty($cart_items)): ?>        <div class="empty-cart">
            <p>Votre panier est vide.</p>
            <a href="index.php?url=shop" class="btn btn-primary">Continuer mes achats</a>
        </div>
    <?php else: ?>        <form action="index.php?url=shop&action=update_cart" method="POST" class="cart-form">
            <div class="cart-items">                <div class="cart-header-row">
                    <div class="item-name">Produit</div>
                    <div class="item-price">Prix unitaire</div>
                    <div class="item-quantity">Quantité</div>
                    <div class="item-stock">Stock</div>
                    <div class="item-total">Total</div>
                    <div class="item-actions">Actions</div>
                </div>                <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-name">
                            <strong><?= htmlspecialchars($item['nom']) ?></strong>
                        </div>
                        <div class="item-price">
                            <?= number_format($item['prix'], 2) ?> €
                        </div>
                        <div class="item-quantity">
                            <?php 
                            $max_quantity = min(10, $item['stock']); 
                            $current_quantity = min($item['quantity'], $max_quantity);
                            ?>
                            <input type="number" name="quantities[<?= $item['id'] ?>]" 
                                   value="<?= $current_quantity ?>" min="1" max="<?= $max_quantity ?>" 
                                   class="quantity-input" <?= $item['stock'] == 0 ? 'disabled' : '' ?>>
                        </div>
                        <div class="item-stock">
                            <span class="stock-info <?= $item['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                <?php if($item['stock'] > 0): ?>
                                    <i class="fas fa-check-circle"></i> <?= $item['stock'] ?> en stock
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i> Rupture
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="item-total">
                            <?= number_format($item['prix'] * $current_quantity, 2) ?> €
                        </div>
                        <div class="item-actions">
                            <button type="submit" name="remove" value="1" class="btn btn-danger btn-sm">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                Supprimer
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="cart-total">
                    <h3>Total : <?= number_format($total, 2) ?> €</h3>
                </div>
                
                <div class="cart-actions">
                    <button type="submit" name="update_quantities" class="btn btn-info">
                        Mettre à jour les quantités
                    </button>                      <a href="index.php?url=shop" class="btn btn-secondary">
                        Continuer mes achats
                    </a>
                    
                    <a href="index.php?url=shop&action=checkout" class="btn btn-success" 
                       onclick="return confirm('Confirmer la commande pour <?= number_format($total, 2) ?> € ?')">
                        Finaliser la commande
                    </a>
                </div>
            </div>
        </form>
          <div class="cart-info">
            <h4>Informations importantes :</h4>
            <ul>
                <li>Maximum 10 articles identiques par commande (selon stock disponible)</li>
                <li>Les quantités sont automatiquement ajustées selon le stock disponible</li>
                <li>Les prix sont indiqués en euros TTC</li>
                <li>La commande sera enregistrée dans votre historique</li>
                <li>Le stock est mis à jour en temps réel lors de la commande</li>
            </ul>
    </div>
    <?php endif; ?>
</div>
