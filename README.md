# IT Job Portal

Veb portal za oglašavanje IT poslova koji povezuje kompanije sa kandidatima.
Projekat iz predmeta **Web programiranje** — Visoka tehnička škola strukovnih studija, Subotica.

## Opis

Portal omogućava kompanijama da objavljuju oglase za posao, a kandidatima da pretražuju
oglase i konkurišu na njih. Sistem razlikuje četiri tipa korisnika, pri čemu svaki ima
svoj skup dozvoljenih akcija:

- **Gost** — pregled i pretraga oglasa
- **Kandidat** — konkurisanje na oglase, uređivanje profila
- **Kompanija** — objavljivanje oglasa, pregled prijavljenih kandidata
- **Administrator** — upravljanje korisnicima, kategorijama i odobravanje oglasa

## Tehnologije

- **PHP 8.1+** (OOP, PDO sloj za pristup bazi)
- **MySQL** (utf8mb4)
- **HTML5, CSS3, Bootstrap 5** (responzivan interfejs)
- **JavaScript, Fetch API** (asinhrona pretraga, provera e-maila, konkurisanje)
- **PHPMailer** (SMTP — aktivacija naloga i resetovanje lozinke)
- **Composer** (upravljanje zavisnostima)

## Funkcionalnosti

- Registracija kandidata i kompanija sa aktivacijom naloga putem e-maila
- Prijava, odjava, resetovanje zaboravljene lozinke (token sa rokom važenja)
- Pretraga oglasa po ključnoj reči, kategoriji i lokaciji (asinhrono, bez osvežavanja)
- Objavljivanje oglasa (kompanija) uz odobrenje administratora
- Konkurisanje na oglas jednim klikom, uz sprečavanje duplih prijava
- Uređivanje korisničkog profila i promena lozinke
- Administratorski panel sa statistikom i upravljanjem sadržajem

## Bezbednost

- Lozinke heširane bcrypt algoritmom (cost 12)
- Zaštita od SQL injection kroz PDO pripremljene upite
- Zaštita od CSRF napada tokenima po sesiji
- Provera aktivacije i blokade naloga pri prijavi

## Pokretanje (lokalno)

Potreban je XAMPP (Apache, MySQL, PHP 8.1+) i Composer.

1. Kloniraj repozitorijum u `htdocs` folder:
   ```
   git clone https://github.com/stepanovicc/projekatweb.git job_portal
   ```

2. Instaliraj zavisnosti:
   ```
   cd job_portal
   composer install
   ```

3. Napravi bazu i uvezi strukturu:
   - u phpMyAdmin-u kreiraj bazu `job`
   - uvezi priloženi SQL dump (struktura + osnovni podaci)

4. Podesi konfiguraciju:
   - kopiraj `db_config.example.php` u `db_config.php`
   - upiši svoje podatke o bazi, `BASE_URL` i SMTP nalog

5. Otvori u browseru:
   ```
   http://localhost/job_portal/
   ```

## Pristup za administratora

Nakon uvoza osnovnih podataka:

- **E-mail:** `admin@jobportal.com`
- **Lozinka:** `Admin123!`

## Struktura projekta

```
├── admin/            # administratorski panel
├── css/              # stilovi
├── js/               # JavaScript (Fetch API)
├── includes/         # zaglavlje, podnožje, autentikacija
├── php/
│   ├── ajax/         # AJAX endpointi
│   └── classes/      # klase (User, JobListing, Category, ...)
├── vendor/           # Composer zavisnosti (nije u repozitorijumu)
├── index.php         # početna strana
├── db_config.php     # konfiguracija (nije u repozitorijumu)
└── db_config.example.php  # primer konfiguracije
```

## Napomena

Fajl `db_config.php` sa stvarnim lozinkama namerno nije u repozitorijumu
(nalazi se u `.gitignore`). Za rad aplikacije potrebno ga je kreirati lokalno
prema priloženom primeru `db_config.example.php`.
