<?php
/**
 * Modèle pour la gestion du blog et des commentaires
 */

require_once __DIR__ . '/../config/database.php';

class Blog {
    private $conn;
    private $table = 'blog_posts';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtenir tous les billets de blog
     */
    public function getAllPosts($search = null) {
        try {
            if($search) {
                $query = "SELECT bp.*, u.pseudo as author_pseudo FROM " . $this->table . " bp 
                         INNER JOIN users u ON bp.author_id = u.id 
                         WHERE bp.titre LIKE :search 
                         ORDER BY bp.date_creation DESC";
                $stmt = $this->conn->prepare($query);
                $search_param = '%' . htmlspecialchars($search) . '%';
                $stmt->bindParam(':search', $search_param);
            } else {
                $query = "SELECT bp.*, u.pseudo as author_pseudo FROM " . $this->table . " bp 
                         INNER JOIN users u ON bp.author_id = u.id 
                         ORDER BY bp.date_creation DESC";
                $stmt = $this->conn->prepare($query);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtenir un billet par ID
     */
    public function getPostById($id) {
        try {
            $query = "SELECT bp.*, u.pseudo as author_pseudo FROM " . $this->table . " bp 
                     INNER JOIN users u ON bp.author_id = u.id 
                     WHERE bp.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Créer un nouveau billet (admin seulement)
     */    public function createPost($title, $content, $author_id) {
        try {
            $query = "INSERT INTO " . $this->table . " (titre, contenu, author_id) 
                     VALUES (:titre, :contenu, :author_id)";
            $stmt = $this->conn->prepare($query);
            $title_clean = htmlspecialchars($title);
            $content_clean = htmlspecialchars($content);
            $stmt->bindParam(':titre', $title_clean);
            $stmt->bindParam(':contenu', $content_clean);
            $stmt->bindParam(':author_id', $author_id);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir les commentaires d'un billet
     */
    public function getPostComments($post_id) {
        try {
            $query = "SELECT c.*, u.pseudo FROM comments c 
                     INNER JOIN users u ON c.user_id = u.id 
                     WHERE c.post_id = :post_id 
                     ORDER BY c.date_creation ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }
    
    /**
     * Ajouter un commentaire
     */
    public function addComment($post_id, $user_id, $content) {
        try {
            $query = "INSERT INTO comments (post_id, user_id, contenu) 
                     VALUES (:post_id, :user_id, :contenu)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':contenu', htmlspecialchars($content));
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtenir les derniers commentaires d'un utilisateur (admin)
     */
    public function getUserLastComments($user_id, $limit = 5) {
        try {
            $query = "SELECT c.*, bp.titre as post_title FROM comments c 
                     INNER JOIN blog_posts bp ON c.post_id = bp.id 
                     WHERE c.user_id = :user_id 
                     ORDER BY c.date_creation DESC 
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            return [];
        }
    }    /**
     * Obtenir les articles récents pour la page d'accueil
     */
    public function getRecent($limit = 3) {
        try {
            $query = "SELECT bp.*, u.pseudo as author_name, bp.date_creation as created_at, bp.titre as title, bp.contenu as content FROM " . $this->table . " bp 
                     INNER JOIN users u ON bp.author_id = u.id 
                     ORDER BY bp.date_creation DESC 
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
     * Obtenir un article par son titre
     */
    public function getPostByTitle($title) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE titre = :title LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
