<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'user') {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panou Utilizator</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(to bottom, #610C9F, #940B92, #DA0C81, #E95793);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-start px-4 py-6 text-white">

<nav class="bg-white/10 backdrop-blur-md p-4 rounded-xl mb-6 flex flex-wrap items-center justify-center gap-4">
  <strong class="text-lg">Panou Utilizator</strong>
  <a href="control_lumina.php" class="text-white hover:underline">💡 Control lumină</a>
  <a href="activitate/activitate.php">📋 Activitate</a>
  <a href="../backend/logout.php" class="text-white hover:underline">🔒 Logout</a>
</nav>

<h2 class="text-2xl font-bold mb-4">Bine ai venit, <span id="prenume"></span>!</h2>

<script>
  fetch('../backend/check_session.php')
    .then(r => r.json())
    .then(data => {
      if (!data.success || data.rol !== 'user') {
        window.location.href = 'login.html';
      } else {
        document.getElementById('prenume').textContent = data.prenume;
      }
    })
    .catch(() => window.location.href = 'login.html');
</script>

</body>
</html>