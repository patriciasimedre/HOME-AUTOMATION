<?php
session_start();
header('Content-Type: application/json');
require 'db.php';
date_default_timezone_set('Europe/Bucharest');

if (!isset($_SESSION['rol'])) {
    http_response_code(403);
    echo json_encode(["succes" => false, "mesaj" => "Acces interzis"]);
    exit;
}

$actiune = $_POST['actiune'] ?? null;
$camera = "camera1";

if (!in_array($actiune, ['on', 'off'])) {
    echo json_encode(["succes" => false, "mesaj" => "Comandă invalidă"]);
    exit;
}

$telefon = $_SESSION['telefon'];

$stmt = $pdo->prepare("INSERT INTO comenzi_bec (camera, actiune, telefon) VALUES (?, ?, ?)");
$stmt->execute([$camera, $actiune, $telefon]);

echo json_encode([
  "succes" => true,
  "mesaj" => $actiune === 'on' ? 'Aprins' : 'Stins'
]);