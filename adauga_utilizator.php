<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

require 'db.php';

$nume = $_POST['nume'];
$prenume = $_POST['prenume'];
$cnp = $_POST['cnp'];
$telefon = $_POST['telefon'];
$parola = $_POST['parola'];
$codBluetooth = $_POST['codBluetooth'];
$rol = $_POST['rol'];

// Criptăm parola înainte de salvare
$parola_hash = password_hash($parola, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
            VALUES (:nume, :prenume, :cnp, :telefon, :parola_hash, :codBluetooth, :rol)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nume' => $nume,
        ':prenume' => $prenume,
        ':cnp' => $cnp,
        ':telefon' => $telefon,
        ':parola_hash' => $parola_hash,
        ':codBluetooth' => $codBluetooth,
        ':rol' => $rol
    ]);

    echo "Utilizatorul a fost adăugat cu succes.";
} catch (PDOException $e) {
    echo "Eroare: " . $e->getMessage();
}
?>