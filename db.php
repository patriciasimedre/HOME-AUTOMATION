<?php
$host = 'localhost';
$db = 'autentificare';
$user = 'root';
$pass = ''; // sau parola ta de MySQL

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}
?>