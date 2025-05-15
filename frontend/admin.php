<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: login.html');
  exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panou Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(to bottom, #610C9F, #940B92, #DA0C81, #E95793);
    }
  </style>
</head>
<body class="min-h-screen text-white px-4 py-6 font-sans">

<nav class="bg-white/10 backdrop-blur-md p-4 rounded-xl mb-6 flex flex-wrap items-center justify-center gap-4">
  <strong class="text-lg">Admin Panel</strong>
  <a href="admin.php" class="text-white hover:underline">Adaugă utilizator</a>
  <a href="control_lumina.php" class="text-white hover:underline">💡 Control Lumina</a>
  <a href="activitate/activitate.php">📋 Activitate</a>
  <a href="../backend/logout.php" class="text-white hover:underline">🔒 Logout</a>
</nav>

<div class="text-center">
  <h2 class="text-2xl font-bold">Bine ai venit, <span id="prenume"></span>!</h2>
</div>

<div id="mesajContainer" class="max-w-xl mx-auto mt-6"></div>

<!-- FORMULAR ADĂUGARE -->
<div class="max-w-4xl mx-auto bg-white/10 p-6 rounded-xl mt-8 backdrop-blur-xl">
  <h3 class="text-xl font-bold mb-4">Adaugă utilizator</h3>
  <form id="adaugaForm" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <input type="text" name="nume" placeholder="Nume" required class="px-4 py-3 rounded-lg text-black text-sm">
    <input type="text" name="prenume" placeholder="Prenume" required class="px-4 py-3 rounded-lg text-black text-sm">
    <input type="text" name="cnp" placeholder="CNP (13 cifre)" required maxlength="13" class="px-4 py-3 rounded-lg text-black text-sm">
    <input type="text" name="telefon" placeholder="Telefon (07XXXXXXXX)" required class="px-4 py-3 rounded-lg text-black text-sm">
    <input type="text" name="parola" placeholder="Parolă (min 6 caractere)" required class="px-4 py-3 rounded-lg text-black text-sm">
    <input type="text" name="codBluetooth" placeholder="Cod Bluetooth" class="px-4 py-3 rounded-lg text-black text-sm">
    <select name="rol" class="px-4 py-3 rounded-lg text-black text-sm">
      <option value="user">user</option>
      <option value="admin">admin</option>
    </select>
    <button type="submit" class="bg-[#DA0C81] hover:bg-[#940B92] transition-colors py-3 px-6 rounded-xl font-bold col-span-1 sm:col-span-2">Adaugă</button>
  </form>
</div>

<!-- TABEL UTILIZATORI -->
<div class="max-w-6xl mx-auto mt-10">
  <h3 class="text-xl font-bold mb-4">Utilizatori existenți</h3>
  <div class="relative overflow-x-auto rounded-lg shadow-lg border border-white/20">
    <div id="scrollArrow" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 text-white text-sm animate-bounce font-bold bg-[#DA0C81] px-2 py-1 rounded shadow">
      ⬅ Scroll
    </div>
    <table class="min-w-full text-sm text-black bg-white/90 rounded-xl">
      <thead class="bg-[#610C9F] text-white">
        <tr>
          <th class="p-2 whitespace-nowrap">ID</th>
          <th class="p-2 whitespace-nowrap">Nume</th>
          <th class="p-2 whitespace-nowrap">Prenume</th>
          <th class="p-2 whitespace-nowrap">CNP</th>
          <th class="p-2 whitespace-nowrap">Telefon</th>
          <th class="p-2 whitespace-nowrap">Bluetooth</th>
          <th class="p-2 whitespace-nowrap">Rol</th>
          <th class="p-2 whitespace-nowrap">Parolă nouă</th>
          <th class="p-2 whitespace-nowrap">Salvează</th>
          <th class="p-2 whitespace-nowrap">Șterge</th>
        </tr>
      </thead>
      <tbody id="tabelUtilizatori"></tbody>
    </table>
  </div>
</div>

<script>
fetch('../backend/check_session.php')
  .then(r => r.json())
  .then(data => {
    if (!data.success || data.rol !== 'admin') {
      window.location.href = 'login.html';
    } else {
      document.getElementById('prenume').textContent = data.prenume;
      incarcaUtilizatori();
    }
  });

const mesajContainer = document.getElementById("mesajContainer");
const form = document.getElementById("adaugaForm");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  mesajContainer.innerHTML = "";

  const formData = new FormData(form);
  const payload = new URLSearchParams(formData);

  const response = await fetch("../backend/adauga_utilizator.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: payload
  });

  const result = await response.json();
  afiseazaMesaj(result);
  if (result.success) {
    form.reset();
    incarcaUtilizatori();
  }
});

function afiseazaMesaj(result) {
  mesajContainer.innerHTML = "";
  const div = document.createElement("div");
  div.className = (result.success ? "bg-green-200 text-green-900" : "bg-red-200 text-red-800") +
                  " rounded-xl px-4 py-2 mt-4 text-sm flex items-center justify-between";
  div.innerHTML = result.message;

  const closeBtn = document.createElement("span");
  closeBtn.textContent = "✖";
  closeBtn.className = "ml-4 cursor-pointer font-bold";
  closeBtn.onclick = () => div.remove();

  div.appendChild(closeBtn);
  mesajContainer.appendChild(div);
}

async function incarcaUtilizatori() {
  const response = await fetch("../backend/lista_utilizatori.php");
  const result = await response.json();
  const tbody = document.getElementById("tabelUtilizatori");
  tbody.innerHTML = "";

  if (result.success) {
    result.utilizatori.forEach(user => {
      const tr = document.createElement("tr");
      tr.className = "border-b text-xs sm:text-sm";
      tr.innerHTML = `
        <td class="p-2 min-w-[60px]">${user.id}</td>
        <td class="p-2 min-w-[120px]"><input type="text" value="${user.nume}" class="px-2 py-1 rounded w-full text-sm" data-camp="nume"></td>
        <td class="p-2 min-w-[120px]"><input type="text" value="${user.prenume}" class="px-2 py-1 rounded w-full text-sm" data-camp="prenume"></td>
        <td class="p-2 min-w-[130px]"><input type="text" value="${user.cnp}" class="px-2 py-1 rounded w-full text-sm" data-camp="cnp"></td>
        <td class="p-2 min-w-[130px]"><input type="text" value="${user.telefon}" class="px-2 py-1 rounded w-full text-sm" data-camp="telefon"></td>
        <td class="p-2 min-w-[130px]"><input type="text" value="${user.codBluetooth}" class="px-2 py-1 rounded w-full text-sm" data-camp="codBluetooth"></td>
        <td class="p-2 min-w-[110px]">
          <select class="px-2 py-1 rounded w-full text-sm" data-camp="rol">
            <option value="user" ${user.rol === 'user' ? 'selected' : ''}>user</option>
            <option value="admin" ${user.rol === 'admin' ? 'selected' : ''}>admin</option>
          </select>
        </td>
        <td class="p-2 min-w-[130px]"><input type="password" placeholder="Nouă parolă" class="px-2 py-1 rounded w-full text-sm" data-camp="parola"></td>
        <td class="p-2 min-w-[110px]">
          <button onclick="salveazaUtilizator(${user.id}, this.parentNode.parentNode)" class="bg-[#DA0C81] hover:bg-[#940B92] text-white rounded px-4 py-1 text-sm">Salvează</button>
        </td>
        <td class="p-2 min-w-[110px]">
          <button onclick="stergeUtilizator(${user.id})" class="bg-red-500 hover:bg-red-700 text-white rounded px-4 py-1 text-sm">🗑️</button>
        </td>`;
      tbody.appendChild(tr);
    });
  }
}

async function salveazaUtilizator(id, row) {
  const date = { id };
  row.querySelectorAll('[data-camp]').forEach(input => {
    date[input.dataset.camp] = input.value;
  });

  const response = await fetch("../backend/salveaza_modificari.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(date)
  });

  const result = await response.json();
  afiseazaMesaj(result);
}

async function stergeUtilizator(id) {
  if (!confirm("Sigur vrei să ștergi acest utilizator?")) return;

  const response = await fetch("../backend/salveaza_modificari.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ sterge: true, id })
  });

  const result = await response.json();
  afiseazaMesaj(result);
  if (result.success) incarcaUtilizatori();
}

// Ascunde săgeata animată după 2 secunde
setTimeout(() => {
  const arrow = document.getElementById("scrollArrow");
  if (arrow) arrow.style.display = "none";
}, 2000);
</script>

</body>
</html>