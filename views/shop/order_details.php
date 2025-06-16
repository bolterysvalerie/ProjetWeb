<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="container">
    <div class="order-details-container" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem 0;">
        <div class="order-details-header" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="color: #2d3748; font-size: 2rem; font-weight: 700; margin: 0; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        📋 Détails de la commande #<?= htmlspecialchars($_GET['id'] ?? $order_id) ?>
                    </h2>
                    <?php if(isset($order_info) && !empty($order_info)): ?>
                        <p style="color: #718096; margin: 0.5rem 0 0 0; font-size: 1rem;">
                            📅 Commandé le <?= date('d/m/Y à H:i', strtotime($order_info['date_commande'])) ?>
                            <?php if(isset($order_info['statut'])): ?>
                                - 
                                <?php 
                                $statut = $order_info['statut'];
                                $statusEmoji = ['en_attente' => '⏳', 'confirmee' => '✅', 'expediee' => '📦', 'livree' => '🎉', 'annulee' => '❌'];
                                echo ($statusEmoji[$statut] ?? '📋') . ' ' . ucfirst(str_replace('_', ' ', $statut));
                                ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <a href="index.php?url=shop&action=orders" class="btn btn-secondary" style="background: linear-gradient(135deg, #6c757d, #495057); border: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                    ← Retour aux commandes
                </a>
            </div>
        </div>        <?php if(empty($order_details) || !is_array($order_details)): ?>
            <div class="no-details" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 3rem 2rem; text-align: center; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                <h3 style="color: #2d3748; margin-bottom: 1rem;">Commande vide ou introuvable</h3>
                <p style="color: #718096; margin: 0;">Aucun article trouvé pour cette commande ou vous n'avez pas l'autorisation de la consulter.</p>
                <div style="margin-top: 2rem;">
                    <a href="index.php?url=shop&action=orders" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                        ← Retour aux commandes
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="order-details-content" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); margin-bottom: 2rem;">
                <h3 style="color: #2d3748; font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    🛒 Articles commandés
                </h3>
                
                <div class="details-table" style="background: white; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
                    <div class="table-header" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem 1.5rem; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <div class="product-name">Produit</div>
                        <div class="product-price">Prix unitaire</div>
                        <div class="product-quantity">Quantité</div>
                        <div class="product-total">Total</div>
                    </div>                    <?php 
                    $grand_total = 0;
                    foreach($order_details as $detail): 
                        // Validation des données
                        $prix_unitaire = floatval($detail['prix_unitaire'] ?? 0);
                        $quantite = intval($detail['quantite'] ?? 0);
                        $line_total = $prix_unitaire * $quantite;
                        $grand_total += $line_total;
                        
                        // Protection contre les données invalides
                        if($quantite <= 0) continue;
                    ?>
                        <div class="table-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; align-items: center; transition: background-color 0.2s ease;">
                            <div class="product-name">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if(!empty($detail['product_image'])): ?>
                                        <img src="<?= htmlspecialchars($detail['product_image']) ?>" alt="<?= htmlspecialchars($detail['product_name'] ?? 'Produit') ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.375rem; border: 2px solid #e2e8f0;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 0.375rem; display: none; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.5rem;">
                                            📦
                                        </div>
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.5rem;">
                                            📦
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong style="color: #2d3748; font-weight: 600; font-size: 1rem;"><?= htmlspecialchars($detail['product_name'] ?? 'Produit supprimé') ?></strong>
                                        <?php if(isset($detail['product_id'])): ?>
                                            <div style="font-size: 0.85rem; color: #718096; margin-top: 0.25rem;">ID: <?= $detail['product_id'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>                            <div class="product-price price-display">
                                <?= number_format($prix_unitaire, 2) ?> €
                            </div><div class="product-quantity" style="color: #2d3748; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                                <span class="quantity-badge">
                                    ×<?= $quantite ?>
                                </span>
                            </div>
                            <div class="product-total price-display total-price">
                                <?= number_format($line_total, 2) ?> €
                            </div>
                        </div>
                    <?php endforeach; ?>                </div>                <div class="order-summary" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; border: 2px solid #dee2e6;">
                    <div class="summary-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #2d3748; font-size: 1.1rem;">
                            💰 <span style="font-weight: 600;">Total de la commande :</span>
                        </div>
                        <div class="price-display" style="font-size: 1.8rem; font-weight: 700; color: #667eea; background: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2); border: 2px solid rgba(102, 126, 234, 0.1);">
                            <?= number_format($grand_total, 2) ?> €
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="order-actions" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; padding: 2rem; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); text-align: center;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; font-weight: 600;">Que souhaitez-vous faire ?</h4>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="index.php?url=shop&action=orders" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                    📋 Mes autres commandes
                </a>
                <a href="index.php?url=shop&action=index" class="btn btn-success" style="background: linear-gradient(135deg, #00b894, #00a085); border: none; padding: 0.75rem 2rem; border-radius: 0.5rem; color: white; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                    🛒 Nouvelle commande
                </a>
            </div>
        </div>
    </div>
</div>

