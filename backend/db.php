<?php
$host = 'proiectip-db.mysql.database.azure.com';
$db   = 'proiectIP';
$user = 'azureadmin';
$pass = 'Haiaratacapoti69';  // <- înlocuiește cu parola reală
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Conexiune eșuată: " . $e->getMessage());
}
?>