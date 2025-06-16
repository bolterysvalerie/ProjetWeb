<?php
/**
 * Modèle pour la gestion des produits et du panier
 */

require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = 'products';
    
    public function __construct($db = null) {
        if ($db === null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $db;
        }
    }
    
    /**
     * Obtenir tous les produits
     */
    public function getAllProducts() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY nom";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }    /**
     * Obtenir tous les produits par catégorie
     */
    public function getProductsByCategory($category_id = null) {        
        try {
            // Assurer que la valeur est correctement typée
            $category_id = !empty($category_id) ? (int)$category_id : null;
            
            if($category_id && $category_id > 0) {
                $query = "SELECT p.*, c.nom as category FROM " . $this->table . " p 
                         INNER JOIN categories c ON p.category_id = c.id 
                         WHERE p.category_id = ? ORDER BY p.nom";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$category_id]);
            } else {
                $query = "SELECT p.*, c.nom as category FROM " . $this->table . " p 
                         INNER JOIN categories c ON p.category_id = c.id 
                         ORDER BY c.nom, p.nom";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            // Pour le débogage, on peut logger l'erreur
            error_log("Erreur dans getProductsByCategory: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtenir toutes les catégories
     */
    public function getCategories() {
        try {
            $query = "SELECT * FROM categories ORDER BY nom";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtenir un produit par ID
     */    
    public function getProductById($id) {
        try {
            $query = "SELECT p.*, c.nom as category FROM " . $this->table . " p 
                     INNER JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Ajouter un nouveau produit (admin seulement)
     */
    public function addProduct($data) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (nom, description, prix, category_id, stock, image) 
                     VALUES (:nom, :description, :prix, :category_id, :stock, :image)";
              $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':nom', htmlspecialchars($data['nom']));
            $stmt->bindValue(':description', htmlspecialchars($data['description']));
            $stmt->bindParam(':prix', $data['prix']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':stock', $data['stock']);
            $stmt->bindParam(':image', $data['image']);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Supprimer un produit (admin seulement)
     */
    public function deleteProduct($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Mettre à jour un produit
     */
    public function updateProduct($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET nom = :nom, description = :description, prix = :prix, 
                         category_id = :category_id, stock = :stock, image = :image 
                     WHERE id = :id";
              $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindValue(':nom', htmlspecialchars($data['nom']));
            $stmt->bindValue(':description', htmlspecialchars($data['description']));
            $stmt->bindParam(':prix', $data['prix']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':stock', $data['stock']);
            $stmt->bindParam(':image', $data['image']);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
      /**
     * Obtenir les produits en vedette pour la page d'accueil
     */
    public function getFeatured($limit = 6) {
        try {
            $query = "SELECT p.*, c.nom as category FROM " . $this->table . " p 
                     INNER JOIN categories c ON p.category_id = c.id 
                     WHERE p.stock > 0 
                     ORDER BY RAND() 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtenir le nombre total de produits
     */
    public function getTotalProducts() {
        try {
            $query = "SELECT COUNT(*) as total FROM products";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }

    /**
     * Mettre à jour le stock d'un produit
     */
    public function updateStock($product_id, $quantity_to_subtract) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET stock = stock - :quantity 
                     WHERE id = :id AND stock >= :quantity";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity_to_subtract, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;
            
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Vérifier si la quantité demandée est disponible en stock
     */
    public function checkStock($product_id, $quantity) {
        try {
            $query = "SELECT stock FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch();
            if (!$product) {
                return false;
            }
            
            return $product['stock'] >= $quantity;
            
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Obtenir le stock actuel d'un produit
     */
    public function getStock($product_id) {
        try {
            $query = "SELECT stock FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch();
            return $product ? $product['stock'] : 0;
            
        } catch(PDOException $e) {
            return 0;
        }
    }

    /**
     * Valider une URL d'image
     */
    public function validateImageUrl($url) {
        // Vérifier si l'URL est valide
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Vérifier si l'URL pointe vers une image
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        
        return in_array($file_extension, $allowed_extensions);
    }
    
    /**
     * Mettre à jour l'image d'un produit via URL
     */
    public function updateProductImage($product_id, $image_url) {
        try {
            // Valider l'URL de l'image
            if (!empty($image_url) && !$this->validateImageUrl($image_url)) {
                return false;
            }
            
            $query = "UPDATE " . $this->table . " SET image = :image WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':image', $image_url);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir l'URL de l'image d'un produit
     */
    public function getProductImage($product_id) {
        try {
            $query = "SELECT image FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ? $result['image'] : null;
            
        } catch(PDOException $e) {
            return null;
        }
    }
    
    /**
     * Supprimer l'image d'un produit
     */
    public function removeProductImage($product_id) {
        try {
            $query = "UPDATE " . $this->table . " SET image = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
      /**
     * Obtenir une image par défaut si aucune image n'est définie
     */
    public function getDefaultImage() {
        return 'assets/images/default-product.svg';
    }
    
    /**
     * Obtenir l'URL de l'image d'un produit avec fallback
     */
    public function getProductImageWithFallback($product_id) {
        $image = $this->getProductImage($product_id);
        return !empty($image) ? $image : $this->getDefaultImage();
    }
}
?>
