<?php
/**
 * Contrôleur pour la gestion des utilisateurs
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends Controller {
    private $userModel;
      public function __construct() {
        parent::__construct();
        $this->userModel = new User($this->db);
    }
      /**
     * Afficher la page de connexion/inscription
     */
    public function showLogin() {
        if(isLoggedIn()) {
            $this->redirect('index.php', 'Vous êtes déjà connecté.', 'info');
        }
        $this->render('auth/login', [
            'pageTitle' => 'Connexion'
        ]);
    }
    
    /**
     * Traiter la connexion
     */
    public function login() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = sanitizeInput($_POST['pseudo']);
            $password = $_POST['password'];
            
            if(empty($pseudo) || empty($password)) {
                redirect('index.php?page=login', 'Veuillez remplir tous les champs.', 'error');
            }
            
            $user = $this->userModel->login($pseudo, $password);
              if($user === 'blocked') {
                redirect('index.php?page=login', 'Votre compte a été bloqué par un administrateur.', 'error');
            } elseif($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['is_admin'] = $user['is_admin'];
                $_SESSION['user_role'] = $user['is_admin'] ? 'admin' : 'user';
                
                // Force session write before redirect
                session_write_close();
                session_start();
                
                redirect('index.php', 'Connexion réussie !', 'success');
            } else {
                redirect('index.php?page=login', 'Identifiants incorrects.', 'error');
            }
        }
    }
      /**
     * Afficher la page d'inscription
     */
    public function showRegister() {
        if(isLoggedIn()) {
            $this->redirect('index.php', 'Vous êtes déjà connecté.', 'info');
        }
        $this->render('auth/register', [
            'pageTitle' => 'Inscription'
        ]);
    }
    
    /**
     * Traiter l'inscription
     */
    public function register() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeInput($_POST);
            
            // Validation des champs obligatoires
            $required_fields = ['nom', 'prenom', 'adresse', 'code_postal', 'date_naissance', 'email', 'pseudo', 'password'];
            foreach($required_fields as $field) {
                if(empty($data[$field])) {
                    redirect('index.php?page=register', 'Veuillez remplir tous les champs obligatoires.', 'error');
                }
            }
            
            // Validation de l'email
            if(!validateEmail($data['email'])) {
                redirect('index.php?page=register', 'Format d\'email invalide.', 'error');
            }
            
            // Validation de la date de naissance
            if(!validateBirthDate($data['date_naissance'])) {
                redirect('index.php?page=register', 'Date de naissance invalide.', 'error');
            }
            
            // Validation du mot de passe
            if(strlen($data['password']) < 6) {
                redirect('index.php?page=register', 'Le mot de passe doit contenir au moins 6 caractères.', 'error');
            }
            
            // Gestion de l'upload d'image
            $image_profil = null;
            if(isset($_FILES['image_profil']) && $_FILES['image_profil']['error'] === UPLOAD_ERR_OK) {
                $image_profil = uploadImage($_FILES['image_profil'], 'uploads/profiles/');
                if(!$image_profil) {
                    redirect('index.php?page=register', 'Erreur lors de l\'upload de l\'image. Formats acceptés : JPG, GIF, PNG (max 2MB).', 'error');
                }
            }
            
            $data['image_profil'] = $image_profil;
            
            if($this->userModel->register($data)) {
                redirect('index.php?page=login', 'Inscription réussie ! Vous pouvez maintenant vous connecter.', 'success');
            } else {
                redirect('index.php?page=register', 'Erreur lors de l\'inscription. L\'email ou le pseudo existe peut-être déjà.', 'error');
            }
        }
    }
    
    /**
     * Afficher le profil utilisateur
     */
    public function showProfile() {
        requireLogin();
          $user = $this->userModel->getUserById($_SESSION['user_id']);
        if(!$user) {
            $this->redirect('index.php', 'Erreur lors de la récupération du profil.', 'error');
        }
        
        $this->render('user/profile', [
            'pageTitle' => 'Mon Profil',
            'user' => $user
        ]);
    }
    
    /**
     * Mettre à jour le profil
     */
    public function updateProfile() {
        requireLogin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = sanitizeInput($_POST);
            
            // Validation des champs obligatoires
            if(empty($data['adresse']) || empty($data['code_postal']) || empty($data['email'])) {
                redirect('index.php?page=profile', 'Veuillez remplir tous les champs obligatoires.', 'error');
            }
            
            // Validation de l'email
            if(!validateEmail($data['email'])) {
                redirect('index.php?page=profile', 'Format d\'email invalide.', 'error');
            }
            
            // Validation du mot de passe si fourni
            if(!empty($data['password']) && strlen($data['password']) < 6) {
                redirect('index.php?page=profile', 'Le mot de passe doit contenir au moins 6 caractères.', 'error');
            }
            
            if($this->userModel->updateProfile($_SESSION['user_id'], $data)) {
                redirect('index.php?page=profile', 'Profil mis à jour avec succès !', 'success');
            } else {
                redirect('index.php?page=profile', 'Erreur lors de la mise à jour du profil.', 'error');
            }
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        redirect('index.php', 'Vous avez été déconnecté.', 'info');
    }
}
?>
