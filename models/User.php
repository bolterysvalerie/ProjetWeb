<?php
/**
 * Modèle pour la gestion des utilisateurs
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register($data) {
        try {
            // Vérification si l'email ou le pseudo existe déjà
            $query = "SELECT id FROM " . $this->table . " WHERE email = :email OR pseudo = :pseudo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':pseudo', $data['pseudo']);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return false; // Utilisateur déjà existant
            }
            
            // Hashage du mot de passe
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO " . $this->table . " 
                     (nom, prenom, adresse, code_postal, date_naissance, email, pseudo, password, image_profil) 
                     VALUES (:nom, :prenom, :adresse, :code_postal, :date_naissance, :email, :pseudo, :password, :image_profil)";
            
            $stmt = $this->conn->prepare($query);
              // Protection contre les injections SQL avec bindParam
            $nom_clean = htmlspecialchars($data['nom']);
            $prenom_clean = htmlspecialchars($data['prenom']);
            $adresse_clean = htmlspecialchars($data['adresse']);
            $code_postal_clean = htmlspecialchars($data['code_postal']);
            $email_clean = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $pseudo_clean = htmlspecialchars($data['pseudo']);
            
            $stmt->bindParam(':nom', $nom_clean);
            $stmt->bindParam(':prenom', $prenom_clean);
            $stmt->bindParam(':adresse', $adresse_clean);
            $stmt->bindParam(':code_postal', $code_postal_clean);
            $stmt->bindParam(':date_naissance', $data['date_naissance']);
            $stmt->bindParam(':email', $email_clean);
            $stmt->bindParam(':pseudo', $pseudo_clean);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':image_profil', $data['image_profil']);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir un utilisateur par son pseudo
     */
    public function getUserByPseudo($pseudo) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE pseudo = :pseudo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':pseudo', htmlspecialchars($pseudo));
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                return $stmt->fetch();
            }
            return false;
            
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Authentification d'un utilisateur
     */    public function login($pseudo, $password) {
        try {
            $query = "SELECT id, pseudo, password, is_admin, is_blocked FROM " . $this->table . " WHERE pseudo = :pseudo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':pseudo', htmlspecialchars($pseudo));
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                $user = $stmt->fetch();
                
                if($user['is_blocked'] == 1) {
                    return 'blocked';
                }
                
                if(password_verify($password, $user['password'])) {
                    // Enregistrer la connexion
                    $this->logConnection($user['id']);
                    return $user;
                }
            }
            return false;
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir les informations d'un utilisateur
     */
    public function getUserById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET adresse = :adresse, code_postal = :code_postal, email = :email";
            
            $params = [
                ':id' => $id,
                ':adresse' => htmlspecialchars($data['adresse']),
                ':code_postal' => htmlspecialchars($data['code_postal']),
                ':email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL)
            ];
            
            // Mise à jour du mot de passe si fourni
            if(!empty($data['password'])) {
                $query .= ", password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Enregistrer une connexion
     */
    private function logConnection($user_id) {
        try {
            $query = "INSERT INTO user_connections (user_id) VALUES (:user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } catch(PDOException $e) {
            // Erreur silencieuse pour les logs
        }
    }
    
    /**
     * Obtenir le nombre de connexions d'un utilisateur
     */
    public function getConnectionCount($user_id, $days = 1) {
        try {
            $query = "SELECT COUNT(*) as count FROM user_connections 
                     WHERE user_id = :user_id AND date_connexion >= DATE_SUB(NOW(), INTERVAL :days DAY)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':days', $days);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['count'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Bloquer/débloquer un utilisateur (admin seulement)
     */
    public function blockUser($user_id, $block = true) {        try {
            $query = "UPDATE " . $this->table . " SET is_blocked = :blocked WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':blocked', $block ? 1 : 0);
            $stmt->bindParam(':id', $user_id);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir tous les utilisateurs (admin seulement)
     */    public function getAllUsers() {
        try {
            $query = "SELECT id, nom, prenom, email, pseudo, date_inscription, is_blocked, is_admin, 
                      CASE WHEN is_admin = 1 THEN 'admin' ELSE 'user' END as role 
                      FROM " . $this->table . " ORDER BY date_inscription DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser($user_id, $data) {
        try {
            $query = "UPDATE " . $this->table . " SET 
                     nom = :nom, 
                     prenom = :prenom, 
                     email = :email, 
                     pseudo = :pseudo,
                     adresse = :adresse,
                     code_postal = :code_postal
                     WHERE id = :id";
              $stmt = $this->conn->prepare($query);            $stmt->bindValue(':nom', htmlspecialchars($data['nom']));
            $stmt->bindValue(':prenom', htmlspecialchars($data['prenom']));
            $stmt->bindValue(':email', filter_var($data['email'], FILTER_SANITIZE_EMAIL));
            $stmt->bindValue(':pseudo', isset($data['pseudo']) ? htmlspecialchars($data['pseudo']) : '');
            $stmt->bindValue(':adresse', htmlspecialchars($data['adresse']));
            $stmt->bindValue(':code_postal', htmlspecialchars($data['code_postal']));
            $stmt->bindParam(':id', $user_id);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($user_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id AND is_admin = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir le nombre total d'utilisateurs
     */
    public function getTotalUsers() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE is_admin = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }
}
?>
