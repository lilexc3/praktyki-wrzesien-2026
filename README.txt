EGZAMIN – ARCHIWUM ZSM3
Projekt praktyk zawodowych – technik programista, klasa IV
=======================================================

1. CO ZAWIERA PROJEKT
Lokalna aplikacja PHP z bazą MySQL/MariaDB, wyszukiwarką, filtrami,
stronami kwalifikacji, pobieraniem plików i panelem administratora.
Interfejs nie korzysta z CDN, zewnętrznych fontów ani bibliotek JavaScript.

W bazie jest 5 obszarów i 19 kwalifikacji.

Materiały startowe:
- 15 rekordów demonstracyjnych, bez fikcyjnych PDF-ów i adresów źródłowych;
- 15 rzeczywistych arkuszy praktycznych CKE z czerwca 2025, zadanie 01:
  INF.02, INF.03, INF.04, ELM.03, ELM.06, ELM.07, ELM.08, MOT.02, MOT.04,
  MOT.05, MOT.06, TLO.01, TLO.03, SPL.02 i SPL.03;
- 15 pasujących plików z zasadami oceniania;
- 1 ZIP z materiałami do zadania INF.03.
Materiały pochodzą z archiwum arkusze.pl. Źródła poszczególnych arkuszy
są zapisane w bazie i widoczne na stronach materiałów.
To początkowy zasób, a nie komplet wszystkich sesji i kwalifikacji.
Rzeczywiste arkusze teoretyczne i pozostałe materiały należy uzupełnić
po sprawdzeniu ich dostępności, treści i źródła. Nowe kwalifikacje lotnicze
nie mają tutaj zmyślonych historycznych arkuszy.

2. WYMAGANIA
- Windows lub Linux, Apache 2.4 z obsługą .htaccess i AllowOverride All;
- PHP 8.1 lub nowszy, rozszerzenia mysqli, mbstring, fileinfo, zip;
- MySQL 8 lub MariaDB 10.4+;
- aktualna przeglądarka;
- zapis do katalogu uploads i katalogu sesji PHP.

3. INSTALACJA XAMPP
Pobierz XAMPP z https://www.apachefriends.org/ i zainstaluj np. w C:\xampp.
Otwórz XAMPP Control Panel. W wierszu Apache kliknij Start.
W wierszu MySQL również kliknij Start (XAMPP może używać MariaDB).
Jeśli port 80 lub 3306 jest zajęty, sprawdź konfigurację usług i ich porty.
Uruchom http://localhost/ i http://localhost/phpmyadmin/.

4. KOPIOWANIE PROJEKTU
Skopiuj cały katalog EGZAMIN-ZSM3 do katalogu htdocs instalacji XAMPP:
C:\xampp\htdocs\EGZAMIN-ZSM3\index.php
Jeżeli XAMPP jest zainstalowany w innym miejscu, użyj jego katalogu `htdocs`.

5. IMPORT BAZY
W phpMyAdmin wybierz Import, wskaż database/baza.sql i wykonaj import.
Plik sam tworzy bazę egzamin_zsm3_archiwum i tabele oraz wprowadza dane.
Użyj kodowania UTF-8. Nie importuj pliku ponownie do działającej bazy:
nie służy on do aktualizacji, a zawarte w nim tabele już będą istniały.
Skrypt nie zawiera DROP TABLE ani DROP DATABASE.

Alternatywa dla phpMyAdmin (terminal w folderze projektu):
C:\xampp\php\php.exe tools\import.php
Narzędzie CLI odmawia importu, jeśli docelowa baza zawiera już tabele.

6. KONFIGURACJA POŁĄCZENIA
Otwórz config/database.php. Domyślnie:
host: 127.0.0.1
port: 3306
name: egzamin_zsm3_archiwum
user: root
password: pusty ciąg
Są to ustawienia lokalnego XAMPP. Jeśli masz inne, wpisz własne.
Na szkolnym serwerze najlepiej utworzyć osobnego użytkownika bazy mającego
uprawnienia SELECT, INSERT, UPDATE, DELETE tylko do tej bazy.

7. URUCHOMIENIE
Strona: http://localhost/EGZAMIN-ZSM3/
Panel:  http://localhost/EGZAMIN-ZSM3/admin/
Gdy Apache ma inny port, dodaj go do adresu, np. localhost:8080.
Nie otwieraj plików PHP dwuklikiem ani przez file://.

Do uruchomienia użyj zwykłej instalacji w htdocs opisanej wyżej.

8. LOGOWANIE ADMINISTRATORA
Login: admin
Hasło: admin123
Po pierwszym logowaniu wybierz Zmień hasło. Nowe hasło: 10–72 bajty.
W bazie jest tylko skrót hasła wygenerowany password_hash().
Sesja wygasa po 30 minutach bezczynności. Wylogowanie jest przyciskiem w menu.

9. DODAWANIE MATERIAŁÓW
Panel -> Arkusze -> Dodaj arkusz.
Wybierz kwalifikację, rok, sesję, rodzaj, numer i wpisz tytuł.
Dodaj nazwę źródła, adres (jeżeli dostępny), datę faktycznej weryfikacji.
Sprawdź zawartość arkusza, odpowiedzi i załączników przed wgraniem.
Rzeczywiste materiały wymagają daty weryfikacji. Dla próbnego rekordu zaznacz
„Dane przykładowe”. Możesz dodać rekord bez pliku: uczeń zobaczy „Plik niedostępny”.
Arkusz i odpowiedzi: PDF. Załączniki: PDF, ZIP, RAR, 7Z, DOC, DOCX, XLS, XLSX.
Limit jednego pliku: 20 MB. Jednocześnie można dodać do 10 załączników.

W php.ini ustaw (po zmianie uruchom Apache ponownie):
upload_max_filesize = 20M
post_max_size = 256M
max_file_uploads = 20
extension=zip
Większy post_max_size umożliwia przesłanie kilku plików naraz.
Gdy całe żądanie przekroczy limit PHP, formularz może zgłosić brak tokena sesji.
Rozszerzenie zip jest potrzebne do sprawdzenia dokumentów DOCX i XLSX.

10. STRUKTURA UPLOADU
uploads/INF.03/2025/czerwiec/praktyczny/nazwa_bezpieczna.pdf
Nazwy generowane podczas uploadu mają losowy dopisek, więc się nie nadpisują.
Sesja styczeń w nazwie katalogu ma zapis styczen.
W bazie zapisujemy wyłącznie ścieżki względne, zaczynające się od uploads/.
Załączniki ZIP/7Z nie są rozpakowywane ani wykonywane przez serwer.
Bezpośredni dostęp HTTP do uploads jest zablokowany przez .htaccess.
Pobieranie odbywa się przez serwis/download.php, który sprawdza ścieżkę i plik.
Nie usuwaj .htaccess. Przy Nginx trzeba odtworzyć te ograniczenia w konfiguracji.
Zmiana metadanych arkusza lub symbolu kwalifikacji nie przenosi dawnych plików;
ich zapisane ścieżki pozostają poprawne. Nowe uploady trafiają do nowego katalogu.
Usunięcie arkusza usuwa jego wpisy załączników i nieużywane pliki.

11. KOPIA ZAPASOWA
1) Na czas kopii wstrzymaj dodawanie, edycję i usuwanie materiałów.
2) W phpMyAdmin wybierz egzamin_zsm3_archiwum -> Eksport -> SQL.
3) Zapisz eksport jako np. kopia_2026-09-25.sql poza publicznym katalogiem.
4) Skopiuj cały katalog EGZAMIN-ZSM3, szczególnie uploads.
5) Przechowuj SQL i folder z tej samej chwili razem, np. na innym dysku.
6) Sprawdź odtworzenie kopii na osobnej instalacji.
database/baza.sql to dane początkowe. Nie zastępuje eksportu aktualnej bazy!

12. PRZENIESIENIE NA INNY KOMPUTER
Zainstaluj XAMPP, uruchom Apache i MySQL, skopiuj folder do htdocs.
Utwórz bazę o nazwie z config/database.php. Zaimportuj aktualny eksport SQL.
Przy pierwszej instalacji użyj database/baza.sql.
Ustaw dane połączenia i limity uploadu. Sprawdź zapis do uploads.
Otwórz stronę, wyszukaj INF.03, pobierz PDF i zaloguj się do panelu.
Żaden plik aplikacji nie wymaga stałej ścieżki typu C:\xampp.

13. SPRAWDZENIE INSTALACJI
Otwórz stronę, wyszukaj INF.03 i pobierz PDF.
Sprawdź logowanie administratora oraz dodawanie, edycję i usuwanie
próbnego materiału oznaczonego jako dane przykładowe.
