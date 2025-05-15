<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['rol'])) {
  echo json_encode([
    'success' => true,
    'rol' => $_SESSION['rol'],
    'prenume' => $_SESSION['prenume'] ?? ''
  ]);
} else {
  echo json_encode(['success' => false]);
}