<?php
session_start();

// Configuration des erreurs pour le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Chargement de la configuration de la base de données
require_once 'config/database.php';

// Chargement des modèles
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Blog.php';
require_once 'models/Chat.php';

// Chargement des contrôleurs
require_once 'controllers/Controller.php';
require_once 'controllers/UserController.php';
require_once 'controllers/ShopController.php';
require_once 'controllers/BlogController.php';
require_once 'controllers/ChatController.php';
require_once 'controllers/AdminController.php';

// Récupération de l'URL et de l'action
$url = $_GET['url'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$page = $_GET['page'] ?? null;

// Conversion des anciens liens "page" vers le nouveau système de routage
if ($page) {
    switch ($page) {
        case 'login':
            $url = 'user';
            $action = 'login';
            break;
        case 'register':
            $url = 'user';
            $action = 'register';
            break;
        case 'profile':
            $url = 'user';
            $action = 'profile';            break;
        case 'shop':
            $url = 'shop';
            $action = 'index';
            // Préserver le paramètre category s'il existe
            $_GET['category'] = $_GET['category'] ?? null;
            break;
        case 'blog':
            $url = 'blog';
            $action = 'index';
            break;
        case 'chat':
            $url = 'chat';
            $action = 'index';
            break;
        case 'admin':
            $url = 'admin';
            $action = 'index';
            break;
        case 'order_details':
            $url = 'shop';
            $action = 'order_details';
            break;
    }
}

// Routage principal basé sur l'URL
try {
    switch ($url) {
        case 'home':
        case '':
            $controller = new Controller();
            $controller->home();
            break;

        case 'auth':
        case 'user':
            $controller = new UserController();
            switch ($action) {
                case 'login':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $controller->login();
                    } else {
                        $controller->showLogin();
                    }
                    break;
                case 'register':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $controller->register();
                    } else {
                        $controller->showRegister();
                    }
                    break;
                case 'logout':
                    $controller->logout();
                    break;
                case 'profile':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $controller->updateProfile();
                    } else {
                        $controller->showProfile();
                    }
                    break;
                default:
                    throw new Exception("Action utilisateur non trouvée");
            }
            break;

        case 'shop':
            $controller = new ShopController();
            switch ($action) {
                case 'index':
                case 'products':
                    $controller->showProducts();
                    break;
                case 'add_to_cart':
                    $controller->addToCart();
                    break;
                case 'cart':
                    $controller->showCart();
                    break;
                case 'update_cart':
                    $controller->updateCart();
                    break;
                case 'checkout':
                    $controller->checkout();
                    break;
                case 'orders':
                    $controller->showOrders();
                    break;
                case 'order_details':
                    $controller->showOrderDetails();
                    break;
                default:
                    $controller->showProducts();
            }
            break;

        case 'blog':
            $controller = new BlogController();
            switch ($action) {
                case 'index':
                    $controller->showBlog();
                    break;
                case 'post':
                    $controller->showPost();
                    break;
                case 'create':
                    $controller->createPost();
                    break;
                case 'add_comment':
                    $controller->addComment();
                    break;
                default:
                    $controller->showBlog();
            }
            break;

        case 'chat':
            $controller = new ChatController();
            switch ($action) {
                case 'index':
                    $controller->showChat();
                    break;
                case 'send':
                    $controller->addMessage();
                    break;
                case 'get_messages':
                    $controller->getMessages();
                    break;
                default:
                    $controller->showChat();
            }
            break;

        case 'admin':
            $controller = new AdminController();
            // Vérification des droits d'administrateur
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                header('Location: index.php?url=user&action=login');
                exit;
            }
            
            switch ($action) {
                case 'index':
                    $controller->showAdmin();
                    break;
                case 'users':
                    $controller->manageUsers();
                    break;
                case 'user_profile':
                    $controller->viewUserProfile();
                    break;
                case 'update_user':
                    $controller->updateUser();
                    break;
                case 'delete_user':
                    $controller->deleteUser();
                    break;
                case 'products':
                    $controller->manageProducts();
                    break;
                case 'add_product':
                    $controller->addProduct();
                    break;
                case 'edit_product':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $controller->updateProduct();
                    } else {
                        $controller->showEditProduct();
                    }
                    break;
                case 'delete_product':
                    $controller->deleteProduct();
                    break;
                case 'orders':
                    $controller->manageOrders();
                    break;
                case 'order_details':
                    $controller->viewUserOrder();
                    break;
                case 'update_order_status':
                    $controller->updateOrderStatus();
                    break;
                case 'statistics':
                    $controller->showStatistics();
                    break;
                default:
                    $controller->showAdmin();
            }
            break;

        default:
            // Page 404
            http_response_code(404);
            $controller = new Controller();
            $controller->show404();
            break;
    }
} catch (Exception $e) {
    // Gestion des erreurs
    error_log("Erreur dans le routeur: " . $e->getMessage());
    
    // En production, afficher une page d'erreur générique
    if (ini_get('display_errors')) {
        echo "<h1>Erreur</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        $controller = new Controller();
        $controller->show500();
    }
}
?>