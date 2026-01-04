<?php
// ================== CONFIG BDD ==================
$host = "localhost";
$dbname = "liu_mr";
$user = "root";
$pass = "";

session_start();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// ================== PROTECTION ==================
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// ================== AJOUT ETUDIANT ==================
if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);

    $sql = "INSERT INTO etudiants (nom, email) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $email]);
}

// ================== SUPPRESSION ETUDIANT ==================
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $sql = "DELETE FROM etudiants WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

// ================== LOGOUT ==================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// ================== AFFICHAGE ==================
$etudiants = $pdo->query("SELECT * FROM etudiants ORDER BY id ASC")->fetchAll();
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | LIU-Mr</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        header {
            background: #0a1f44;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        main {
            padding: 20px;
        }

        h1,
        h3 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
        }

        input {
            padding: 10px;
            margin: 5px;
            width: 250px;
        }

        button {
            padding: 10px 15px;
            background: #0a1f44;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #06122b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #0a1f44;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        a.logout {
            color: #fff;
            background: #c62828;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        a.logout:hover {
            background: #a71d1d;
        }

        a.supprimer {
            color: #c62828;
            text-decoration: none;
            font-weight: bold;
        }

        a.supprimer:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <header>
        <h1>Administration | LIU-Mr</h1>
        <a href="?logout" class="logout">Déconnexion</a>
    </header>

    <main>
        <!-- FORMULAIRE AJOUT ETUDIANT -->
        <form method="post">
            <h3>Ajouter un étudiant</h3>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="email" name="email" placeholder="Email" required>
            <button name="ajouter">Ajouter</button>
        </form>

        <!-- TABLEAU ETUDIANTS -->
        <h3>Liste des étudiants</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php foreach ($etudiants as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['nom']) ?></td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><a class="supprimer" href="?supprimer=<?= $e['id'] ?>"
                            onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- TABLEAU MESSAGES CONTACT -->
        <h3>Messages de contact / inscriptions</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            <?php foreach ($contacts as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['message']) ?></td>
                    <td><?= $c['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    </main>

</body>

</html>