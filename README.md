Tematyka projektu: Portal biura podróży GlobeTrotter
Funkcjonalności:
1. System autoryzacji: Rejestracja i logowanie użytkowników z bezpiecznym
przechowywaniem danych.
2. Zarządzanie ofertami (CRUD): Pełna możliwość dodawania, edycji i
usuwania wycieczek przez administratora (w tym wgrywanie zdjęć ofert).
3. Role użytkowników:
○ Użytkownik: Przeglądanie oraz filtrowanie ofert, składanie rezerwacji,
dostęp do historii podróży oraz komunikacja z biurem.
○ Administrator: Zarządzanie bazą wycieczek, aktualizacja statusów
rezerwacji (oczekująca, opłacona, anulowana) oraz obsługa wiadomości
od klientów.
4. System rezerwacji: Składanie zamówień na wycieczki z automatycznym
przypisaniem do konta klienta.
5. Interakcje (Messenger): Zaawansowany system wiadomości prywatnych
pozwalający na dwustronną komunikację między klientem a biurem w czasie
rzeczywistym.
6. Interfejs użytkownika: Intuicyjny panel klienta podzielony na sekcje: Moje
Dane, Wiadomości oraz Historia Wycieczek.
Narzędzia i technologie:
Backend: Native PHP 8.1+ (architektura oparta na klasach i kontrolerach).
/classes – modele odpowiadające za logikę (Baza, User, Tour, Messenger).
/admin – moduły zarządzania dostępne tylko dla pracowników biura.
/includes – fragmenty interfejsu i widoki.
Baza danych: MySQL.
Frontend: HTML5, CSS3 (układ Flexbox i Grid), JavaScript.
Zastosowane środki bezpieczeństwa:
● Ochrona przed SQL Injection: Całość komunikacji z bazą danych realizowana
jest poprzez bibliotekę PDO przy użyciu Prepared Statements.
● Zabezpieczenie przed XSS: Wszystkie dane wyświetlane w przeglądarce są
filtrowane (funkcja htmlspecialchars), co zapobiega wstrzykiwaniu złośliwych
skryptów.
Zarządzanie sesją:
● HttpOnly: Flaga ciasteczek blokująca dostęp skryptów JS do identyfikatora
sesji.
● Regeneracja sesji: Zmiana ID sesji po zalogowaniu w celu uniknięcia Session
Fixation.
● Bezpieczeństwo haseł: Hasła są przechowywane w formie zahashowanej
(funkcja password_hash), co uniemożliwia ich odczytanie nawet w przypadku
wycieku bazy danych.
● Weryfikacja uprawnień: Każda akcja administracyjna (np. zmiana statusu
rezerwacji czy edycja wycieczki) jest weryfikowana po stronie serwera pod
kątem roli użytkownika.
● Bezpieczny upload zdjęć: Weryfikacja typu MIME wgrywanych grafik oraz
unikalne nazewnictwo plików w celu ochrony przed nadpisaniem danych.
Wymagania systemowe:
● Serwer WWW (np. Apache) z obsługą PHP 8.1 lub nowszym.
● Baza danych MySQL.
● Przeglądarka internetowa (Edge, Chrome, Firefox).
Instrukcja uruchomienia:
1. Skopiuj folder projektu projekt_koncowy do katalogu C:\xampp\htdocs\.
2. Upewnij się, że w folderze głównym istnieje katalog images/ z prawami do
zapisu zdjęć wycieczek.
3. Uruchom moduły Apache i MySQL w panelu XAMPP.
4. Stwórz nową bazę danych “globetrotter_db” i zaimportuj do niej plik .sql
5. Skonfiguruj parametry połączenia (host, user, pass) w pliku db_config.php.
6. Otwórz przeglądarkę i wejdź pod adres:
http://localhost/projekt_koncowy/index.php.
Konta testowe do weryfikacji:
● Administrator: Login: admin, Hasło: zaq123.
● Użytkownik: Login: tomasz, Hasło: Zaq123.
