<?php
session_start();
if (!isset($_SESSION['rol'])) {
  header('Location: ../../login.html');
  exit;
}

include '../../backend/db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mesaj_tip_automat = '';
$mesaj_eroare = '';
$mesaj_succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zi = $_POST['zi'];
    $ora = $_POST['ora'];
    $temperatura = floatval($_POST['temperatura']);
    $histerezis = floatval($_POST['histerezis']);
    $offset = floatval($_POST['offset']);
    $tip = $_POST['tip_program'];

    if (!$zi || !$ora || $temperatura <= 0) {
        $mesaj_eroare = "⚠️ Te rog completează toate câmpurile necesare corect. Temperatura trebuie să fie > 0.";
    } elseif ($histerezis < 0 || $offset < -20 || $offset > 20) {
        $mesaj_eroare = "⚠️ Histerezisul trebuie să fie pozitiv. Offsetul între -20 și 20.";
    } else {
        if (empty($tip)) {
            if ($zi === 'sambata' || $zi === 'duminica') {
                $tip = 'weekend';
            } else {
                $tip = 'uzual';
            }
            $mesaj_tip_automat = "⚙️ Tipul de program a fost setat automat ca <strong>$tip</strong>.";
        }

        $stmt = $pdo->prepare("INSERT INTO programe_temperatura 
            (zi, ora_start, temperatura_referinta, histerezis, offset, tip_program)
            VALUES (:zi, :ora, :temp, :histerezis, :offset, :tip)");

        $stmt->execute([
            ':zi' => $zi,
            ':ora' => $ora,
            ':temp' => $temperatura,
            ':histerezis' => $histerezis,
            ':offset' => $offset,
            ':tip' => $tip
        ]);

        $mesaj_succes = "✅ Program adăugat cu succes!";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM programe_temperatura WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

$stmt = $pdo->query("SELECT * FROM programe_temperatura 
                     ORDER BY FIELD(zi,'luni','marti','miercuri','joi','vineri','sambata','duminica'), ora_start");
$programe = $stmt->fetchAll();

$linkInapoi = ($_SESSION['rol'] === 'admin') ? '../admin.php' : '../user.php';
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program temperatură</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, #610C9F, #940B92, #DA0C81, #E95793);
        }
    </style>
</head>
<body class="min-h-screen text-white font-sans">

<nav class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 bg-white/10 backdrop-blur-md shadow-lg gap-2 sm:gap-0">
    <a href="<?= $linkInapoi ?>" class="text-white font-medium hover:underline">&larr; Înapoi la panou</a>
    <div class="flex gap-4">
        <a href="../control_lumina.php" class="text-white hover:underline">💡 Control lumină</a>
        <a href="../activitate/activitate.php" class="text-white hover:underline">📋 Activitate</a>
        <a href="../../backend/logout.php" class="text-white hover:underline">🔒 Logout</a>
    </div>
</nav>

<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-center mb-6">🌡️ Gestionare Program Temperatură</h1>

    <?php if (!empty($mesaj_eroare)): ?>
        <div class="bg-red-300 text-black px-4 py-3 mb-4 rounded-xl shadow text-sm sm:text-base"><?= $mesaj_eroare ?></div>
    <?php endif; ?>

    <?php if (!empty($mesaj_succes)): ?>
        <div class="bg-green-300 text-black px-4 py-3 mb-4 rounded-xl shadow text-sm sm:text-base"><?= $mesaj_succes ?></div>
    <?php endif; ?>

    <?php if (!empty($mesaj_tip_automat)): ?>
        <div class="bg-yellow-300 text-black px-4 py-3 mb-4 rounded-xl shadow text-sm sm:text-base"><?= $mesaj_tip_automat ?></div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-2 sm:grid-cols-3 gap-4 bg-white/10 p-4 rounded-xl mb-8 shadow-lg backdrop-blur-md">
        <select name="zi" class="rounded p-2 text-black" required>
            <option value="">Zi</option>
            <?php foreach(['luni','marti','miercuri','joi','vineri','sambata','duminica'] as $z): ?>
                <option value="<?= $z ?>"><?= ucfirst($z) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="time" name="ora" class="rounded p-2 text-black" required>
        <input type="number" step="0.1" name="temperatura" placeholder="Temp. (°C)" class="rounded p-2 text-black" required>
        <input type="number" step="0.1" name="histerezis" placeholder="Histerezis" class="rounded p-2 text-black">
        <input type="number" step="0.1" name="offset" placeholder="Offset" class="rounded p-2 text-black">
        <select name="tip_program" class="rounded p-2 text-black">
            <option value="">Auto</option>
            <option value="uzual">Uzual</option>
            <option value="weekend">Weekend</option>
            <option value="concediu">Concediu</option>
        </select>
        <button type="submit" class="col-span-2 sm:col-span-3 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded text-base">
            ➕ Adaugă Program
        </button>
    </form>

    <div class="bg-white/10 rounded-xl p-4 shadow-lg backdrop-blur-md">
        <h2 class="text-xl font-semibold mb-4">📋 Programe existente</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-center border-separate border-spacing-y-2 text-sm min-w-[700px]">
                <thead>
                    <tr class="bg-[#610C9F] text-white">
                        <th class="p-2">Zi</th>
                        <th>Ora</th>
                        <th>Temp.</th>
                        <th>Histerezis</th>
                        <th>Offset</th>
                        <th>Tip</th>
                        <th>Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programe as $row): ?>
                        <tr class="bg-pink-400 text-black rounded">
                            <td class="p-2 rounded-l-xl"><?= ucfirst($row['zi']) ?></td>
                            <td><?= $row['ora_start'] ?></td>
                            <td><?= $row['temperatura_referinta'] ?>°C</td>
                            <td><?= $row['histerezis'] ?></td>
                            <td><?= $row['offset'] ?></td>
                            <td><?= ucfirst($row['tip_program']) ?></td>
                            <td class="rounded-r-xl">
                                <a href="?delete=<?= $row['id'] ?>" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">🗑️ Șterge</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>