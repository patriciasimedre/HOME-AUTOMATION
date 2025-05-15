<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode(["success" => false, "message" => "Acces interzis."]);
  exit;
}

require 'db.php';

$nume = $_POST['nume'] ?? '';
$prenume = $_POST['prenume'] ?? '';
$cnp = $_POST['cnp'] ?? '';
$telefon = $_POST['telefon'] ?? '';
$parola = $_POST['parola'] ?? '';
$codBluetooth = $_POST['codBluetooth'] ?? '';
$rol = $_POST['rol'] ?? 'user';

$erori = [];

if (!preg_match('/^[0-9]{13}$/', $cnp)) {
  $erori[] = "CNP-ul trebuie să conțină exact 13 cifre.";
}
if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
  $erori[] = "Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX).";
}
if (!empty($codBluetooth) && !preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
  $erori[] = "Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice.";
}
if (strlen($parola) < 6) {
  $erori[] = "Parola trebuie să aibă cel puțin 6 caractere.";
}

// Verificări unice
$sql_cnp = $pdo->prepare("SELECT id FROM utilizatori WHERE cnp = ?");
$sql_cnp->execute([$cnp]);
if ($sql_cnp->fetch()) {
  $erori[] = "CNP-ul este deja înregistrat.";
}
$sql_tel = $pdo->prepare("SELECT id FROM utilizatori WHERE telefon = ?");
$sql_tel->execute([$telefon]);
if ($sql_tel->fetch()) {
  $erori[] = "Numărul de telefon este deja folosit.";
}

if ($rol === 'admin') {
  $stmt_admin = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
  $nr_admini = $stmt_admin->fetchColumn();
  if ($nr_admini >= 1) {
    $erori[] = "Există deja un administrator în sistem. Doar unul este permis.";
  }
}

if (!empty($erori)) {
  echo json_encode(["success" => false, "message" => implode("<br>", $erori)]);
  exit;
}

$parola_hash = password_hash($parola, PASSWORD_DEFAULT);

try {
  $sql = "INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
          VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$nume, $prenume, $cnp, $telefon, $parola_hash, $codBluetooth, $rol]);

  echo json_encode(["success" => true, "message" => "Utilizator adăugat cu succes!"]);
} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => "Eroare la inserare: " . $e->getMessage()]);
}