<?php
session_start();
if (!isset($_SESSION['rol'])) {
  header('Location: login.html');
  exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Control Bec - Camera 1</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(to bottom, #610C9F, #940B92, #DA0C81, #E95793);
    }
    @keyframes glow {
      0% { text-shadow: 0 0 5px #fff, 0 0 10px #ffe933, 0 0 15px #ffdf33; }
      50% { text-shadow: 0 0 10px #fff, 0 0 20px #ffe933, 0 0 30px #ffdf33; }
      100% { text-shadow: 0 0 5px #fff, 0 0 10px #ffe933, 0 0 15px #ffdf33; }
    }
    .bulb-on {
      animation: glow 1.5s infinite;
    }
  </style>
</head>
<body class="min-h-screen text-white font-sans flex flex-col">

  <!-- NAVBAR -->
  <nav class="flex justify-between items-center px-6 py-4 bg-white/10 backdrop-blur-md shadow-lg">
    <a href="javascript:history.back()" class="text-white font-medium hover:underline">&larr; Înapoi</a>
    <div class="flex gap-6">
      <a href="../frontend/activitate/activitate.php" class="text-white hover:underline">📋 Activitate</a>
      <a href="../backend/logout.php" class="text-white hover:underline">🔒 Logout</a>
    </div>
  </nav>

  <!-- CONȚINUT PRINCIPAL -->
  <main class="flex-grow flex flex-col items-center justify-center text-center px-4">
    <h1 class="text-3xl sm:text-4xl font-bold mb-8">Control Bec - Camera 1</h1>

    <div id="bec" class="text-[120px] mb-6 transition duration-500">💡</div>

    <div class="flex gap-6 mb-4">
      <button onclick="controlBec('on')" class="bg-[#E95793] hover:bg-[#DA0C81] px-6 py-3 rounded-xl font-semibold shadow-xl transition-all scale-100 hover:scale-105">Aprinde</button>
      <button onclick="controlBec('off')" class="bg-[#940B92] hover:bg-[#610C9F] px-6 py-3 rounded-xl font-semibold shadow-xl transition-all scale-100 hover:scale-105">Stinge</button>
    </div>

    <p id="status" class="text-lg italic mt-2"></p>
  </main>

  <!-- SUNETE -->
  <audio id="audioOn" src="assets/sounds/on.mp3"></audio>
  <audio id="audioOff" src="assets/sounds/off.mp3"></audio>

  <!-- SCRIPT CONTROL -->
  <script>
    function controlBec(actiune) {
      fetch('../backend/control_bec.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'actiune=' + actiune
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('status').textContent = data.mesaj;

        const bec = document.getElementById('bec');
        bec.className = 'text-[120px] mb-6 transition duration-500 ' +
          (actiune === 'on' ? 'text-yellow-300 bulb-on' : 'opacity-50');

        // Redă sunetul
        const sunet = actiune === 'on'
          ? document.getElementById('audioOn')
          : document.getElementById('audioOff');
        sunet.currentTime = 0;
        sunet.play();
      })
      .catch(() => {
        document.getElementById('status').textContent = 'Eroare la trimiterea comenzii';
      });
    }
  </script>

</body>
</html>