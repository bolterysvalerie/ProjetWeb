<?php
/**
 * Contrôleur pour l'administration
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Blog.php';

class AdminController extends Controller {    private $userModel;
    private $productModel;
    private $orderModel;
    private $blogModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User($this->db);
        $this->productModel = new Product($this->db);
        $this->orderModel = new Order($this->db);
        $this->blogModel = new Blog($this->db);
    }
    
    /**
     * Afficher le panneau d'administration
     */
    public function showAdmin() {
        $this->requireAdmin();
        
        $users = $this->userModel->getAllUsers();
        
        $this->render('admin/admin', [
            'pageTitle' => 'Administration',
            'users' => $users
        ]);    }
      /**
     * Consulter le profil d'un utilisateur
     */
    public function viewUserProfile() {
        $this->requireAdmin();
        
        $user_id = (int)($_GET['id'] ?? $_GET['user_id']);
        $user = $this->userModel->getUserById($user_id);
        
        if(!$user) {
            $this->redirect('index.php?url=admin', 'Utilisateur introuvable.', 'error');
            return;
        }
        
        // Obtenir les statistiques de connexion
        $connections_today = $this->userModel->getConnectionCount($user_id, 1);
        $connections_week = $this->userModel->getConnectionCount($user_id, 7);
        
        // Obtenir les commandes de l'utilisateur
        $orders = $this->orderModel->getUserOrders($user_id);
        
        // Obtenir les derniers commentaires
        $comments = $this->blogModel->getUserLastComments($user_id, 5);
        
        $this->render('admin/user_profile', [
            'pageTitle' => 'Profil de ' . htmlspecialchars($user['pseudo']),
            'user' => $user,
            'connections_today' => $connections_today,
            'connections_week' => $connections_week,
            'orders' => $orders,
            'comments' => $comments
        ]);    }
    
    /**
     * Bloquer/débloquer un utilisateur
     */
    public function toggleUserBlock() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $block = isset($_POST['block']) ? true : false;
            
            if($this->userModel->blockUser($user_id, $block)) {
                $action = $block ? 'bloqué' : 'débloqué';
                $this->redirect('index.php?url=admin', "Utilisateur $action avec succès.", 'success');
            } else {
                $this->redirect('index.php?url=admin', 'Erreur lors de la modification du statut utilisateur.', 'error');
            }
        }    }
    
    /**
     * Afficher la gestion des produits
     */
    public function manageProducts() {
        $this->requireAdmin();
        
        $products = $this->productModel->getProductsByCategory();
        $categories = $this->productModel->getCategories();
        
        $this->render('admin/manage_products', [
            'pageTitle' => 'Gestion des produits',
            'products' => $products,
            'categories' => $categories
        ]);    }
    
    /**
     * Ajouter un produit
     */    public function addProduct() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitize($_POST);
            
            if(empty($data['nom']) || empty($data['prix']) || empty($data['category_id'])) {
                $this->redirect('index.php?url=admin&action=products', 'Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }
            
            if(!is_numeric($data['prix']) || $data['prix'] <= 0) {
                $this->redirect('index.php?url=admin&action=products', 'Le prix doit être un nombre positif.', 'error');
                return;
            }
            
            // Validation de l'URL de l'image
            if(!empty($data['image']) && !$this->productModel->validateImageUrl($data['image'])) {
                $this->redirect('index.php?url=admin&action=products', 'L\'URL de l\'image doit être valide et pointer vers une image (jpg, jpeg, png, gif, webp).', 'error');
                return;
            }
            
            $data['stock'] = isset($data['stock']) ? (int)$data['stock'] : 100;
            $data['image'] = !empty($data['image']) ? $data['image'] : null;
            
            if($this->productModel->addProduct($data)) {
                $this->redirect('index.php?url=admin&action=products', 'Produit ajouté avec succès !', 'success');
            } else {
                $this->redirect('index.php?url=admin&action=products', 'Erreur lors de l\'ajout du produit.', 'error');
            }
        }}
    
    /**
     * Supprimer un produit
     */
    public function deleteProduct() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)$_POST['product_id'];
            
            if($this->productModel->deleteProduct($product_id)) {
                $this->redirect('index.php?url=admin&action=products', 'Produit supprimé avec succès !', 'success');
            } else {
                $this->redirect('index.php?url=admin&action=products', 'Erreur lors de la suppression du produit.', 'error');
            }
        }
    }
    
    /**
     * Gérer les utilisateurs
     */
    public function manageUsers() {
        $this->requireAdmin();
        
        $users = $this->userModel->getAllUsers();
        
        $this->render('admin/manage_users', [
            'pageTitle' => 'Gestion des utilisateurs',
            'users' => $users
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition d'un produit
     */
    public function showEditProduct() {
        $this->requireAdmin();
        
        $product_id = (int)$_GET['id'];
        $product = $this->productModel->getProductById($product_id);
        
        if (!$product) {
            $this->redirect('index.php?url=admin&action=products', 'Produit non trouvé.', 'error');
            return;
        }
        
        $categories = $this->productModel->getCategories();
        
        $this->render('admin/edit_product', [
            'pageTitle' => 'Modifier le produit',
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    /**
     * Mettre à jour un produit
     */    public function updateProduct() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitize($_POST);
            $product_id = (int)$data['product_id'];
              if(empty($data['nom']) || empty($data['prix']) || empty($data['category_id'])) {
                $this->redirect('index.php?url=admin&action=edit_product&id=' . $product_id, 'Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }
            
            if(!is_numeric($data['prix']) || $data['prix'] <= 0) {
                $this->redirect('index.php?url=admin&action=edit_product&id=' . $product_id, 'Le prix doit être un nombre positif.', 'error');
                return;
            }
            
            // Validation de l'URL de l'image
            if(!empty($data['image']) && !$this->productModel->validateImageUrl($data['image'])) {
                $this->redirect('index.php?url=admin&action=edit_product&id=' . $product_id, 'L\'URL de l\'image doit être valide et pointer vers une image (jpg, jpeg, png, gif, webp).', 'error');
                return;
            }
            
            $data['stock'] = isset($data['stock']) ? (int)$data['stock'] : 100;
            
            if($this->productModel->updateProduct($product_id, $data)) {
                $this->redirect('index.php?url=admin&action=products', 'Produit mis à jour avec succès !', 'success');
            } else {
                $this->redirect('index.php?url=admin&action=edit_product&id=' . $product_id, 'Erreur lors de la mise à jour du produit.', 'error');
            }
        }
    }
    
    /**
     * Voir les détails d'une commande utilisateur
     */
    public function viewUserOrder() {
        $this->requireAdmin();
        
        $order_id = (int)$_GET['order_id'];
        $order_details = $this->orderModel->getOrderDetails($order_id);
        
        if(empty($order_details)) {
            $this->redirect('index.php?url=admin', 'Commande introuvable.', 'error');
            return;
        }
        
        $this->render('admin/order_details', [
            'pageTitle' => 'Détails de la commande #' . $order_id,
            'order_details' => $order_details,
            'order_id' => $order_id
        ]);
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            $data = $this->sanitize($_POST);
            
            if($this->userModel->updateUser($user_id, $data)) {
                $this->redirect('index.php?url=admin&action=user_profile&user_id=' . $user_id, 'Utilisateur mis à jour avec succès.', 'success');
            } else {
                $this->redirect('index.php?url=admin&action=user_profile&user_id=' . $user_id, 'Erreur lors de la mise à jour.', 'error');
            }
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function deleteUser() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)$_POST['user_id'];
            
            if($this->userModel->deleteUser($user_id)) {
                $this->redirect('index.php?url=admin', 'Utilisateur supprimé avec succès.', 'success');
            } else {
                $this->redirect('index.php?url=admin', 'Erreur lors de la suppression.', 'error');
            }
        }
    }
    
    /**
     * Gérer les commandes
     */
    public function manageOrders() {
        $this->requireAdmin();
        
        $orders = $this->orderModel->getAllOrders();
        
        $this->render('admin/manage_orders', [
            'pageTitle' => 'Gestion des commandes',
            'orders' => $orders
        ]);
    }
    
    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateOrderStatus() {
        $this->requireAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)$_POST['order_id'];
            $status = $this->sanitize($_POST['status']);
            
            if($this->orderModel->updateOrderStatus($order_id, $status)) {
                $this->redirect('index.php?url=admin&action=orders', 'Statut de commande mis à jour.', 'success');
            } else {
                $this->redirect('index.php?url=admin&action=orders', 'Erreur lors de la mise à jour.', 'error');
            }
        }
    }
    
    /**
     * Afficher les statistiques
     */
    public function showStatistics() {
        $this->requireAdmin();
        
        $stats = [
            'total_users' => $this->userModel->getTotalUsers(),
            'total_products' => $this->productModel->getTotalProducts(),
            'total_orders' => $this->orderModel->getTotalOrders(),
            'recent_orders' => $this->orderModel->getRecentOrders(10)
        ];
        
        $this->render('admin/statistics', [
            'pageTitle' => 'Statistiques',
            'stats' => $stats
        ]);
    }
}
?>
