<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

include 'db.php';

if (isset($_POST['salveaza'])) {
    $id = $_POST['salveaza'];
    $index = array_search($id, $_POST['id']);

    $nume = $_POST['nume'][$index];
    $prenume = $_POST['prenume'][$index];
    $cnp = $_POST['cnp'][$index];
    $telefon = $_POST['telefon'][$index];
    $codBluetooth = $_POST['codBluetooth'][$index];
    $rol = $_POST['rol'][$index];
    $parola_noua = trim($_POST['parola'][$index]);

    // Validări de formă
    if (!preg_match('/^[0-9]{13}$/', $cnp)) {
        $status = "Eroare: CNP-ul trebuie să conțină exact 13 cifre.";
        header("Location: admin.php?status=" . urlencode($status));
        exit;
    }

    if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
        $status = "Eroare: Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX).";
        header("Location: admin.php?status=" . urlencode($status));
        exit;
    }

    if (!empty($codBluetooth) && !preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
        $status = "Eroare: Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice.";
        header("Location: admin.php?status=" . urlencode($status));
        exit;
    }

    // Preluăm datele actuale din DB pentru comparație
    $sql_select = "SELECT * FROM utilizatori WHERE id=?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $rezultat = $stmt_select->get_result();
    $vechi = $rezultat->fetch_assoc();

    // Verificare CNP duplicat (dacă s-a schimbat)
    if ($cnp !== $vechi['cnp']) {
        $stmt_cnp = $conn->prepare("SELECT id FROM utilizatori WHERE cnp = ? AND id != ?");
        $stmt_cnp->bind_param("si", $cnp, $id);
        $stmt_cnp->execute();
        $stmt_cnp->store_result();
        if ($stmt_cnp->num_rows > 0) {
            $status = "Eroare: CNP-ul $cnp este deja folosit de alt utilizator.";
            header("Location: admin.php?status=" . urlencode($status));
            exit;
        }
    }

    // Verificare telefon duplicat (dacă s-a schimbat)
    if ($telefon !== $vechi['telefon']) {
        $stmt_tel = $conn->prepare("SELECT id FROM utilizatori WHERE telefon = ? AND id != ?");
        $stmt_tel->bind_param("si", $telefon, $id);
        $stmt_tel->execute();
        $stmt_tel->store_result();
        if ($stmt_tel->num_rows > 0) {
            $status = "Eroare: Numărul de telefon $telefon este deja folosit de alt utilizator.";
            header("Location: admin.php?status=" . urlencode($status));
            exit;
        }
    }

    // Verificare dacă se încearcă setarea ca admin
    if ($rol === 'admin' && $vechi['rol'] !== 'admin') {
        $stmt_admin = $conn->prepare("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
        $stmt_admin->execute();
        $stmt_admin->bind_result($nr_admini);
        $stmt_admin->fetch();
        if ($nr_admini >= 1) {
            $status = "Eroare: Există deja un administrator în sistem. Doar unul este permis.";
            header("Location: admin.php?status=" . urlencode($status));
            exit;
        }
    }

    $schimbari = [];

    if ($nume !== $vechi['nume']) $schimbari[] = "numele";
    if ($prenume !== $vechi['prenume']) $schimbari[] = "prenumele";
    if ($cnp !== $vechi['cnp']) $schimbari[] = "CNP-ul";
    if ($telefon !== $vechi['telefon']) $schimbari[] = "telefonul";
    if ($codBluetooth !== $vechi['codBluetooth']) $schimbari[] = "codul Bluetooth";
    if ($rol !== $vechi['rol']) $schimbari[] = "rolul";

    if (!empty($parola_noua)) {
        $parola_hash = password_hash($parola_noua, PASSWORD_DEFAULT);
        $sql = "UPDATE utilizatori SET nume=?, prenume=?, cnp=?, telefon=?, codBluetooth=?, rol=?, parola_hash=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $nume, $prenume, $cnp, $telefon, $codBluetooth, $rol, $parola_hash, $id);
        $schimbari[] = "parola";
    } else {
        $sql = "UPDATE utilizatori SET nume=?, prenume=?, cnp=?, telefon=?, codBluetooth=?, rol=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $nume, $prenume, $cnp, $telefon, $codBluetooth, $rol, $id);
    }

    $stmt->execute();

    if (!empty($schimbari)) {
        $status = "Au fost modificate următoarele câmpuri pentru utilizatorul cu ID $id: " . implode(", ", $schimbari);
    } else {
        $status = "Nu s-au detectat modificări pentru utilizatorul cu ID $id.";
    }
    header("Location: admin.php?status=" . urlencode($status));
    exit;
}

if (isset($_POST['sterge'])) {
    $id = $_POST['sterge'];

    // Verificăm dacă este singurul admin
    $stmt_check = $conn->prepare("SELECT rol FROM utilizatori WHERE id = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $stmt_check->bind_result($rol_utilizator);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($rol_utilizator === 'admin') {
        $stmt_admin_count = $conn->prepare("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
        $stmt_admin_count->execute();
        $stmt_admin_count->bind_result($nr_admini);
        $stmt_admin_count->fetch();
        $stmt_admin_count->close();

        if ($nr_admini <= 1) {
            $status = "Eroare: Nu poți șterge singurul administrator din sistem.";
            header("Location: admin.php?status=" . urlencode($status));
            exit;
        }
    }

    $sql = "DELETE FROM utilizatori WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $status = "Utilizatorul cu ID $id a fost șters cu succes";
    header("Location: admin.php?status=" . urlencode($status));
    exit;
}

header("Location: admin.php");
exit;
?>
