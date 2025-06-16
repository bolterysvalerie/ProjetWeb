<?php
/**
 * Configuration de la base de données
 * Variables globales pour la compatibilité avec les anciens modèles
 */

// Variables de connexion globales
$host = 'localhost';
$dbname = 'e_commerce_web';
$username = 'root';
$password = '';

/**
 * Configuration de la base de données
 * Utilise PDO pour la compatibilité avec différents SGBDR
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'e_commerce_web';
    private $username = 'root';
    private $password = '';
    private $conn = null;
    
    /**
     * Connexion à la base de données avec PDO
     */
    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            die();
        }
        
        return $this->conn;
    }
}
?>
