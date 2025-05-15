<?php
session_start();
require '../../backend/db.php';
date_default_timezone_set('Europe/Bucharest');

if (!isset($_SESSION['telefon']) || !isset($_POST['actiune'])) {
  echo json_encode(['success' => false, 'message' => 'Acces neautorizat.']);
  exit;
}

$telefon = $_SESSION['telefon'];
$actiune = $_POST['actiune'];

if (!in_array($actiune, ['intrat', 'iesit'])) {
  echo json_encode(['success' => false, 'message' => 'Acțiune invalidă.']);
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT INTO activitate (telefon, actiune) VALUES (?, ?)");
  $stmt->execute([$telefon, $actiune]);
  echo json_encode(['success' => true, 'message' => 'Activitate înregistrată!']);
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Eroare: ' . $e->getMessage()]);
}
?>