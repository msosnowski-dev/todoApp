# Wykonanie zadania – ToDo App (Laravel 11)

## Zakres wykonanych funkcji

### Główne wymagania

- [x] Pełne CRUD z walidacją (zadanie: tytuł, opis, status, priorytet, termin)
- [x] Filtrowanie zadań (status, priorytet, termin)
- [x] Scheduler + Queue: powiadomienie e-mail na 1 dzień przed terminem
- [x] System logowania / rejestracji (Breeze)
- [x] Uwierzytelnienie użytkowników + dostęp tylko do własnych zadań
- [x] Publiczne zadanie (token + data wygaśnięcia)
- [x] REST API (Laravel Sanctum)
- [x] Obsługa AJAX (usuwanie zadania, podgląd modalu)

### Zadania opcjonalne

- [x] Paginacja wyników
- [x] Historia edycji zadania (wersjonowanie danych)
- [x] Integracja z Google Calendar (`spatie/laravel-google-calendar`)
- [x] Obsługa błędów (brak dostępu, brak pliku json, brak ID kalendarza)
- [x] Konfiguracja Docker (`Dockerfile`, `docker-compose.yml`)
- [x] Gotowe pliki `README.md`, `TODO.md`, `DONE.md`

## Uwagi techniczne

- Do wersjonowania wykorzystano osobny model `TaskVersion` 
- Wysyłka powiadomień odbywa się przez komendę artisan lub harmonogram
- Publiczne linki zostały podzielone na dwa typy: w pełni publiczne oraz dostępne wyłącznie dla zalogowanych użytkowników
- W API zastosowano `auth:sanctum` oraz `Bearer` `token`

## Uruchomienie

Wszystkie informacje zawarte są w `README.md`. Projekt można uruchomić lokalnie lub przez Dockera.