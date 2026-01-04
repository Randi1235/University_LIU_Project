<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "liu_mr";
$user = "root";      // adapte si besoin
$pass = "";          // adapte si besoin

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Identifiants admin de test
$email = "admin@liu-mr.edu";
$password = "admin123";

// Création ou mise à jour de l'admin
$sql = "
    INSERT INTO admins (email, password)
    VALUES (:email, :password)
    ON DUPLICATE KEY UPDATE password = :password
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':password' => $password
]);

echo "✅ Admin créé ou mis à jour avec succès.<br>";
echo "Email : <b>$email</b><br>";
echo "Mot de passe : <b>$password</b>";
