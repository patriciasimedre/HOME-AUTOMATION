<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis.");
}

require 'db.php';

$mesaj = "";
$mesaj_tip = "";
if (isset($_GET['status'])) {
    $mesaj = htmlspecialchars($_GET['status']);
    $mesaj_tip = (strpos($mesaj, 'șters') !== false || strpos($mesaj, 'Eroare') !== false) ? 'danger' : 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adauga'])) {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $cnp = $_POST['cnp'];
    $telefon = $_POST['telefon'];
    $parola = $_POST['parola'];
    $codBluetooth = $_POST['codBluetooth'];
    $rol = $_POST['rol'];

    $erori = [];

    if (!preg_match('/^[0-9]{13}$/', $cnp)) {
        $erori[] = "CNP-ul trebuie să conțină exact 13 cifre.";
    }

    if (!preg_match('/^07[0-9]{8}$/', $telefon)) {
        $erori[] = "Numărul de telefon trebuie să fie valid (ex: 07XXXXXXXX).";
    }

    if (!preg_match('/^[a-zA-Z0-9]{4,10}$/', $codBluetooth)) {
        $erori[] = "Codul Bluetooth trebuie să aibă între 4 și 10 caractere alfanumerice.";
    }

    if (strlen($parola) < 6) {
        $erori[] = "Parola trebuie să aibă cel puțin 6 caractere.";
    }

    // Verificări unice
    $sql_cnp = $pdo->prepare("SELECT id FROM utilizatori WHERE cnp = ?");
    $sql_cnp->execute([$cnp]);
    if ($sql_cnp->fetch()) {
        $erori[] = "CNP-ul este deja înregistrat.";
    }

    $sql_tel = $pdo->prepare("SELECT id FROM utilizatori WHERE telefon = ?");
    $sql_tel->execute([$telefon]);
    if ($sql_tel->fetch()) {
        $erori[] = "Numărul de telefon este deja folosit.";
    }

    if ($rol === 'admin') {
        $stmt_admin = $pdo->query("SELECT COUNT(*) FROM utilizatori WHERE rol = 'admin'");
        $nr_admini = $stmt_admin->fetchColumn();
        if ($nr_admini >= 1) {
            $erori[] = "Există deja un administrator în sistem. Doar unul este permis.";
        }
    }

    if (empty($erori)) {
        $parola_hash = password_hash($parola, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$nume, $prenume, $cnp, $telefon, $parola_hash, $codBluetooth, $rol]);

        if ($ok) {
            $mesaj = "Utilizator adăugat cu succes!";
            $mesaj_tip = "success";
        } else {
            $mesaj = "Eroare la inserare.";
            $mesaj_tip = "error";
        }
    } else {
        $mesaj = implode("<br>", $erori);
        $mesaj_tip = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <title>Panou Admin</title>
  <style>
    body { font-family: Arial, sans-serif; }
    nav { background: #eee; padding: 10px; margin-bottom: 20px; }
    nav a { margin-right: 10px; }
    table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; }
    input[type=text], input[type=password] { width: 100px; }
    .success { color: green; }
    .error, .danger { color: red; }
  </style>
</head>
<body>

<nav>
  <strong>Admin Panel</strong> |
  <a href="admin.php">Adaugă utilizator</a> |
  <a href="logout.php">Logout</a>
</nav>

<?php if ($mesaj): ?>
  <p class="<?= $mesaj_tip ?>">
    <strong><?= $mesaj ?></strong>
  </p>
<?php endif; ?>

<form method="POST">
  <h3>Adaugă utilizator</h3>
  <input type="hidden" name="adauga" value="1">
  <label>Nume: <input type="text" name="nume" required></label>
  <label>Prenume: <input type="text" name="prenume" required></label>
  <label>CNP: <input type="text" name="cnp" maxlength="13" required></label>
  <label>Telefon: <input type="text" name="telefon" required></label>
  <label>Parolă: <input type="text" name="parola" required></label>
  <label>Bluetooth: <input type="text" name="codBluetooth"></label>
  <label>Rol:
    <select name="rol">
      <option value="user">user</option>
      <option value="admin">admin</option>
    </select>
  </label>
  <button type="submit">Adaugă</button>
</form>

<hr>

<h3>Utilizatori existenți</h3>
<form method="POST" action="salveaza_modificari.php">
  <table>
    <tr>
      <th>ID</th>
      <th>Nume</th>
      <th>Prenume</th>
      <th>CNP</th>
      <th>Telefon</th>
      <th>Bluetooth</th>
      <th>Rol</th>
      <th>Parolă nouă</th>
      <th>Salvează</th>
      <th>Șterge</th>
    </tr>
    <?php
    $stmt = $pdo->query("SELECT * FROM utilizatori");
    while ($row = $stmt->fetch()) {
        echo "<tr>
          <td>{$row['id']}<input type='hidden' name='id[]' value='{$row['id']}'></td>
          <td><input type='text' name='nume[]' value='{$row['nume']}'></td>
          <td><input type='text' name='prenume[]' value='{$row['prenume']}'></td>
          <td><input type='text' name='cnp[]' value='{$row['cnp']}'></td>
          <td><input type='text' name='telefon[]' value='{$row['telefon']}'></td>
          <td><input type='text' name='codBluetooth[]' value='{$row['codBluetooth']}'></td>
          <td>
            <select name='rol[]'>
              <option value='user'" . ($row['rol'] === 'user' ? ' selected' : '') . ">user</option>
              <option value='admin'" . ($row['rol'] === 'admin' ? ' selected' : '') . ">admin</option>
            </select>
          </td>
          <td><input type='password' name='parola[]' placeholder='Nouă parolă'></td>
          <td><button type='submit' name='salveaza' value='{$row['id']}'>Salvează</button></td>
          <td><button type='submit' name='sterge' value='{$row['id']}' onclick=\"return confirm('Sigur vrei să ștergi acest utilizator?')\">🗑️</button></td>
        </tr>";
    }
    ?>
  </table>
</form>

<br>
<button onclick="history.back()">← Înapoi</button>
<button onclick="history.forward()">Înainte →</button>

</body>
</html>