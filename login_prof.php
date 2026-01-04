<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM profs WHERE email = ? AND password = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email,$password]);

    if($stmt->rowCount() == 1){
        $prof = $stmt->fetch();
        $_SESSION['prof_id'] = $prof['id'];
        $_SESSION['prof_nom'] = $prof['nom'];
        header("Location: dashboard_prof.php");
        exit;
    } else {
        $message = "Email ou mot de passe incorrect ❌";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Connexion Prof</title>
<meta charset="utf-8">
</head>
<body>
<h2>Connexion Professeur</h2>

<form method="POST">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Se connecter</button>
</form>

<p style="color:red;">
    <?php echo $message; ?>
</p>
</body>
</html>
