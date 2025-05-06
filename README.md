# 🏠 HOME-AUTOMATION

Aplicație web PHP pentru gestionarea utilizatorilor într-un sistem de tip Home Automation, cu autentificare pe roluri și conectare la o bază de date MySQL găzduită în Azure.

---

## 🚀 Funcționalități

- Autentificare cu număr de telefon și parolă
- Criptarea parolelor cu `password_hash()`
- Panou Admin cu:
  - Vizualizare listă utilizatori
  - Adăugare, modificare, ștergere utilizatori
  - Validări stricte pentru CNP, telefon, cod Bluetooth
- Limitare: doar un singur admin permis în sistem
- Bază de date MySQL găzduită în Azure
- Conectare sigură prin PDO (cu suport pentru SSL)

---

## ⚙️ Configurare locală

### 1. Clonează proiectul:

```bash
git clone https://github.com/patriciasimedre/HOME-AUTOMATION.git
cd HOME-AUTOMATION
git checkout autentificare_AzureDB
```

---

### 2. Configurare fișier `db.php`

Creează fișierul `db.php` în root și adaugă:

```php
<?php
$host = 'proiectip-db.mysql.database.azure.com';
$db   = 'proiectIP';
$user = 'azureadmin';
$pass = 'parola_ta';
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
```

> 💡 Poți adăuga un `.env` + loader pentru securizare ulterioară.

---

### 3. Reguli Azure pentru conexiune

- Asigură-te că IP-ul tău public este adăugat în **Azure MySQL Firewall Rules**.
- Portul 3306 trebuie să fie deschis (verificat automat dacă funcționează în Workbench).

---

### 4. Rulare locală

Folosește un server PHP:

```bash
php -S localhost:8000
```

Accesează aplicația:
```
http://localhost:8000/login.html
```

---

## 👤 Date implicite pentru autentificare

Scriptul `init_admin.php` adaugă un admin dacă nu există:

```
📱 Telefon: 0700000000
🔑 Parolă: admin123
```

---

## 📁 Structură fișiere relevante

- `db.php` – Conexiunea PDO la baza Azure
- `login.php` – Autentificare utilizator
- `admin.php` – Panou de administrare
- `lista_utilizatori.php` – Afișare utilizatori
- `salveaza_modificari.php` – Modificare / ștergere
- `adauga_utilizator.php` – Inserare manuală
- `init_admin.php` – Adaugă admin implicit
- `logout.php` – Terminare sesiune

---

## 🧠 Tehnologii

- PHP 8+
- MySQL 8 (Azure-hosted)
- PDO
- HTML + CSS basic
- XAMPP / PHP dev server local

---

## 🛡️ Securitate

- Parole criptate cu `password_hash()`
- Validare date critice (CNP, telefon)
- Limitare acces pe roluri (admin vs. user)

---


---

## 👥 Utilizatori demo (pentru testare)

Poți adăuga în baza de date câțiva utilizatori de test rulând:

```sql
INSERT INTO utilizatori (nume, prenume, cnp, telefon, parola_hash, codBluetooth, rol)
VALUES 
('Popescu', 'Ana', '2990101123456', '0712345678', SHA2('parola123', 256), 'BT123456', 'admin'),
('Ionescu', 'Andrei', '1990202123456', '0723456789', SHA2('parola456', 256), 'BT789012', 'user');
```

> ⚠️ Dacă folosești `password_hash()` în PHP, folosește același algoritm și în aplicație.


## 📌 Licență

Proiect educațional realizat de [@patriciasimedre](https://github.com/patriciasimedre)