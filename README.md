# To-Do App – Laravel 11

Aplikacja To-Do napisana w Laravel 11 z:
- operacjami CRUD na zadaniach
- filtrowaniem i paginacją
- systemem wersjonowania treści zadania
- powiadomieniami e-mail (na 1 dzień przed terminem)
- integracją z Google Calendar
- publicznym linkiem do zadań
- REST API (z autoryzacją Sanctum)
- możliwość uruchomienia w Dockerze

---

## Wymagania
- PHP 8.2+
- Composer
- Node.js + NPM
- SQLite lub MySQL
- Konto Google + Google Cloud Console (dla integracji kalendarza)

---

## Instalacja

```bash
git clone https://github.com/msosnowski-dev/todoApp.git
```
```bash
cd todoApp
```
```bash
composer install
```
```bash
npm install && npm run build
```
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```

---

## Modyfikacje pliku `.env`

```dotenv
APP_TIMEZONE=Europe/Warsaw
APP_URL=http://127.0.0.1:8000
APP_LOCALE=pl
```

---

## Baza danych

### Domyślnie: SQLite

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
```

Utwórz plik bazy:

```bash
touch database/database.sqlite
```

### Alternatywnie MySQL

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=todo
DB_USERNAME=root
DB_PASSWORD=root
```

### Migracja

```bash
php artisan optimize
```
```bash
php artisan migrate
```

---

## Powiadomienia email

Powiadomienie e-mail jest wysyłane na 1 dzień przed `due_date` zadania, o godzinie **08:00**.

Domyślnie:

```dotenv
MAIL_MAILER=log
```

Dla wysyłki e-maili przez SMTP:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=nazwa_hosta_smtp
MAIL_PORT=587
MAIL_USERNAME=adres_email
MAIL_PASSWORD=haslo_do_email
```

### Harmonogram (Scheduler)

Do prawidłowego działania wysyłki powiadomień, należy użyć Laravel schedule:work lub serwerowego CRON

```bash
php artisan schedule:work
```

lub wklejamy tę linijkę do listy zadań CRON

```bash
php /ścieżka/do/projektu/artisan schedule:run >> /dev/null 2>&1
```

Jeżeli nie chcemy czekać, można od razu przetestować działanie za pomocą utworzonej komendy:

```bash
php artisan tasks:send-reminders
```

Powiadomienia email, spełniające kryteria, powinny przyjść na Twój email

## Integracja z Google Calendar

Użyto biblioteki: [`spatie/laravel-google-calendar`](https://github.com/spatie/laravel-google-calendar)

### Wymagane działania:

1. Zalogować się do Google Cloud Console
   1. Wybrać projekt przypisany do konta serwisowego
   2. Przejść do Biblioteka (Library) i wyszukać Google Calendar API
   3. Kliknąć „Włącz” (Enable)
2. Utworzyć konto serwisowe Google z uprawnieniem do edycji kalendarza
3. Umieścić plik `.json` z poświadczeniami w:

```
storage/app/google-calendar/service-account-credentials.json
```

4. W ustawieniach kalendarza Google, w sekcji "Udostępnione dla" należy dodać adres e-mail konta serwisowego i nadać mu uprawnienie "może zmieniać wydarzenia"
5. Dodaj do `.env`:

```dotenv
GOOGLE_CALENDAR_ID=twoj_adres@gmail.com
```

6. Następnie:

```bash
php artisan optimize
```

Zadania można ręcznie przypinać do kalendarza Google, a także usuwać je z poziomu aplikacji.

## Autoryzacja i REST API

Część danych dostępna jest także przez API (`/api/tasks`).  
Autoryzacja przez Laravel Sanctum (token API generowany przy logowaniu).  
Operacja DELETE także AJAX-em przez token `Bearer`.

## Uruchomienie aplikacji

```bash
php artisan serve
```

## Uruchomienie przez Docker (opcjonalnie)

### 1. Uruchom kontenery

```bash
docker-compose up -d --build
```

### 2. Wykonaj wszystkie instrukcje z sekcji instalacja

### 3. W razie błędu "file_put_contents"

```bash
docker-compose exec app chmod -R 775 bootstrap/cache
```
```bash
docker-compose exec app chmod -R 775 storage
```
```bash
docker-compose exec app php artisan config:clear
```
```bash
docker-compose exec app php artisan cache:clear
```
```bash
docker-compose exec app php artisan view:clear
```
```bash
docker-compose exec app php artisan serve
```

### 4. Aplikacja działa pod adresem:

```
http://127.0.0.1:8000
```

### 5. Scheduler + Queue

```bash
docker-compose exec app php artisan queue:work
```
```bash
docker-compose exec app php artisan tasks:send-reminders
```

---