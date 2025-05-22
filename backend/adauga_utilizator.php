<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

$nume         = trim($_POST['nume'] ?? '');
$prenume      = trim($_POST['prenume'] ?? '');
$cnp          = trim($_POST['cnp'] ?? '');
$telefon      = trim($_POST['telefon'] ?? '');
$parola       = trim($_POST['parola'] ?? '');
$codBluetooth = trim($_POST['codBluetooth'] ?? '');
$rol          = trim($_POST['rol'] ?? 'user');

$erori = [];

if ($nume === '' || $prenume === '') {
    $erori[] = "Numele și prenumele sunt obligatorii.";
}
if (!preg_match('/^[0-9]{13}$/', $cnp)) {
    $erori[] = "CNP-ul trebuie să conțină exact 13 cifre.";
}
if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
    $erori[] = "Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX).";
}
if (strlen($parola) < 6) {
    $erori[] = "Parola trebuie să aibă cel puțin 6 caractere.";
}
if (!empty($codBluetooth) && !preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
    $erori[] = "Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice.";
}

// Verificăm dacă deja există acest CNP sau telefon
$stmt_check = $pdo->prepare("SELECT id FROM utilizatori WHERE cnp = ? OR telefon = ?");
$stmt_check->execute([$cnp, $telefon]);
if ($stmt_check->fetch()) {
    $erori[] = "CNP-ul sau telefonul sunt deja înregistrate.";
}

if (!empty($erori)) {
    echo json_encode(["success" => false, "message" => implode("<br>", $erori)]);
    exit;
}

// Verificăm dacă mai există admini
$stmt_admin = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
$nr_admini = $stmt_admin->fetchColumn();
$promovat_automat = false;

if ($nr_admini == 0) {
    $rol = 'admin';
    $promovat_automat = true;
}

// Hash pentru parola
$parola_hash = password_hash($parola, PASSWORD_DEFAULT);

// Inserăm în baza de date
$stmt = $pdo->prepare("INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
$ok = $stmt->execute([$nume, $prenume, $cnp, $telefon, $parola_hash, $codBluetooth, $rol]);

if ($ok) {
    $mesaj = "Utilizator adăugat cu succes!";
    if ($promovat_automat) {
        $mesaj .= " Acest utilizator a fost promovat automat ca administrator pentru a nu lăsa sistemul fără admin.";
    }
    echo json_encode(["success" => true, "message" => $mesaj]);
} else {
    echo json_encode(["success" => false, "message" => "Eroare la adăugare."]);
}