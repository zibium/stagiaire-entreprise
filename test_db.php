<?php
// Test de connexion à la base de données et inscription entreprise
try {
    // Test de connexion
    $pdo = new PDO('mysql:host=localhost;dbname=jobboard', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion à la base de données réussie\n";
    
    // Vérifier si la table utilisateurs existe
    $stmt = $pdo->query('DESCRIBE utilisateurs');
    echo "\nStructure de la table utilisateurs:\n";
    while($row = $stmt->fetch()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
    
    // Test d'insertion d'un utilisateur entreprise
    echo "\nTest d'inscription entreprise...\n";
    
    $email = 'test_entreprise@example.com';
    $password = 'testpass123';
    $role = 'entreprise';
    
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE email = ?');
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo "Email déjà existant, suppression...\n";
        $deleteStmt = $pdo->prepare('DELETE FROM utilisateurs WHERE email = ?');
        $deleteStmt->execute([$email]);
    }
    
    // Insertion
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $insertStmt = $pdo->prepare('INSERT INTO utilisateurs (email, password_hash, role, is_active, email_verified) VALUES (?, ?, ?, 1, 0)');
    $result = $insertStmt->execute([$email, $passwordHash, $role]);
    
    if ($result) {
        $userId = $pdo->lastInsertId();
        echo "Inscription réussie ! ID utilisateur: $userId\n";
        
        // Vérifier l'insertion
        $verifyStmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
        $verifyStmt->execute([$userId]);
        $user = $verifyStmt->fetch();
        
        echo "Utilisateur créé:\n";
        echo "- ID: " . $user['id'] . "\n";
        echo "- Email: " . $user['email'] . "\n";
        echo "- Rôle: " . $user['role'] . "\n";
        echo "- Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . "\n";
        echo "- Email vérifié: " . ($user['email_verified'] ? 'Oui' : 'Non') . "\n";
        
        // Nettoyer
        $deleteStmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
        $deleteStmt->execute([$userId]);
        echo "\nUtilisateur de test supprimé.\n";
        
    } else {
        echo "Erreur lors de l'inscription\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>