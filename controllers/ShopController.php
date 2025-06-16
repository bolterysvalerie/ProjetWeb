<?php
/**
 * Contrôleur pour la gestion du e-commerce
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';

class ShopController extends Controller {
    private $productModel;    private $orderModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product($this->db);
        $this->orderModel = new Order($this->db);
    }    /**
     * Afficher la boutique
     */
    public function showProducts() {
        // S'assurer que category_id est correctement typé comme entier
        $category_id = isset($_GET['category']) && !empty($_GET['category']) ? (int)$_GET['category'] : null;
        
        // Vérifier que l'ID est valide (supérieur à zéro)
        if ($category_id !== null && $category_id <= 0) {
            $category_id = null;
        }
        
        $categories = $this->productModel->getCategories();
        $products = $this->productModel->getProductsByCategory($category_id);
        
        $this->render('shop/shop', [
            'pageTitle' => 'Boutique',
            'categories' => $categories,
            'products' => $products
        ]);
    }    /**
     * Ajouter un produit au panier
     */
    public function addToCart() {
        $this->requireAuth();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            
            if($quantity < 1 || $quantity > 10) {
                $this->redirect('index.php?url=shop', 'Quantité invalide (1-10 maximum).', 'error');
                return;
            }
            
            $product = $this->productModel->getProductById($product_id);
            if(!$product) {
                $this->redirect('index.php?url=shop', 'Produit introuvable.', 'error');
                return;
            }

            // Vérifier le stock disponible
            if(!$this->productModel->checkStock($product_id, $quantity)) {
                $current_stock = $this->productModel->getStock($product_id);
                $this->redirect('index.php?url=shop', 'Stock insuffisant. Stock disponible : ' . $current_stock, 'error');
                return;
            }
              // Initialiser le panier en session
            if(!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Calculer la quantité totale qui sera dans le panier après ajout
            $current_cart_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
            $total_quantity = $current_cart_quantity + $quantity;
            
            // Vérifier que la quantité totale ne dépasse pas le stock disponible
            if(!$this->productModel->checkStock($product_id, $total_quantity)) {
                $current_stock = $this->productModel->getStock($product_id);
                $this->redirect('index.php?url=shop', 'Stock insuffisant. Stock disponible : ' . $current_stock . ', déjà dans le panier : ' . $current_cart_quantity, 'error');
                return;
            }

            // Ajouter ou mettre à jour la quantité
            if(isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                
                // Limiter à 10 articles identiques maximum ou au stock disponible
                $max_quantity = min(10, $this->productModel->getStock($product_id));
                if($_SESSION['cart'][$product_id]['quantity'] > $max_quantity) {
                    $_SESSION['cart'][$product_id]['quantity'] = $max_quantity;
                }
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'nom' => $product['nom'],
                    'prix' => $product['prix'],
                    'quantity' => $quantity
                ];
            }
            
            $this->redirect('index.php?url=shop&action=cart', 'Produit ajouté au panier !', 'success');
        }
    }    /**
     * Afficher le panier
     */
    public function showCart() {
        $this->requireAuth();
        
        $cart_items = $_SESSION['cart'] ?? [];
        $total = 0;
        
        // Enrichir les données du panier avec les informations de stock
        foreach($cart_items as $product_id => &$item) {
            $current_stock = $this->productModel->getStock($product_id);
            $item['stock'] = $current_stock;
            
            // Si le stock est insuffisant, ajuster la quantité dans le panier
            if($item['quantity'] > $current_stock) {
                $item['quantity'] = $current_stock;
                $_SESSION['cart'][$product_id]['quantity'] = $current_stock;
            }
            
            $total += $item['prix'] * $item['quantity'];
        }
        
        $this->render('shop/cart', [
            'pageTitle' => 'Mon Panier',
            'cart_items' => $cart_items,
            'total' => $total
        ]);
    }    /**
     * Mettre à jour le panier
     */
    public function updateCart() {
        $this->requireAuth();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['remove'])) {
                // Supprimer un produit du panier
                $product_id = (int)$_POST['product_id'];
                unset($_SESSION['cart'][$product_id]);
                $this->redirect('index.php?url=shop&action=cart', 'Produit retiré du panier.', 'info');
            } elseif(isset($_POST['update_quantities'])) {
                // Mettre à jour les quantités
                $quantities_updated = false;
                $stock_warnings = [];
                
                foreach($_POST['quantities'] as $product_id => $quantity) {
                    $product_id = (int)$product_id;
                    $quantity = (int)$quantity;
                    
                    if($quantity <= 0) {
                        // Supprimer le produit si quantité = 0
                        unset($_SESSION['cart'][$product_id]);
                        $quantities_updated = true;
                    } else {
                        // Vérifier le stock disponible
                        $available_stock = $this->productModel->getStock($product_id);
                        
                        if($available_stock <= 0) {
                            // Produit en rupture de stock
                            unset($_SESSION['cart'][$product_id]);
                            $stock_warnings[] = "Produit retiré car en rupture de stock.";
                        } else {
                            // Ajuster la quantité selon le stock et la limite
                            $max_quantity = min($quantity, 10, $available_stock);
                            
                            if(isset($_SESSION['cart'][$product_id])) {
                                $_SESSION['cart'][$product_id]['quantity'] = $max_quantity;
                            }
                            
                            // Avertir si la quantité a été réduite
                            if($max_quantity < $quantity) {
                                if($max_quantity == $available_stock) {
                                    $stock_warnings[] = "Quantité ajustée selon le stock disponible.";
                                } else {
                                    $stock_warnings[] = "Quantité limitée à 10 articles maximum.";
                                }
                            }
                            
                            $quantities_updated = true;
                        }
                    }
                }
                
                // Rediriger avec le message approprié
                if(!empty($stock_warnings)) {
                    $this->redirect('index.php?url=shop&action=cart', implode(' ', $stock_warnings), 'warning');
                } elseif($quantities_updated) {
                    $this->redirect('index.php?url=shop&action=cart', 'Panier mis à jour avec succès.', 'success');
                } else {
                    $this->redirect('index.php?url=shop&action=cart', 'Aucune modification détectée.', 'info');
                }
            } else {
                // Aucune action valide
                $this->redirect('index.php?url=shop&action=cart', 'Action non reconnue.', 'error');
            }
        } else {
            // Méthode non autorisée
            $this->redirect('index.php?url=shop&action=cart', 'Méthode non autorisée.', 'error');
        }
    }/**
     * Finaliser la commande
     */
    public function checkout() {
        $this->requireAuth();
          if(empty($_SESSION['cart'])) {
            $this->redirect('index.php?url=shop&action=cart', 'Votre panier est vide.', 'error');
            return;
        }
        
        $cart_items = $_SESSION['cart'];
        $total = 0;
        
        // Vérifier le stock de tous les produits dans le panier avant de finaliser
        foreach($cart_items as $item) {            if(!$this->productModel->checkStock($item['id'], $item['quantity'])) {
                $current_stock = $this->productModel->getStock($item['id']);
                $this->redirect('index.php?url=shop&action=cart', 'Stock insuffisant pour le produit "' . $item['nom'] . '". Stock disponible : ' . $current_stock, 'error');
                return;
            }
            $total += $item['prix'] * $item['quantity'];
        }
        
        try {
            $order_id = $this->orderModel->createOrder($_SESSION['user_id'], $cart_items, $total);
            
            if($order_id) {
                $_SESSION['cart'] = []; // Vider le panier
                $this->redirect('index.php?page=orders', 'Commande passée avec succès ! Numéro de commande : #' . $order_id, 'success');            } else {
                $this->redirect('index.php?url=shop&action=cart', 'Erreur lors de la finalisation de la commande.', 'error');
            }
        } catch(Exception $e) {
            $this->redirect('index.php?url=shop&action=cart', 'Erreur : ' . $e->getMessage(), 'error');
        }
    }
      /**
     * Afficher les commandes de l'utilisateur
     */
    public function showOrders() {
        $this->requireAuth();
        
        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);
        
        $this->render('shop/orders', [
            'pageTitle' => 'Mes Commandes',
            'orders' => $orders
        ]);
    }    /**
     * Afficher les détails d'une commande
     */
    public function showOrderDetails() {
        $this->requireAuth();
        
        $order_id = (int)($_GET['id'] ?? $_GET['order_id'] ?? 0);
        
        if($order_id <= 0) {
            $this->redirect('index.php?url=shop&action=orders', 'ID de commande invalide.', 'error');
            return;
        }
        
        // Récupérer les informations de base de la commande
        $order_info = $this->orderModel->getOrderById($order_id);
        
        if(!$order_info) {
            $this->redirect('index.php?url=shop&action=orders', 'Commande introuvable.', 'error');
            return;
        }
        
        // Vérifier que la commande appartient à l'utilisateur (sauf admin)
        if(!isAdmin() && $order_info['user_id'] != $_SESSION['user_id']) {
            $this->redirect('index.php?url=shop&action=orders', 'Vous n\'avez pas l\'autorisation de consulter cette commande.', 'error');
            return;
        }
        
        // Récupérer les détails des articles
        $order_details = $this->orderModel->getOrderDetails($order_id);
        
        $this->render('shop/order_details', [
            'pageTitle' => 'Détails de la commande #' . $order_id,
            'order_details' => $order_details,
            'order_info' => $order_info,
            'order_id' => $order_id
        ]);
    }
}
?>
