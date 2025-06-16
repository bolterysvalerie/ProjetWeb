<?php

class Order {
    private $pdo;
    
    public function __construct($db = null) {
        if ($db === null) {
            // Si aucune connexion n'est fournie, créer une nouvelle
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $this->pdo = $database->getConnection();
        } else {
            $this->pdo = $db;
        }
    }    // Créer une nouvelle commande
    public function createOrder($userId, $items, $total) {
        try {
            $this->pdo->beginTransaction();
            
            // Vérifier le stock de tous les produits avant de créer la commande
            require_once __DIR__ . '/Product.php';
            $productModel = new Product($this->pdo);
            
            foreach ($items as $item) {
                if (!$productModel->checkStock($item['id'], $item['quantity'])) {
                    $this->pdo->rollBack();
                    throw new Exception('Stock insuffisant pour le produit: ' . $item['nom']);
                }
            }
            
            // Insérer la commande
            $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total, date_commande) VALUES (?, ?, NOW())");
            $stmt->execute([$userId, $total]);
            $orderId = $this->pdo->lastInsertId();            // Insérer les articles de la commande et réduire le stock
            $stmt = $this->pdo->prepare("INSERT INTO order_details (order_id, product_id, product_name, quantite, prix_unitaire) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt->execute([$orderId, $item['id'], $item['nom'], $item['quantity'], $item['prix']]);
                
                // Réduire le stock du produit
                if (!$productModel->updateStock($item['id'], $item['quantity'])) {
                    $this->pdo->rollBack();
                    throw new Exception('Erreur lors de la mise à jour du stock pour le produit: ' . $item['nom']);
                }
            }
            
            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    // Récupérer les commandes d'un utilisateur
    public function getUserOrders($userId) {
        $stmt = $this->pdo->prepare("            SELECT o.*, COUNT(oi.id) as item_count            FROM orders o 
            LEFT JOIN order_details oi ON o.id = oi.order_id 
            WHERE o.user_id = ? 
            GROUP BY o.id 
            ORDER BY o.date_commande DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer une commande par ID
    public function getOrderById($orderId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);    }
    
    // Récupérer les détails d'une commande avec ses articles
    public function getOrderDetails($orderId) {        $stmt = $this->pdo->prepare("
            SELECT 
                oi.product_id,
                oi.prix_unitaire,
                oi.quantite,
                COALESCE(oi.product_name, p.nom, CONCAT('Produit supprimé (ID: ', oi.product_id, ')')) as product_name,
                p.image as product_image
            FROM order_details oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
            ORDER BY oi.id
        ");        $stmt->execute([$orderId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
    
    // Récupérer toutes les commandes (pour admin)
    public function getAllOrders($limit = null, $offset = 0) {
        $sql = "SELECT o.*, u.pseudo as user_pseudo, u.pseudo as username, u.email, COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_details oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.date_commande DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour le statut d'une commande
    public function updateOrderStatus($orderId, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE orders SET statut = ? WHERE id = ?");
            return $stmt->execute([$status, $orderId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Obtenir le nombre total de commandes
    public function getTotalOrders() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
      // Obtenir les commandes récentes (pour admin)
    public function getRecentOrders($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.pseudo as user_pseudo, u.pseudo as username 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.date_commande DESC 
            LIMIT " . intval($limit)
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
      // Obtenir les statistiques des commandes
    public function getOrderStatistics() {
        $stats = [];
        
        // Total des commandes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM orders");
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetchColumn();
        
        /* La table orders n'a pas de colonne status dans notre version
        // Commandes par statut
        $stmt = $this->pdo->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
        $stmt->execute();
        $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        */
        // On initialise quand même la statistique pour éviter les erreurs
        $stats['by_status'] = [];
        
        // Revenus totaux
        $stmt = $this->pdo->prepare("SELECT SUM(total) as total_revenue FROM orders");
        $stmt->execute();
        $stats['total_revenue'] = $stmt->fetchColumn() ?: 0;
        
        // Commandes ce mois
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE MONTH(date_commande) = MONTH(NOW()) AND YEAR(date_commande) = YEAR(NOW())");
        $stmt->execute();
        $stats['this_month'] = $stmt->fetchColumn();
        
        return $stats;
    }
      // Supprimer une commande
    public function deleteOrder($orderId) {
        try {
            $this->pdo->beginTransaction();
            
            // Supprimer les articles de la commande
            $stmt = $this->pdo->prepare("DELETE FROM order_details WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Supprimer la commande
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $result = $stmt->execute([$orderId]);
            
            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;        }
    }
}
