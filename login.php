<?php
session_start();
include 'db.php';

# temporar
echo "<pre>";
print_r($_POST);
echo "</pre>";
#-------------

$telefon = $_POST['telefon'];
$parola = $_POST['parola'];

$sql = "SELECT * FROM utilizatori WHERE telefon=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $telefon);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
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
        } else {
            echo "Bine ai venit, " . $user['prenume'] . "!";
        }
    } else {
        echo "Parolă greșită.";
    }
} else {
    echo "Telefonul nu există.";
}
?>