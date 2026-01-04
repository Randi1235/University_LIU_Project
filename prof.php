<?php
session_start();

// Vérifier rôle professeur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'prof') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=liu_mr;charset=utf8mb4","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// LOGOUT
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Sauvegarder présences
if (!empty($_POST['course_id']) && !empty($_POST['presence'])) {

    $cid = $_POST['course_id'];

    foreach ($_POST['presence'] as $uid => $p) {

        $check = $pdo->prepare("
            SELECT id FROM attendance
            WHERE user_id = ? AND course_id = ? AND date = CURDATE()
        ");
        $check->execute([$uid, $cid]);

        if (!$check->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO attendance(user_id,course_id,date,present)
                VALUES(?,?,CURDATE(),?)
            ");
            $stmt->execute([$uid, $cid, $p]);
        }
    }

    $msg = "Présence enregistrée ✔️";
}

$courses = $pdo->query("SELECT * FROM courses");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Espace Professeur</title>

<link rel="stylesheet" href="../style.css">

<style>
body{background:#f4f6fb}

/* NAVBAR */
.navbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:14px 26px;
  background:#0a1f44;
  color:white;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
}
.logo{font-weight:700}
.logout-btn{
  background:#ef4444;
  color:white;
  text-decoration:none;
  padding:8px 14px;
  border-radius:10px;
  transition:.2s;
}
.logout-btn:hover{background:#dc2626}

/* PAGE HEADER */
.dashboard{
  max-width:1000px;
  margin:26px auto;
  padding:0 14px 60px;
}
.page-title{margin-bottom:4px}
.subtitle{color:#6b7280;margin-bottom:16px}

/* FEEDBACK */
.alert{
  padding:10px 14px;
  border-radius:10px;
  margin-bottom:14px;
}
.alert.success{
  background:#ecfdf5;
  color:#065f46;
  border:1px solid #a7f3d0;
}

/* GRID */
.cards{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(310px,1fr));
  gap:18px;
}

/* CARD */
.card{
  background:white;
  border-radius:14px;
  padding:16px 18px 14px;
  box-shadow:0 6px 20px rgba(0,0,0,.08);
  border:1px solid #eef0f6;
  transition:.25s;
}
.card:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 30px rgba(0,0,0,.12);
}

.header-line{
  margin-bottom:12px;
  border-bottom:1px solid #e5e7eb;
  padding-bottom:6px;
}

.small-info{color:#6b7280;font-size:.88rem;margin-bottom:14px}

/* TABLE */
.table{
  width:100%;
  border-collapse:collapse;
  margin-bottom:8px;
  font-size:.92rem;
}
.table th{
  text-align:left;
  background:#f3f4f6;
  padding:8px;
  border-radius:6px 6px 0 0;
}
.table td{padding:8px;border-top:1px solid #e5e7eb}

.badge{
  padding:4px 10px;
  border-radius:999px;
  font-size:12px;
}
.badge.abs{background:#fee2e2;color:#7f1d1d}

/* SELECT */
select{
  padding:6px 8px;
  border-radius:8px;
}

/* BUTTON */
.btn-primary{
  background:#0a1f44;
  color:white;
  border:none;
  padding:9px 14px;
  border-radius:10px;
  cursor:pointer;
  transition:.2s;
}
.btn-primary:hover{background:#071633}

.save{margin-top:12px}
</style>

</head>

<body class="dashboard">

<nav class="navbar">
  <h2 class="logo">👨‍🏫 Portail Professeur</h2>
  <a href="prof.php?logout=1" class="logout-btn">Déconnexion</a>
</nav>

<section class="dashboard fade-in">
  
  <h1 class="page-title">Gestion des présences</h1>
  <p class="subtitle">Sélectionnez un cours, puis cochez la présence des étudiants.</p>

  <?php if (!empty($msg)): ?>
      <div class="alert success"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="cards">

  <?php while ($course = $courses->fetch(PDO::FETCH_ASSOC)) { ?>
    
    <div class="card">
        
        <div class="header-line">
          <h3><?php echo htmlspecialchars($course['name']); ?></h3>
        </div>

        <p class="small-info">
          📚 Suivi de présence — <?php echo date("d/m/Y"); ?>
        </p>

        <form method="post" class="attendance-form">
          <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">

          <table class="table">
            <tr>
              <th>Étudiant</th>
              <th>Absences</th>
              <th>Présence</th>
            </tr>

            <?php
              $students = $pdo->query("SELECT * FROM users WHERE role='etudiant'");
              while ($s = $students->fetch(PDO::FETCH_ASSOC)) {

                  $abs_stmt = $pdo->prepare("
                      SELECT COUNT(*) FROM attendance
                      WHERE user_id = ? AND course_id = ? AND present = 0
                  ");
                  $abs_stmt->execute([$s['id'], $course['id']]);
                  $abs = $abs_stmt->fetchColumn();
            ?>

            <tr>
              <td><?php echo htmlspecialchars($s['email']); ?></td>

              <td>
                <span class="badge abs"><?php echo $abs; ?></span>
              </td>

              <td>
                <select name="presence[<?php echo $s['id']; ?>]">
                    <option value="1">Présent</option>
                    <option value="0">Absent</option>
                </select>
              </td>
            </tr>

            <?php } ?>
          </table>

          <button type="submit" class="btn-primary save">
            ✔️ Enregistrer la séance
          </button>
        </form>

    </div>

  <?php } ?>

  </div>

</section>

</body>
</html>
