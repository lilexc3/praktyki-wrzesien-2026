<?php require_once __DIR__ . '/../includes/functions.php';
 $title='O serwisie';
 require ROOT . '/includes/header.php';
 ?>
<section class="panel prose">
<span class="eyebrow dark">PROJEKT PRAKTYK ZAWODOWYCH</span>
<h1>Wiedza w jednym miejscu.</h1>
<p>
<strong>EGZAMIN-ARCHIWUM ZSM3</strong> to lokalny serwis dla uczniów Zespołu Szkół Mechanicznych nr 3. Powstał jako projekt praktyk zawodowych w zawodzie technik programista.</p>
<p>Archiwum porządkuje arkusze, odpowiedzi i materiały dodatkowe według obszaru, kwalifikacji, roku, sesji oraz rodzaju egzaminu. Pomaga szybko znaleźć materiały potrzebne do samodzielnej nauki.</p>
<div class="feature-grid">
<div><?= icon('shield') ?><h3>Działa lokalnie</h3>
<p>Po uruchomieniu na szkolnym serwerze zapisane materiały są dostępne w sieci lokalnej bez internetu.</p>
</div>
<div><?= icon('cap') ?><h3>Pięć obszarów</h3>
<p>Informatyka, mechatronika, motoryzacja, lotnictwo i logistyka — wszystkie symbole z polecenia szkoły.</p>
</div>
<div><?= icon('download') ?><h3>Łatwe przenoszenie</h3>
<p>Wystarczy skopiować katalog projektu i odtworzyć bazę danych na nowym komputerze.</p>
</div>
</div>
<h2>Materiały i odpowiedzialność</h2>
<p>Serwis jest szkolnym archiwum, nie oficjalnym serwisem CKE. Rekordy demonstracyjne są wyraźnie oznaczone. Przed nauką z materiału sprawdź jego źródło i datę weryfikacji. Administrator uzupełnia archiwum o sprawdzone pliki.</p>
<a href="<?= e(url('serwis/sources.php')) ?>">Dowiedz się więcej o źródłach →</a>
</section><?php require ROOT . '/includes/footer.php';
 ?>
