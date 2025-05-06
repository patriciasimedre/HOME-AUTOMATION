<?php
require 'db.php';

try {
    echo "<h2>✅ Conexiunea la baza de date Azure a reușit!</h2>";

    $stmt = $pdo->query("SELECT id, nume, prenume, rol FROM utilizatori");
    $utilizatori = $stmt->fetchAll();

    if (count($utilizatori) > 0) {
        echo "<h3>Utilizatori existenți:</h3><ul>";
        foreach ($utilizatori as $user) {
            echo "<li>{$user['id']}: {$user['nume']} {$user['prenume']} ({$user['rol']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nu există utilizatori în tabel.</p>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Conexiune eșuată:</h2><pre>" . $e->getMessage() . "</pre>";
}
?>