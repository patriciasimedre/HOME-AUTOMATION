<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

require 'db.php';

try {
    $sql = "SELECT id, nume, prenume, cnp, telefon, codBluetooth, rol FROM utilizatori ORDER BY id";
    $stmt = $pdo->query($sql);
    $utilizatori = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Eroare la interogare: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Lista utilizatori</title>
</head>
<body>
  <h2>Toți utilizatorii</h2>
  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>Nume</th>
      <th>Prenume</th>
      <th>CNP</th>
      <th>Telefon</th>
      <th>Bluetooth</th>
      <th>Rol</th>
    </tr>

    <?php foreach ($utilizatori as $row) { ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['nume']) ?></td>
        <td><?= htmlspecialchars($row['prenume']) ?></td>
        <td><?= htmlspecialchars($row['cnp']) ?></td>
        <td><?= htmlspecialchars($row['telefon']) ?></td>
        <td><?= htmlspecialchars($row['codBluetooth']) ?></td>
        <td><?= htmlspecialchars($row['rol']) ?></td>
      </tr>
    <?php } ?>

  </table>
  <br>
  <a href="admin.php">Înapoi la admin</a>
</body>
</html>