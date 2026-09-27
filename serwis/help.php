<?php require_once __DIR__ . '/../includes/functions.php';
 $title='Pomoc i instrukcja';
 require ROOT . '/includes/header.php';
 ?>
<section class="panel prose">
<span class="eyebrow dark">KROK PO KROKU</span>
<h1>Jak korzystać z archiwum?</h1>
<p>Wybierz temat i znajdź odpowiedź.</p>
<details open>
<summary>Wyszukiwanie materiałów</summary>
<p>Wpisz nazwę obszaru, symbol lub nazwę kwalifikacji, rok, sesję, rodzaj egzaminu albo numer arkusza. Możesz łączyć słowa: <strong>INF.03 2025</strong>, <strong>Informatyka 2025</strong>, <strong>praktyczny</strong>. Każde wpisane słowo zawęża wyniki. Kliknij tytuł materiału, aby otworzyć szczegóły.</p>
</details>
<details>
<summary>Przeglądanie i filtrowanie</summary>
<p>Na stronie głównej wybierz obszar, a następnie kwalifikację. Arkusze teoretyczne i praktyczne mają osobne sekcje. Ustaw rok, sesję, rodzaj lub numer i kliknij „Filtruj”. „Wyczyść” przywraca pełną listę. Adres strony zawiera ustawione filtry — można go zapisać w zakładkach.</p>
</details>
<details>
<summary>Pobieranie i otwieranie plików</summary>
<p>„Arkusz PDF” pobiera arkusz, a „Odpowiedzi” — klucz lub zasady oceniania. Pozostałe przyciski prowadzą do załączników. W szczegółach wybierz „Otwórz PDF w przeglądarce”, aby go przeczytać. „Plik niedostępny” oznacza, że administrator nie dodał pliku lub plik został usunięty.</p>
</details>
<details>
<summary>Teoria i praktyka</summary>
<p>Część teoretyczna sprawdza wiedzę zawodową. Część praktyczna sprawdza wykonanie zadania, np. przygotowanie aplikacji, konfigurację sprzętu lub opracowanie dokumentacji. Zawsze sprawdź instrukcję w konkretnym arkuszu.</p>
</details>
<details>
<summary>Panel administratora</summary>
<p>Otwórz „Panel administratora” i zaloguj się. W zakładce „Arkusze” wybierz „Dodaj arkusz”, uzupełnij opis, kwalifikację, rok, sesję i źródło. Przeczytaj pliki przed dodaniem i podaj rzeczywistą datę sprawdzenia. PDF i odpowiedzi przyjmują PDF; załączniki także ZIP, RAR, 7Z, DOC, DOCX, XLS i XLSX. Limit: 20 MB na plik. Przy edycji można wymienić i usunąć pliki. Po pierwszym logowaniu zmień hasło.</p>
</details>
<details>
<summary>Brak wyników lub problem z połączeniem</summary>
<p>Usuń część filtrów lub użyj samego symbolu kwalifikacji. Przy błędzie bazy sprawdź, czy działa MySQL i czy zaimportowano baza.sql. Problemy z plikami zgłoś administratorowi. Strony źródłowe wymagają internetu, ale lokalne pliki nie.</p>
</details>
</section><?php require ROOT . '/includes/footer.php';
 ?>
