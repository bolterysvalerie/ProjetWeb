<?php
/**
 * Contrôleur de base avec méthodes communes
 */
class Controller {
    protected $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
      /**
     * Afficher une vue avec le layout
     */
    protected function render($view, $data = []) {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Capturer le contenu de la vue
        ob_start();
        require_once "views/{$view}.php";
        $content = ob_get_clean();
        
        // Afficher avec le layout
        require_once 'views/layout.php';
    }
    
    /**
     * Méthode layout pour compatibilité avec les templates
     */
    protected function layout($layoutName, $data = []) {
        // Extraire les données pour les rendre disponibles dans le layout
        extract($data);
        
        // Inclure le layout spécifié
        require_once "views/{$layoutName}.php";
    }
    
    /**
     * Définir un message flash
     */
    protected function setFlash($message, $type = 'info') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    /**
     * Redirection avec message flash optionnel
     */
    protected function redirect($url, $message = null, $type = 'info') {
        if ($message) {
            $this->setFlash($message, $type);
        }
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('index.php?url=user&action=login', 'Vous devez être connecté pour accéder à cette page.', 'warning');
        }
    }
    
    /**
     * Vérifier si l'utilisateur est admin
     */
    protected function requireAdmin() {
        $this->requireAuth();
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('index.php', 'Accès refusé. Vous devez être administrateur.', 'danger');
        }
    }
    
    /**
     * Nettoyer les données d'entrée
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valider l'email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Gérer l'upload de fichiers sécurisé
     */
    protected function handleFileUpload($file, $uploadDir = 'uploads/', $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Vérifier la taille (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }
        
        // Créer le dossier si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . strtolower($extension);
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return false;
    }
    
    /**
     * Réponse JSON pour les requêtes AJAX
     */
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Page d'accueil
     */
    public function home() {
        // Récupérer quelques produits en vedette
        $productModel = new Product();
        $featuredProducts = $productModel->getFeatured(6);
        
        // Récupérer les derniers articles de blog
        $blogModel = new Blog();
        $recentPosts = $blogModel->getRecent(3);
        
        $this->render('home', [
            'pageTitle' => 'Accueil',
            'featuredProducts' => $featuredProducts,
            'recentPosts' => $recentPosts
        ]);
    }
    
    /**
     * Page 404
     */
    public function show404() {
        http_response_code(404);
        $this->render('errors/404', [
            'pageTitle' => 'Page non trouvée'
        ]);
    }
    
    /**
     * Page 500
     */
    public function show500() {
        http_response_code(500);
        $this->render('errors/500', [
            'pageTitle' => 'Erreur serveur'
        ]);
    }
}

/**
 * Fonctions helper globales (pour rétrocompatibilité)
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Rediriger vers une page avec message
 */
function redirect($page, $message = null, $type = 'info') {
    if($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $page");
    exit();
}

/**
 * Afficher et supprimer les messages flash
 */
function getFlashMessage() {
    if(isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Vérifier l'accès aux pages protégées
 */
function requireLogin() {
    if(!isLoggedIn()) {
        redirect('index.php?page=login', 'Vous devez être connecté pour accéder à cette page.', 'error');
    }
}

/**
 * Vérifier l'accès administrateur
 */
function requireAdmin() {
    if(!isLoggedIn() || !isAdmin()) {
        redirect('index.php', 'Accès refusé. Vous devez être administrateur.', 'error');
    }
}

/**
 * Sécuriser l'upload de fichiers
 */
function uploadImage($file, $upload_dir = 'uploads/') {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if(!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if($file['size'] > $max_size) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if(!in_array($mime_type, $allowed_types)) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = $upload_dir . $filename;
    
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if(move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $filename;
    }
    
    return false;
}

/**
 * Nettoyer et valider les données d'entrée
 */
function sanitizeInput($data) {
    if(is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Valider l'email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valider la date de naissance
 */
function validateBirthDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date && $d < new DateTime();
}
?>
