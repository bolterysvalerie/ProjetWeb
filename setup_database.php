<?php
/**
 * Script d'application du fichier database.sql unifié
 * Peut être utilisé pour créer une nouvelle base ou mettre à jour une existante
 */

require_once 'config/database.php';

echo "<h2>Application du script database.sql</h2>";

try {
    // Connexion avec les variables globales
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Lire le fichier SQL
    $sql = file_get_contents('database.sql');
    
    if ($sql === false) {
        throw new Exception("Impossible de lire le fichier database.sql");
    }
    
    echo "<p>📁 Fichier database.sql lu avec succès.</p>";
    
    // Diviser les requêtes
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0 || strpos($query, '/*') === 0) {
            continue; // Ignorer les commentaires et lignes vides
        }
        
        try {
            $pdo->exec($query);
            $successCount++;
        } catch (PDOException $e) {
            // Ignorer certaines erreurs courantes lors de la mise à jour
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate entry') !== false ||
                strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "<p style='color: orange;'>⚠️ " . $e->getMessage() . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
                $errorCount++;
            }
        }
    }
    
    echo "<h3>Résultat :</h3>";
    echo "<p style='color: green;'>✅ $successCount requêtes exécutées avec succès</p>";
    if ($errorCount > 0) {
        echo "<p style='color: red;'>❌ $errorCount erreurs rencontrées</p>";
    }
    
    // Vérifier que tout fonctionne
    $pdo->exec("USE e_commerce_web");
    
    // Vérifier les tables principales
    $tables = ['users', 'products', 'orders', 'order_details', 'blog_posts', 'chat_messages'];
    echo "<h3>Vérification des tables :</h3>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✅ Table '$table' : " . $result['count'] . " enregistrements</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Problème avec la table '$table': " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p style='color: green; font-size: 1.2em; font-weight: bold;'>🎉 Base de données prête à l'utilisation !</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur générale : " . $e->getMessage() . "</p>";
}
?>
