<?php
// ================== CONFIG BDD ==================
$host = "localhost";
$dbname = "liu_mr";
$user = "root";
$pass = "";

session_start();

// ================== REDIRECTION SI DEJA CONNECTÉ ==================
if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

if (isset($_SESSION['user'])) {

    if ($_SESSION['user']['role'] === 'etudiant') {
        header("Location: etudiant.php");
        exit;
    }

    if ($_SESSION['user']['role'] === 'prof') {
        header("Location: prof.php");
        exit;
    }
}

// ================== CONNEXION BDD ==================
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

// ================== LOGIN ==================
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1️⃣ — Vérifier ADMIN
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $password === $admin['password']) {

        $_SESSION['admin'] = $admin['email'];

        header("Location: admin.php");
        exit;
    }

    // 2️⃣ — Vérifier UTILISATEUR (étudiant/prof)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {

        $_SESSION['user'] = $user;

        if ($user['role'] === 'etudiant') {
            header("Location: etudiant.php");
            exit;
        }

        if ($user['role'] === 'prof') {
            header("Location: prof.php");
            exit;
        }
    }

    // 3️⃣ — Sinon : erreur
    $erreur = "Email ou mot de passe incorrect";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | LIU-Mr</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family:'Poppins',sans-serif; margin:0; background:#f4f6f9; }

        header { background:#0a1f44; color:white; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; }

        .logo-container{ display:flex; align-items:center; }
        .logo-container img{ height:50px; margin-right:10px; }

        .nav-links{ list-style:none; display:flex; margin:0; padding:0; }
        .nav-links li{ margin-left:20px; }
        .nav-links a{ color:white; text-decoration:none; font-weight:600; }
        .nav-links a:hover{ text-decoration:underline; }

        .login-section{ display:flex; justify-content:center; align-items:center; min-height:70vh; padding:50px 20px; }
        .login-form{ background:white; padding:40px; border-radius:8px; box-shadow:0 5px 15px rgba(0,0,0,.1); width:100%; max-width:400px; }

        .login-form h2{ text-align:center; margin-bottom:25px; color:#0a1f44; }
        .login-form input{ width:100%; padding:12px; margin-bottom:15px; border-radius:4px; border:1px solid #ccc; }
        .login-form button{ width:100%; padding:12px; background:#0a1f44; color:white; border:none; border-radius:4px; font-size:16px; cursor:pointer; }
        .login-form button:hover{ background:#06122b; }

        .error{ color:red; text-align:center; margin-bottom:15px; }
    </style>
</head>

<body>

<header>
    <nav class="navbar">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo LIU-Mr">
            <span>Lebanese International University – Mauritania</span>
        </div>

        <ul class="nav-links">
            <li><a href="../index.html">Accueil</a></li>
            <li><a href="../about.html">À propos</a></li>
            <li><a href="../admissions.html">Admissions</a></li>
            <li><a href="../news.html">Actualités</a></li>
            <li><a href="../guide.html">Guide</a></li>
            <li><a href="../contact.html">Contactez-nous</a></li>
        </ul>
    </nav>
</header>

<section class="login-section">
    <form class="login-form" method="post">
        <h2>Connexion</h2>

        <?php if ($erreur): ?>
            <p class="error"><?= $erreur ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>

        <!-- 👇 Message démo ajouté -->
        <div style="background:#f0f6ff;border:1px solid #bcd3ff;padding:10px;border-radius:4px;font-size:14px;margin-bottom:15px;">
            <strong>Compte démo :</strong><br>
            Email : <code>admin@liu-mr.edu</code><br>
            Mot de passe : <code>admin123</code> <br>
            Email : <code>prof1@mail.com</code><br>
            Mot de passe : <code>1234</code><br>
            Email : <code>etu1@mail.com</code><br>
            Mot de passe : <code>1234</code>
        </div>

        <button type="submit">Se connecter</button>
    </form>
</section>

</body>
</html>
