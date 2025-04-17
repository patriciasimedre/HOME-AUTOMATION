<?php
include 'db.php';

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

// Verificăm dacă deja există
$check = $conn->prepare("SELECT id FROM utilizatori WHERE telefon = ?");
$check->bind_param("s", $telefon);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $sql = "INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nume, $prenume, $cnp, $telefon, $parola_hash, $codBluetooth, $rol);

    if ($stmt->execute()) {
        echo "Adminul a fost inserat cu succes.";
    } else {
        echo "Eroare la inserare: " . $stmt->error;
    }
} else {
    echo "Adminul există deja.";
}
?>