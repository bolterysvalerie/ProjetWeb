<?php
// Cette vue utilise maintenant le nouveau système de layout
// Les variables sont directement disponibles depuis le contrôleur
?>

<div class="admin-container">
    <div class="admin-header">
        <h2>Gestion des Commandes</h2>
        <p>Gérez toutes les commandes des utilisateurs</p>
    </div>

    <div class="orders-table-container">
        <?php if(empty($orders)): ?>
            <div class="no-orders">
                <p>Aucune commande trouvée.</p>
            </div>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['user_pseudo'] ?? 'Utilisateur inconnu') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['date_commande'])) ?></td>
                            <td><?= number_format($order['total'], 2) ?> €</td>
                            <td>
                                <?php 
                                $statut = $order['statut'] ?? 'en_attente';
                                $badgeClass = $statut === 'en_attente' ? 'warning' : ($statut === 'expediee' ? 'success' : 'danger');
                                $statutText = ucfirst(str_replace('_', ' ', $statut));
                                ?>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= $statutText ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?url=admin&action=order_details&order_id=<?= $order['id'] ?>" class="btn btn-sm btn-info">Détails</a>
                                  <form method="POST" action="index.php?url=admin&action=update_order_status" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="en_attente" <?= ($order['statut'] ?? 'en_attente') === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                        <option value="expediee" <?= ($order['statut'] ?? 'en_attente') === 'expediee' ? 'selected' : '' ?>>Expédiée</option>
                                        <option value="livree" <?= ($order['statut'] ?? 'en_attente') === 'livree' ? 'selected' : '' ?>>Livrée</option>
                                        <option value="annulee" <?= ($order['statut'] ?? 'en_attente') === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
