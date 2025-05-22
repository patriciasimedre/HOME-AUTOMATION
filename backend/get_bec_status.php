<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
date_default_timezone_set('Europe/Bucharest');

if (!isset($_SESSION['rol']) || !isset($_SESSION['telefon'])) {
    echo json_encode(["success" => false, "message" => "Neautorizat"]);
    exit;
}

$rol = $_SESSION['rol'];
$telefon = $_SESSION['telefon'];

try {
    if ($rol === 'admin') {
        // Adminul vede ultima acțiune globală
        $stmt = $pdo->query("SELECT * FROM comenzi_bec ORDER BY timestamp DESC LIMIT 1");
        $data = $stmt->fetch();

        if ($data) {
            echo json_encode([
            "success" => true,
            "tip" => "admin",
            "status" => $data['actiune'], // cheie corectă așteptată de frontend
            "timestamp" => $data['timestamp'],
            "utilizator" => $data['telefon'] ?? 'necunoscut'
        ]);
        } else {
            echo json_encode(["success" => true, "tip" => "admin", "message" => "Nu există comenzi."]);
        }

    } else {
        // Userul vede toate comenzile proprii
        $stmt = $pdo->prepare("SELECT actiune, timestamp FROM comenzi_bec WHERE telefon = ? ORDER BY timestamp DESC");
        $stmt->execute([$telefon]);
        $actiuni = $stmt->fetchAll();

        echo json_encode([
            "success" => true,
            "tip" => "user",
            "actiuni" => $actiuni
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Eroare la citire: " . $e->getMessage()]);
}