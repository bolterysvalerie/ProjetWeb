<?php
/**
 * Contrôleur pour la gestion du chat
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Chat.php';

class ChatController extends Controller {
    private $chatModel;
    
    public function __construct() {
        parent::__construct();
        $this->chatModel = new Chat($this->db);
    }
    
    /**
     * Afficher le mini-chat
     */
    public function showChat() {
        $this->requireAuth();
        
        // S'assurer que le pseudo est disponible
        if (!isset($_SESSION['pseudo']) && isset($_SESSION['username'])) {
            $_SESSION['pseudo'] = $_SESSION['username'];
        } elseif (!isset($_SESSION['pseudo'])) {
            // Fallback si ni pseudo ni username n'est disponible
            $_SESSION['pseudo'] = 'Utilisateur_' . $_SESSION['user_id'];
        }
        
        $messages = $this->chatModel->getLastMessages(10);
        
        $this->render('chat/chat', [
            'pageTitle' => 'Mini-Chat',
            'messages' => $messages
        ]);
    }
    
    /**
     * Ajouter un message au chat
     */
    public function addMessage() {
        $this->requireAuth();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $this->sanitize($_POST['message'] ?? '');
            $pseudo = $this->sanitize($_POST['pseudo'] ?? $_SESSION['pseudo'] ?? $_SESSION['username'] ?? 'Utilisateur_' . $_SESSION['user_id']);
            
            if(empty($message)) {
                $this->redirect('index.php?url=chat', 'Le message ne peut pas être vide.', 'error');
                return;
            }
            
            if(empty($pseudo)) {
                $this->redirect('index.php?url=chat', 'Le pseudo ne peut pas être vide.', 'error');
                return;
            }
            
            // Mettre à jour le pseudo dans la session
            $_SESSION['pseudo'] = $pseudo;
            
            // Débogage - Journaliser les tentatives d'ajout de message
            error_log("Tentative d'ajout de message - User ID: {$_SESSION['user_id']}, Pseudo: {$pseudo}, Message: {$message}");
            
            $result = $this->chatModel->addMessage($_SESSION['user_id'], $pseudo, $message);
            
            if($result) {
                // Nettoyer les anciens messages de temps en temps
                if(rand(1, 10) == 1) {
                    $this->chatModel->cleanOldMessages();
                }
                $this->redirect('index.php?url=chat', 'Message envoyé !', 'success');
            } else {
                error_log("Échec de l'ajout de message - Erreur dans le modèle Chat");
                $this->redirect('index.php?url=chat', 'Erreur lors de l\'envoi du message.', 'error');
            }
        } else {
            // Rediriger vers la page du chat si la méthode n'est pas POST
            $this->redirect('index.php?url=chat');
        }
    }
    
    /**
     * Obtenir les messages en AJAX
     */
    public function getMessages() {
        $this->requireAuth();
        
        header('Content-Type: application/json');
        echo json_encode($this->chatModel->getLastMessages(10));
        exit();
    }
}
?>
