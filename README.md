# 🏠 Home Automation - Proiect IP

Acesta este un proiect de tip Smart Home realizat ca parte a lucrării pentru **Proiect IP**. Aplicația web permite:
- Autentificarea utilizatorilor (user/admin)
- Gestionarea utilizatorilor (adăugare, editare, ștergere)
- Controlul unui bec din Camera 1 (aprindere/stingere)
- Vizualizarea activității de intrare/ieșire în/din casă
- Forțarea unui program de încălzire (în lucru)

## 🔧 Tehnologii folosite
- **Frontend**: HTML, Tailwind CSS, JavaScript
- **Backend**: PHP
- **Bază de date**: MySQL (MariaDB, utilizând XAMPP)
- **Control acces**: PHP Sessions
- **Localhost & Telefon Test**: rulare locală + testare mobilă prin IP local

---

## 📁 Funcționalități

### 🔐 Autentificare
- Logare utilizatori pe baza **telefonului** și a **parolei**.
- Doar adminul poate adăuga/modifica utilizatori.

### 👤 Gestionare utilizatori (admin.php)
- Adăugare utilizator nou.
- Validări pentru:
  - CNP (13 cifre)
  - Telefon (format valid)
  - Parolă (minim 6 caractere)
- Vizualizare și editare în tabel responsive.
- Feedback vizual la adăugare/modificare/ștergere.

### 💡 Control lumină
- Interfață vizuală cu buton pentru **aprindere/stingere bec**.
- Animație de efect (emoji + glow).
- Comanda se salvează în baza de date `comenzi_bec`.

### 🚪 Activitate utilizatori
- Butoane pentru **Intrat în casă** / **Ieșit din casă**.
- Înregistrare oră + utilizator în tabelul `activitate`.
- Adminul vede toată activitatea, userul doar pe a lui.

### 🌡️ Forțare încălzire (în lucru)
- Se va adăuga modulul de trimitere a unei temperaturi dorite către sistemul de încălzire.

---

## 📱 Responsivitate
- Design optimizat pentru **desktop și mobil**.
- Tabel utilizatori cu scroll lateral.
- Testare pe telefon folosind:
  ```bash
  php -S 192.168.x.x:8000 -t /cale/proiect
  ```

---

## 🛠️ Structură directoare
```
proiectIP/
├── frontend/
│   ├── admin.php
│   ├── user.php
│   ├── control_lumina.php
│   └── activitate/
│       └── activitate.php
├── backend/
│   ├── adauga_utilizator.php
│   ├── salveaza_modificari.php
│   ├── check_session.php
│   ├── control_bec.php
│   ├── activitate.php
│   └── db.php
├── README.md
└── .gitignore
```

---

## ✅ Task-uri finalizate
- [x] Autentificare funcțională
- [x] Gestionare utilizatori (admin only)
- [x] Validări live și mesaje de eroare
- [x] Responsivitate pe mobil
- [x] Control bec cu animație
- [x] Activitate intrare/ieșire
- [ ] Forțare program încălzire (în curs)

---

## 🎨 Culori folosite
Această aplicație utilizează o paletă pastelată pentru un aspect jucăuș și prietenos:

- `#610C9F` – violet închis (navbar, titluri)
- `#940B92` – mov intens (accent, butoane hover)
- `#DA0C81` – roz intens (butoane principale)
- `#E95793` – roz deschis (fundaluri, animații)

Paletă: https://colorhunt.co/palette/610c9f940b92da0c81e95793

---

## 📆 Data generării
2025-05-15