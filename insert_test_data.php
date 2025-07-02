<?php
$config = require_once 'config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    // Insérer un utilisateur entreprise de test
    $stmt = $pdo->prepare("INSERT IGNORE INTO utilisateurs (email, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute(['entreprise@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise']);
    
    $entrepriseUserId = $pdo->lastInsertId();
    if (!$entrepriseUserId) {
        // L'utilisateur existe déjà, récupérer son ID
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute(['entreprise@test.com']);
        $entrepriseUserId = $stmt->fetchColumn();
    }
    
    // Insérer un profil entreprise
    $stmt = $pdo->prepare("INSERT IGNORE INTO profils_entreprises (user_id, nom_entreprise, description, secteur_activite, ville) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $entrepriseUserId,
        'TechCorp Solutions',
        'Entreprise spécialisée dans le développement logiciel',
        'Informatique',
        'Paris'
    ]);
    
    $entrepriseProfilId = $pdo->lastInsertId();
    if (!$entrepriseProfilId) {
        // Le profil existe déjà, récupérer son ID
        $stmt = $pdo->prepare("SELECT id FROM profils_entreprises WHERE user_id = ?");
        $stmt->execute([$entrepriseUserId]);
        $entrepriseProfilId = $stmt->fetchColumn();
    }
    
    // Insérer quelques offres de stage
    $offres = [
        [
            'titre' => 'Stage Développeur Web',
            'description' => 'Rejoignez notre équipe pour développer des applications web modernes avec React et PHP.',
            'duree_mois' => 6,
            'type_contrat_id' => 1,
            'niveau_etude' => 'Bac+3',
            'competences_requises' => 'PHP, JavaScript, React, MySQL',
            'remuneration' => '600€/mois',
            'lieu' => 'Paris',
            'ville' => 'Paris',
            'domaine' => 'Informatique',
            'date_debut' => '2025-03-01',
            'date_fin' => '2025-08-31',
            'date_limite_candidature' => '2025-02-15',
            'statut' => 'approved'
        ],
        [
            'titre' => 'Stage Marketing Digital',
            'description' => 'Participez à nos campagnes marketing digital et apprenez les dernières techniques SEO/SEM.',
            'duree_mois' => 4,
            'type_contrat_id' => 1,
            'niveau_etude' => 'Bac+2',
            'competences_requises' => 'Google Analytics, SEO, Réseaux sociaux',
            'remuneration' => '500€/mois',
            'lieu' => 'Lyon',
            'ville' => 'Lyon',
            'domaine' => 'Marketing',
            'date_debut' => '2025-04-01',
            'date_fin' => '2025-07-31',
            'date_limite_candidature' => '2025-03-15',
            'statut' => 'approved'
        ],
        [
            'titre' => 'Stage Data Analyst',
            'description' => 'Analysez nos données clients et créez des tableaux de bord avec Python et Power BI.',
            'duree_mois' => 5,
            'type_contrat_id' => 1,
            'niveau_etude' => 'Bac+4',
            'competences_requises' => 'Python, SQL, Power BI, Excel',
            'remuneration' => '700€/mois',
            'lieu' => 'Marseille',
            'ville' => 'Marseille',
            'domaine' => 'Data Science',
            'date_debut' => '2025-02-15',
            'date_fin' => '2025-07-15',
            'date_limite_candidature' => '2025-02-01',
            'statut' => 'approved'
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO offres_stage (
            entreprise_id, titre, description, duree_mois, type_contrat_id,
            niveau_etude, competences_requises, remuneration, lieu, ville,
            domaine, date_debut, date_fin, date_limite_candidature, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($offres as $offre) {
        $stmt->execute([
            $entrepriseProfilId,
            $offre['titre'],
            $offre['description'],
            $offre['duree_mois'],
            $offre['type_contrat_id'],
            $offre['niveau_etude'],
            $offre['competences_requises'],
            $offre['remuneration'],
            $offre['lieu'],
            $offre['ville'],
            $offre['domaine'],
            $offre['date_debut'],
            $offre['date_fin'],
            $offre['date_limite_candidature'],
            $offre['statut']
        ]);
    }
    
    echo "Données de test insérées avec succès!\n";
    echo "Nombre d'offres créées: " . count($offres) . "\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>