<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

require 'db.php';

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

    // Validări
    if (!preg_match('/^[0-9]{13}$/', $cnp)) {
        header("Location: admin.php?status=" . urlencode("Eroare: CNP-ul trebuie să conțină exact 13 cifre."));
        exit;
    }

    if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
        header("Location: admin.php?status=" . urlencode("Eroare: Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX)."));
        exit;
    }

    if (!empty($codBluetooth) && !preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
        header("Location: admin.php?status=" . urlencode("Eroare: Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice."));
        exit;
    }

    // Preluăm datele actuale
    $stmt_select = $pdo->prepare("SELECT * FROM utilizatori WHERE id = ?");
    $stmt_select->execute([$id]);
    $vechi = $stmt_select->fetch();

    // Verificare CNP duplicat
    if ($cnp !== $vechi['cnp']) {
        $stmt_cnp = $pdo->prepare("SELECT id FROM utilizatori WHERE cnp = ? AND id != ?");
        $stmt_cnp->execute([$cnp, $id]);
        if ($stmt_cnp->fetch()) {
            header("Location: admin.php?status=" . urlencode("Eroare: CNP-ul $cnp este deja folosit de alt utilizator."));
            exit;
        }
    }

    // Verificare telefon duplicat
    if ($telefon !== $vechi['telefon']) {
        $stmt_tel = $pdo->prepare("SELECT id FROM utilizatori WHERE telefon = ? AND id != ?");
        $stmt_tel->execute([$telefon, $id]);
        if ($stmt_tel->fetch()) {
            header("Location: admin.php?status=" . urlencode("Eroare: Numărul de telefon $telefon este deja folosit de alt utilizator."));
            exit;
        }
    }

    // Verificare admin duplicat
    if ($rol === 'admin' && $vechi['rol'] !== 'admin') {
        $stmt_admin = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
        $nr_admini = $stmt_admin->fetchColumn();
        if ($nr_admini >= 1) {
            header("Location: admin.php?status=" . urlencode("Eroare: Există deja un administrator în sistem. Doar unul este permis."));
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
        $params = [$nume, $prenume, $cnp, $telefon, $codBluetooth, $rol, $parola_hash, $id];
        $schimbari[] = "parola";
    } else {
        $sql = "UPDATE utilizatori SET nume=?, prenume=?, cnp=?, telefon=?, codBluetooth=?, rol=? WHERE id=?";
        $params = [$nume, $prenume, $cnp, $telefon, $codBluetooth, $rol, $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

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

    $stmt_check = $pdo->prepare("SELECT rol FROM utilizatori WHERE id = ?");
    $stmt_check->execute([$id]);
    $rol_utilizator = $stmt_check->fetchColumn();

    if ($rol_utilizator === 'admin') {
        $stmt_admin_count = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
        $nr_admini = $stmt_admin_count->fetchColumn();

        if ($nr_admini <= 1) {
            $status = "Eroare: Nu poți șterge singurul administrator din sistem.";
            header("Location: admin.php?status=" . urlencode($status));
            exit;
        }
    }

    $stmt = $pdo->prepare("DELETE FROM utilizatori WHERE id = ?");
    $stmt->execute([$id]);

    $status = "Utilizatorul cu ID $id a fost șters cu succes";
    header("Location: admin.php?status=" . urlencode($status));
    exit;
}

header("Location: admin.php");
exit;
?>