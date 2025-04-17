<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

include 'db.php';

$nume = $_POST['nume'];
$prenume = $_POST['prenume'];
$cnp = $_POST['cnp'];
$telefon = $_POST['telefon'];
$parola = $_POST['parola'];
$codBluetooth = $_POST['codBluetooth'];
$rol = $_POST['rol'];

// Criptăm parola înainte de salvare
$parola_hash = password_hash($parola, PASSWORD_DEFAULT);

$sql = "INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $nume, $prenume, $cnp, $telefon, $parola_hash, $codBluetooth, $rol);

if ($stmt->execute()) {
    echo "Utilizatorul a fost adăugat cu succes.";
} else {
    echo "Eroare: " . $stmt->error;
}
?>