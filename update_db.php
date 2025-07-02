<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=jobboard', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ajouter les colonnes manquantes
    $alterQueries = [
        "ALTER TABLE profils_entreprises ADD COLUMN linkedin VARCHAR(255)",
        "ALTER TABLE profils_entreprises ADD COLUMN contact_nom VARCHAR(100)",
        "ALTER TABLE profils_entreprises ADD COLUMN contact_prenom VARCHAR(100)",
        "ALTER TABLE profils_entreprises ADD COLUMN contact_fonction VARCHAR(100)",
        "ALTER TABLE profils_entreprises ADD COLUMN contact_email VARCHAR(255)",
        "ALTER TABLE profils_entreprises ADD COLUMN contact_telephone VARCHAR(20)"
    ];
    
    foreach ($alterQueries as $query) {
        try {
            $pdo->exec($query);
            echo "Exécuté: $query\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Colonne déjà existante: $query\n";
            } else {
                echo "Erreur: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "Mise à jour de la base de données terminée.\n";
    
} catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage() . "\n";
}
?>