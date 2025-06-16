<?php
/**
 * Modèle pour la gestion du mini-chat
 */

require_once __DIR__ . '/../config/database.php';

class Chat {
    private $conn;
    private $table = 'chat_messages';
    
    public function __construct($db = null) {
        if ($db === null) {
            $database = new Database();
            $this->conn = $database->getConnection();
        } else {
            $this->conn = $db;
        }
    }    /**
     * Obtenir les derniers messages du chat
     */
    public function getLastMessages($limit = 10) {
        try {
            // Journaliser la limite pour le débogage
            error_log("Chat::getLastMessages - Récupération des $limit derniers messages");
            
            // Utiliser une requête simple pour récupérer les derniers messages
            $query = "SELECT id, user_id, pseudo, message, date_message 
                     FROM " . $this->table . " 
                     ORDER BY date_message DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            // Récupérer tous les résultats
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Chat::getLastMessages - Nombre de messages récupérés: " . count($results));
            
            if (count($results) > 0) {
                error_log("Chat::getLastMessages - Premier message: " . json_encode($results[0]));
                if (count($results) > 1) {
                    error_log("Chat::getLastMessages - Dernier message: " . json_encode($results[count($results)-1]));
                }
            }
            
            // Inverser les résultats pour avoir du plus ancien au plus récent
            return array_reverse($results);
            
        } catch(PDOException $e) {
            error_log("Chat::getLastMessages - Erreur PDO: " . $e->getMessage());
            return [];
        } catch(Exception $e) {
            error_log("Chat::getLastMessages - Exception: " . $e->getMessage());
            return [];
        }
    }/**
     * Ajouter un message au chat
     */
    public function addMessage($user_id, $pseudo, $message) {
        try {
            // Vérifier les paramètres
            if (empty($user_id) || empty($pseudo) || empty($message)) {
                error_log("Chat::addMessage - Paramètres invalides: user_id={$user_id}, pseudo={$pseudo}, message=" . (empty($message) ? "vide" : "présent"));
                return false;
            }
            
            // Limiter la longueur du message à 500 caractères
            $message = substr(htmlspecialchars($message), 0, 500);
            $pseudo = htmlspecialchars($pseudo);
            
            // Convertir user_id en entier
            $user_id = (int)$user_id;
            
            // Vérifier si l'utilisateur existe
            $checkUserQuery = "SELECT COUNT(*) as count FROM users WHERE id = :user_id";
            $checkStmt = $this->conn->prepare($checkUserQuery);
            $checkStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $userExists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
            
            if (!$userExists) {
                error_log("Chat::addMessage - L'utilisateur avec l'ID {$user_id} n'existe pas");
                return false;
            }
            
            error_log("Chat::addMessage - Tentative d'insertion - user_id={$user_id}, pseudo={$pseudo}");
            
            $query = "INSERT INTO " . $this->table . " (user_id, pseudo, message) 
                     VALUES (:user_id, :pseudo, :message)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            
            $result = $stmt->execute();
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Chat::addMessage - Erreur SQL: " . json_encode($errorInfo));
            } else {
                error_log("Chat::addMessage - Message inséré avec succès");
                
                // Vérifier immédiatement le nombre de messages dans la table
                try {
                    $countQuery = "SELECT COUNT(*) as count FROM " . $this->table;
                    $countStmt = $this->conn->query($countQuery);
                    $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
                    error_log("Chat::addMessage - Nombre total de messages après insertion: {$count}");
                } catch (Exception $e) {
                    error_log("Chat::addMessage - Erreur lors du comptage: " . $e->getMessage());
                }
            }
            
            return $result;
            
        } catch(PDOException $e) {
            error_log("Chat::addMessage - Exception PDO: " . $e->getMessage());
            return false;
        } catch(Exception $e) {
            error_log("Chat::addMessage - Exception générale: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Nettoyer les anciens messages (garder seulement les 50 derniers)
     */
    public function cleanOldMessages() {
        try {
            $query = "DELETE FROM " . $this->table . " 
                     WHERE id NOT IN (
                         SELECT id FROM (
                             SELECT id FROM " . $this->table . " 
                             ORDER BY date_message DESC 
                             LIMIT 50
                         ) as temp
                     )";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
