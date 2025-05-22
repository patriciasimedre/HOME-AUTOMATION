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

    <div id="istoricBec" class="mt-6 text-left w-full max-w-xl mx-auto text-sm bg-white/10 p-4 rounded-xl shadow">
      <h3 class="text-lg font-bold mb-2">Istoric / Status bec</h3>
      <div id="becInfo">Se încarcă...</div>
    </div>

    <!-- ULTIMUL STATUS ACTIVITATE -->
    <div id="ultimStatus" class="mt-6 text-left w-full max-w-xl mx-auto text-sm bg-white/10 p-4 rounded-xl shadow hidden">
      <h3 class="text-lg font-bold mb-2">Ultimul status activitate</h3>
      <div id="ultimStatusContent"></div>
    </div>

    <p id="status" class="text-lg italic mt-2"></p>
  </main>

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

        afiseazaIstoricBec();
      })
      .catch(() => {
        document.getElementById('status').textContent = 'Eroare la trimiterea comenzii';
      });
    }

    function afiseazaIstoricBec() {
      fetch('../backend/get_bec_status.php')
        .then(r => r.json())
        .then(data => {
          const container = document.getElementById("becInfo");
          container.innerHTML = "";

          if (!data.success) {
            container.textContent = "Eroare la încărcarea datelor.";
            return;
          }

          if (data.tip === 'admin') {
            container.innerHTML = `
              Ultimul status: <strong>${data.status.toUpperCase()}</strong><br>
              Dată & oră: ${new Date(data.timestamp).toLocaleString()}<br>
              Utilizator: ${data.utilizator}
            `;
          } else if (data.tip === 'user') {
            if (data.actiuni.length === 0) {
              container.textContent = "Nu ai acționat încă becul.";
              return;
            }

            data.actiuni.forEach(entry => {
              const p = document.createElement("p");
              p.innerHTML = `<strong>${entry.actiune.toUpperCase()}</strong> - ${new Date(entry.timestamp).toLocaleString()}`;
              container.appendChild(p);
            });
          }
        });
    }

    function afiseazaUltimStatusActivitate() {
      fetch('../backend/get_ultim_status.php')
        .then(r => r.json())
        .then(data => {
          const box = document.getElementById("ultimStatus");
          const content = document.getElementById("ultimStatusContent");

          if (!data.success || !data.statusuri || data.statusuri.length === 0) return;

          box.classList.remove("hidden");
          content.innerHTML = "";

          data.statusuri.forEach(entry => {
            const p = document.createElement("p");
            p.innerHTML = `<strong>Userul ${entry.telefon}</strong> a <strong>${entry.actiune}</strong> la ora ${new Date(entry.ora).toLocaleString()}`;
            content.appendChild(p);
          });
        });
    }

    afiseazaIstoricBec();
    afiseazaUltimStatusActivitate();
  </script>

</body>
</html>