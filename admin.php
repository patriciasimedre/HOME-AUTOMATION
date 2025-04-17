<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Admin – Adaugă utilizator</title>
</head>
<body>
  <h2>Adaugă utilizator nou</h2>
  <form action="adauga_utilizator.php" method="POST">
    <label>Nume:</label>
    <input type="text" name="nume" required><br>

    <label>Prenume:</label>
    <input type="text" name="prenume" required><br>

    <label>CNP:</label>
    <input type="text" name="cnp" maxlength="13" required><br>

    <label>Telefon:</label>
    <input type="text" name="telefon" maxlength="15" required><br>

    <label>Parolă:</label>
    <input type="text" name="parola" required><br>

    <label>Cod Bluetooth:</label>
    <input type="text" name="codBluetooth"><br>

    <label>Rol:</label>
    <select name="rol" required>
      <option value="user">User</option>
      <option value="admin">Admin</option>
    </select><br><br>

    <input type="submit" value="Adaugă utilizator">
  </form>
</body>
</html>