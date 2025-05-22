<?php
session_start();
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Acces interzis."]);
    exit;
}

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID lipsă."]);
    exit;
}

$id = $data['id'];

// 🔁 GESTIONARE ȘTERGERE UTILIZATOR
if (isset($data['sterge']) && $data['sterge'] === true) {
    $stmt_admin = $pdo->prepare("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
    $stmt_admin->execute();
    $admini = $stmt_admin->fetchColumn();

    $stmt_utilizator = $pdo->prepare("SELECT rol FROM utilizatori WHERE id = ?");
    $stmt_utilizator->execute([$id]);
    $utilizator = $stmt_utilizator->fetch();

    if ($utilizator && $utilizator['rol'] === 'admin' && $admini == 1) {
        echo json_encode(["success" => false, "message" => "Nu poți șterge ultimul administrator din sistem."]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM utilizatori WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true, "message" => "Utilizator șters cu succes."]);
    exit;
}

// 🔁 ACTUALIZARE DATE UTILIZATOR
$nume = $data['nume'] ?? '';
$prenume = $data['prenume'] ?? '';
$cnp = $data['cnp'] ?? '';
$telefon = $data['telefon'] ?? '';
$codBluetooth = $data['codBluetooth'] ?? '';
$rol = $data['rol'] ?? '';
$parola_noua = trim($data['parola'] ?? '');

$erori = [];

if (!preg_match('/^[0-9]{13}$/', $cnp)) {
    $erori[] = "CNP-ul trebuie să conțină exact 13 cifre.";
}
if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
    $erori[] = "Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX).";
}
if (!empty($codBluetooth) && !preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
    $erori[] = "Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice.";
}

$stmt_select = $pdo->prepare("SELECT * FROM utilizatori WHERE id = ?");
$stmt_select->execute([$id]);
$vechi = $stmt_select->fetch();

if (!$vechi) {
    echo json_encode(["success" => false, "message" => "Utilizatorul nu a fost găsit."]);
    exit;
}

if ($cnp !== $vechi['cnp']) {
    $stmt_cnp = $pdo->prepare("SELECT id FROM utilizatori WHERE cnp = ? AND id != ?");
    $stmt_cnp->execute([$cnp, $id]);
    if ($stmt_cnp->fetch()) {
        $erori[] = "CNP-ul este deja folosit.";
    }
}
if ($telefon !== $vechi['telefon']) {
    $stmt_tel = $pdo->prepare("SELECT id FROM utilizatori WHERE telefon = ? AND id != ?");
    $stmt_tel->execute([$telefon, $id]);
    if ($stmt_tel->fetch()) {
        $erori[] = "Numărul de telefon este deja folosit.";
    }
}

// ✅ Verificare dacă este ultimul admin care își schimbă rolul în user
if ($vechi['rol'] === 'admin' && $rol === 'user') {
    $stmt_admin = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
    if ($stmt_admin->fetchColumn() <= 1) {
        $_SESSION['necesita_admin'] = true;
    }
}

if (!empty($erori)) {
    echo json_encode(["success" => false, "message" => implode("<br>", $erori)]);
    exit;
}

$schimbari = [];
if ($nume !== $vechi['nume']) $schimbari[] = "numele";
if ($prenume !== $vechi['prenume']) $schimbari[] = "prenumele";
if ($cnp !== $vechi['cnp']) $schimbari[] = "CNP-ul";
if ($telefon !== $vechi['telefon']) $schimbari[] = "telefonul";
if ($codBluetooth !== $vechi['codBluetooth']) $schimbari[] = "codul Bluetooth";
if ($rol !== $vechi['rol']) $schimbari[] = "rolul";

try {
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

    $mesaj = !empty($schimbari)
      ? "Au fost modificate: " . implode(", ", $schimbari) . "."
      : "Nu s-au detectat modificări.";

    // Afișează mesaj suplimentar dacă este nevoie de admin
    if (isset($_SESSION['necesita_admin']) && $_SESSION['necesita_admin']) {
        unset($_SESSION['necesita_admin']);
        $mesaj .= " <strong class='block mt-1 text-yellow-700'>ATENȚIE: În sistem nu mai există niciun administrator. Următorul utilizator adăugat va deveni automat admin.</strong>";
    }

    echo json_encode(["success" => true, "message" => $mesaj]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Eroare la salvare: " . $e->getMessage()]);
}