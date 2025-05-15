<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Acces interzis."]);
    exit;
}

require 'db.php';

try {
    $stmt = $pdo->query("SELECT id, nume, prenume, cnp, telefon, codBluetooth, rol FROM utilizatori ORDER BY id");
    $utilizatori = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "utilizatori" => $utilizatori
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Eroare la interogare: " . $e->getMessage()
    ]);
}