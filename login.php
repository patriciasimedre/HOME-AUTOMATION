<?php
session_start();
require 'db.php';

# temporar
echo "<pre>";
print_r($_POST);
echo "</pre>";
#-------------

$telefon = $_POST['telefon'] ?? '';
$parola = $_POST['parola'] ?? '';

$sql = "SELECT * FROM utilizatori WHERE telefon = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$telefon]);
$user = $stmt->fetch();

if ($user) {
    # temporar
    echo "Hash salvat: " . $user['parola_hash'] . "<br>";
    echo "Parolă introdusă: " . $parola . "<br>";
    #---------------

    if (password_verify($parola, $user['parola_hash'])) {
        var_dump(password_verify($parola, $user['parola_hash']));
        $_SESSION['telefon'] = $user['telefon'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] === 'admin') {
            header("Location: admin.php");
            exit;
        } else {
            echo "Bine ai venit, " . htmlspecialchars($user['prenume']) . "!";
        }
    } else {
        echo "Parolă greșită.";
    }
} else {
    echo "Telefonul nu există.";
}
?>