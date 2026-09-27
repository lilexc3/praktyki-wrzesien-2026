<?php require_once __DIR__ . '/../includes/functions.php';

$title='Strona główna';
 require ROOT . '/includes/header.php';

$examCount = query('SELECT COUNT(*) FROM exams')->fetch_row()[0];

$qualificationCount = query('SELECT COUNT(*) FROM qualifications')->fetch_row()[0];

?>
<section class="hero">
<div class="hero-copy">
<span class="eyebrow">
<i>
</i> TWOJE MIEJSCE NA DOBRY START</span>
<h1>Twój lokalny serwis<br>z <em>arkuszami<br class="mobile-break"> egzaminacyjnymi.</em>
</h1>
<p>Przygotuj się do egzaminów zawodowych —<br>szybko, wygodnie, bez internetu.</p>
<a class="button orange" href="#obszary">Zacznij przeglądać <?= icon('arrow') ?></a>
<div class="hero-note"><?= icon('shield') ?> Sprawdzone źródła. Uporządkowane materiały.</div>
</div>
<div class="hero-art" aria-hidden="true">
<div class="art-grid">
</div>
<div class="code-window">
<div class="window-bar">
<i>
</i>
<i>
</i>
<i>
</i>
<span>gotowy_na_egzamin.php</span>
</div>
<div class="code">
<span class="code-purple">&lt;?php</span>
<br>
<span class="code-muted">// Twój następny krok</span>
<br>
<span class="code-blue">$cel</span> = <span class="code-green">'zdany egzamin'</span>;<br>
<br>
<span class="code-purple">while</span> (<span class="code-blue">$uczyszSie</span>) {<br>&nbsp; <span class="code-yellow">rozwijajUmiejetnosci</span>();<br>&nbsp; <span class="code-yellow">rozwiazArkusz</span>();<br>}<br>
<br>
<span class="code-muted">// Dasz radę!</span>
<span class="cursor">▎</span>
</div>
</div>
<div class="floating-file"><?= icon('file') ?><span>Wiedza + praktyka<strong>Twój przepis na sukces</strong>
</span>
<b>✓</b>
</div>
</div>
</section>
<div class="stats-strip">
<div><?= icon('grid') ?><span>
<strong><?= count($allAreas) ?></strong> obszarów zawodowych</span>
</div>
<div><?= icon('cap') ?><span>
<strong><?= $qualificationCount ?></strong> kwalifikacji w jednym miejscu</span>
</div>
<div><?= icon('file') ?><span>
<strong><?= $examCount ?></strong> materiałów w archiwum</span>
</div>
<div><?= icon('shield') ?><span>Dostęp <strong>bez internetu</strong>
</span>
</div>
</div>
<section id="obszary">
<div class="section-heading">
<div>
<span class="eyebrow dark">WYBIERZ SWÓJ KIERUNEK</span>
<h2>Obszary zawodowe</h2>
<p>Zacznij od obszaru, w którym się kształcisz.</p>
</div>
<span class="muted small">5 obszarów · <?= $qualificationCount ?> kwalifikacji</span>
</div>
<div class="area-grid"><?php foreach($allAreas as $a): ?><a class="area-card" style="--area:<?= e($a['color']) ?>" href="<?= e(url('serwis/area.php?id=' . $a['id'])) ?>">
<span class="area-icon"><?= icon($a['icon']) ?></span>
<h3><?= e($a['name']) ?></h3>
<span class="area-count"><?= qualification_count((int)$a['total']) ?></span>
<div class="area-bottom">
<span>Przeglądaj materiały</span><?= icon('arrow') ?></div>
</a><?php endforeach;
 ?></div>
</section>
<section class="panel latest">
<div class="section-heading">
<div>
<h2>Najnowsze arkusze</h2>
<p>Materiały uporządkowane według roku i sesji.</p>
</div>
<a class="text-link" href="<?= e(url('serwis/search.php')) ?>">Zobacz wszystkie <?= icon('arrow') ?></a>
</div><?php $exams=find_exams([],6);
 require ROOT . '/includes/exam_table.php';
 ?></section>
<section class="help-banner">
<div><?= icon('book') ?><span>
<strong>Pierwszy raz w archiwum?</strong>
<small>Sprawdź, jak szukać materiałów i przygotować się do egzaminu.</small>
</span>
</div>
<a href="<?= e(url('serwis/help.php')) ?>">Poznaj serwis <?= icon('arrow') ?></a>
</section>
<?php require ROOT . '/includes/footer.php';
 ?>
