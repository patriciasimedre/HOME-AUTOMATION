<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$telefon = $_POST['telefon'] ?? '';
$parola = $_POST['parola'] ?? '';

$sql = "SELECT * FROM utilizatori WHERE telefon = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$telefon]);
$user = $stmt->fetch();

if ($user) {
    if (password_verify($parola, $user['parola_hash'])) {
        $_SESSION['telefon'] = $user['telefon'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['prenume'] = $user['prenume'];

        echo json_encode([
            "success" => true,
            "redirect" => $user['rol'] === 'admin' ? "../frontend/admin.php" : "../frontend/user.php"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Parolă greșită."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Telefonul nu există."
    ]);
}