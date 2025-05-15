<?php
session_start();
if (!isset($_SESSION['telefon'])) {
  header('Location: ../login.html');
  exit;
}

require '../../backend/db.php';

$esteAdmin = $_SESSION['rol'] === 'admin';
$telefon = $_SESSION['telefon'];
$prenume = $_SESSION['prenume'];

try {
  if ($esteAdmin) {
    $stmt = $pdo->query("SELECT * FROM activitate ORDER BY ora DESC");
  } else {
    $stmt = $pdo->prepare("SELECT * FROM activitate WHERE telefon = ? ORDER BY ora DESC");
    $stmt->execute([$telefon]);
  }
  $activitati = $stmt->fetchAll();
} catch (PDOException $e) {
  die("Eroare la interogare: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Activitate Casă</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(to bottom, #610C9F, #940B92, #DA0C81, #E95793);
    }
    .btn-effect:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="text-white min-h-screen px-4 py-6 font-sans">

<!-- Navbar -->
<nav class="bg-white/10 backdrop-blur-md p-4 rounded-xl mb-6 flex flex-wrap items-center justify-center gap-4">
  <strong class="text-lg"><?= htmlspecialchars($esteAdmin ? 'Admin' : 'Utilizator') ?> Panel</strong>
  <a href="<?= $esteAdmin ? '../admin.php' : '../user.php' ?>" class="text-white hover:underline">🏠 Panou</a>
  <a href="../control_lumina.php" class="text-white hover:underline">💡 Control lumină</a>
  <a href="../../backend/logout.php" class="text-white hover:underline">🔒 Logout</a>
</nav>

<!-- Titlu -->
<div class="text-center mb-6">
  <h2 class="text-2xl font-bold">Bun venit, <?= htmlspecialchars($prenume) ?>!</h2>
  <p class="text-white/80"><?= $esteAdmin ? "Poți vedea activitatea tuturor utilizatorilor" : "Poți vedea doar activitatea ta" ?></p>
</div>

<!-- Butoane Intrare/Ieșire -->
<div class="flex flex-col sm:flex-row justify-center gap-6 mb-10">
  <button onclick="inregistreaza('intrat')" class="btn-effect bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-2xl shadow-xl transition">
    🚪 Intră în casă
  </button>
  <button onclick="inregistreaza('iesit')" class="btn-effect bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-3 rounded-2xl shadow-xl transition">
    🚪 Ieși din casă
  </button>
</div>

<!-- Feedback -->
<div id="mesajContainer" class="text-center mb-6"></div>

<!-- Tabel Activitate -->
<div class="max-w-5xl mx-auto overflow-x-auto bg-white/10 rounded-xl shadow-md backdrop-blur-md">
  <table class="min-w-full text-sm text-white text-left">
    <thead class="bg-[#610C9F]">
      <tr>
        <th class="p-3">Telefon</th>
        <th class="p-3">Acțiune</th>
        <th class="p-3">Ora</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($activitati as $row): ?>
        <tr class="border-b border-white/20">
          <td class="p-3"><?= htmlspecialchars($row['telefon']) ?></td>
          <td class="p-3"><?= htmlspecialchars($row['actiune'] === 'intrat' ? 'Intrat în casă' : 'Ieșit din casă') ?></td>
          <td class="p-3"><?= htmlspecialchars($row['ora']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- JS pentru butoane -->
<script>
function inregistreaza(actiune) {
  fetch('inregistrare_actiune.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'actiune=' + actiune
  })
  .then(res => res.json())
  .then(data => {
    const msg = document.createElement('div');
    msg.className = 'mt-4 inline-block px-4 py-2 rounded-lg font-bold ' + 
      (data.success ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800');
    msg.textContent = data.message;

    const container = document.getElementById('mesajContainer');
    container.innerHTML = '';
    container.appendChild(msg);

    setTimeout(() => location.reload(), 1500);
  });
}
</script>

</body>
</html>