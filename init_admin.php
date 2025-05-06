<?php
require 'db.php';

// Datele adminului
$nume = 'Admin';
$prenume = 'Test';
$cnp = '1234567890123';
$telefon = '0700000000';
$parola = 'admin123';
$codBluetooth = 'BT12345678';
$rol = 'admin';

// Criptăm parola
$parola_hash = password_hash($parola, PASSWORD_DEFAULT);

// Verificăm dacă adminul deja există
$stmt_check = $pdo->prepare("SELECT id FROM utilizatori WHERE telefon = ?");
$stmt_check->execute([$telefon]);
$exista = $stmt_check->fetch();

if (!$exista) {
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

    echo "Adminul a fost inserat cu succes.";
} else {
    echo "Adminul există deja.";
}
?>