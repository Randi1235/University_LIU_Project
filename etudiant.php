<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$pdo = new PDO("mysql:host=localhost;dbname=liu_mr;charset=utf8mb4","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$courses = $pdo->query("SELECT * FROM courses");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Étudiant</title>

<link rel="stylesheet" href="../style.css">

<style>
body{
  background:#f4f6fb;
}

/* NAVBAR */
.navbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:14px 26px;
  background:#0a1f44;
  color:white;
  box-shadow:0 4px 16px rgba(0,0,0,.15);
}
.logo{
  font-weight:700;
  font-size:18px;
}
.logout-btn{
  background:#ef4444;
  padding:8px 14px;
  border-radius:8px;
  text-decoration:none;
  color:white;
  transition:.2s;
}
.logout-btn:hover{ background:#dc2626 }

/* HEADER */
.header{
  max-width:900px;
  margin:34px auto 10px;
  padding:18px 22px;
}
.header h1{ margin-bottom:4px }
.subtitle{ color:#6b7280 }

/* CARDS GRID */
.cards{
  max-width:1000px;
  margin:0 auto 60px;
  padding:12px;
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  gap:18px;
}

/* CARD */
.card{
  background:white;
  border-radius:14px;
  padding:18px 18px 14px;
  box-shadow:0 6px 20px rgba(0,0,0,.08);
  position:relative;
  border:1px solid #eef0f6;
  transition:.25s;
}
.card:hover{
  transform:translateY(-4px);
  box-shadow:0 10px 30px rgba(0,0,0,.12);
}

/* Progress bar */
.progress-bar{
  height:10px;
  border-radius:50px;
  background:#e5e7eb;
  margin:10px 0 12px;
  overflow:hidden;
}
.fill{height:100%; transition:.35s}
.fill.success{background:#22c55e}
.fill.warning{background:#facc15}
.fill.danger{background:#ef4444}

/* metrics */
.metric{
  margin-top:6px;
  color:#374151;
}

/* Status badge (coin supérieur droit) */
.badge{
  position:absolute;
  top:14px;
  right:14px;
  padding:4px 10px;
  border-radius:999px;
  font-size:12px;
  color:#0b1220;
  background:#e5e7eb;
}
.badge.success{ background:#dcfce7 }
.badge.warning{ background:#fef9c3 }
.badge.danger{ background:#fee2e2 }

/* Status text */
.status{
  margin-top:8px;
  font-weight:600;
}
.status.success{ color:#15803d }
.status.warning{ color:#ca8a04 }
.status.danger{ color:#b91c1c }
</style>
</head>

<body>

<div class="navbar">
  <div class="logo">🎓 Portail Étudiant</div>
  <a href="etudiant.php?logout=1" class="logout-btn">Déconnexion</a>
</div>

<header class="header fade-in">
  <h1>Bonjour, <?php echo htmlspecialchars($user['email']); ?> 👋</h1>
  <p class="subtitle">Voici un aperçu clair de vos absences par matière.</p>
</header>

<section class="cards fade-in">

<?php while ($course = $courses->fetch(PDO::FETCH_ASSOC)) {

    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN present = 0 THEN 1 ELSE 0 END) AS absences,
            COUNT(*) AS total
        FROM attendance
        WHERE user_id = ? AND course_id = ?
    ");
    $stmt->execute([$user['id'], $course['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $abs = $data['absences'] ?? 0;
    $total = $data['total'] ?: 10;
    $pourcent = $total > 0 ? round(($abs / $total) * 100) : 0;

    if ($pourcent < 20) $badge = "success";
    elseif ($pourcent < 50) $badge = "warning";
    else $badge = "danger";
?>
    <div class="card">
        <h3><?php echo htmlspecialchars($course['name']); ?></h3>

        <div class="badge <?php echo $badge; ?>"><?php echo $pourcent; ?>%</div>

        <div class="progress-bar">
          <div class="fill <?php echo $badge; ?>" style="width: <?php echo $pourcent; ?>%"></div>
        </div>

        <p class="metric">
          Absences : <b><?php echo $abs; ?>/<?php echo $total; ?></b>
        </p>

        <p class="status <?php echo $badge; ?>">
        <?php
          if ($badge === "success") echo "Très bien 👏";
          elseif ($badge === "warning") echo "À surveiller ⚠️";
          else echo "Attention ❗";
        ?>
        </p>
    </div>

<?php } ?>

</section>

</body>
</html>
