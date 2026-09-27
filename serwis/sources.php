<?php require_once __DIR__ . '/../includes/functions.php';
 $title='Źródła materiałów';
 require ROOT . '/includes/header.php';
 ?>
<section class="panel prose">
<span class="eyebrow dark">WIARYGODNE MATERIAŁY</span>
<h1>Źródła i weryfikacja</h1>
<p>Archiwum korzysta przede wszystkim z materiałów Centralnej Komisji Egzaminacyjnej oraz zweryfikowanych archiwów. Każdy rzeczywisty materiał powinien mieć nazwę źródła, adres strony źródłowej, jeśli jest dostępny, i datę sprawdzenia zawartości.</p>
<h2>Gdzie szukać?</h2>
<ul>
<li>
<a href="https://cke.gov.pl/" target="_blank" rel="noopener noreferrer">Centralna Komisja Egzaminacyjna</a> — oficjalne informatory, zadania i materiały egzaminacyjne.</li>
<li>
<a href="https://arkusze.pl/" target="_blank" rel="noopener noreferrer">arkusze.pl</a> — archiwalne arkusze i zasady oceniania.</li>
<li>Inne źródła zaakceptowane przez nauczyciela — po sprawdzeniu pochodzenia i zawartości materiałów.</li>
</ul>
<h2>Jak sprawdzamy pliki?</h2>
<ol>
<li>Otwieramy plik i porównujemy symbol, rok, sesję, numer oraz rodzaj egzaminu z jego stroną tytułową.</li>
<li>Sprawdzamy, czy odpowiedzi i załączniki pasują do tego samego arkusza.</li>
<li>Zapisujemy lokalną kopię, nazwę źródła, adres i datę weryfikacji.</li>
</ol>
<div class="notice">Rekordy oznaczone „Dane przykładowe” służą do prezentacji aplikacji. Nie są oficjalnymi materiałami egzaminacyjnymi i nie mają zmyślonych źródeł ani dat weryfikacji.</div>
<h2>Rejestr źródeł w archiwum</h2>
<div class="table-scroll">
<table>
<thead>
<tr>
<th>Źródło</th>
<th>Materiały</th>
<th>Ostatnia weryfikacja</th>
</tr>
</thead>
<tbody><?php foreach(query('SELECT source_name,COUNT(*) AS total,MAX(verified_at) AS verified FROM exams GROUP BY source_name ORDER BY source_name')->fetch_all(MYSQLI_ASSOC) as $source): ?><tr>
<td><?= e($source['source_name']) ?></td>
<td><?= $source['total'] ?></td>
<td><?= e($source['verified'] ?: 'Nie dotyczy / brak weryfikacji') ?></td>
</tr><?php endforeach;
 ?></tbody>
</table>
</div>
<p>Adres konkretnego źródła znajduje się na stronie materiału. Linki zewnętrzne wymagają dostępu do internetu.</p>
</section><?php require ROOT . '/includes/footer.php';
 ?>
