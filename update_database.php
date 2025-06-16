<?php
// Script de mise à jour de la base de données
// À exécuter une seule fois pour corriger la structure

require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "Mise à jour de la base de données...\n";
    
    // Vérifier si la colonne statut existe déjà
    $checkColumn = "SHOW COLUMNS FROM orders LIKE 'statut'";
    $stmt = $pdo->query($checkColumn);
    
    if ($stmt->rowCount() == 0) {
        echo "Ajout de la colonne 'statut' à la table orders...\n";
        
        // Ajouter la colonne statut
        $alterTable = "ALTER TABLE orders 
            ADD COLUMN statut ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee') 
            DEFAULT 'en_attente' 
            AFTER date_commande";
        
        $pdo->exec($alterTable);
        
        // Mettre à jour les commandes existantes
        $updateExisting = "UPDATE orders SET statut = 'en_attente' WHERE statut IS NULL";
        $pdo->exec($updateExisting);
        
        echo "✅ Colonne 'statut' ajoutée avec succès!\n";
    } else {
        echo "✅ La colonne 'statut' existe déjà.\n";
    }
    
    // Vérifier la structure finale
    echo "\nStructure actuelle de la table orders:\n";
    $describe = $pdo->query("DESCRIBE orders");
    while ($row = $describe->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\n✅ Mise à jour terminée!\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
