<?php require_once __DIR__ . '/../includes/auth.php';
 require_admin();
 $title='Panel administratora';
 require ROOT . '/includes/header.php';
 ?>
<div class="section-heading admin-intro">
<div>
<span class="eyebrow dark">ZARZĄDZANIE ARCHIWUM</span>
<h1>Wszystko pod kontrolą.</h1>
<p>Dodawaj materiały, uzupełniaj źródła i rozwijaj bazę wiedzy uczniów.</p>
</div>
<a class="button" href="<?= e(url('admin/exam_add.php')) ?>">+ Dodaj arkusz</a>
</div>
<div class="admin-stats"><?php foreach(['areas'=>['grid','Obszary'],'qualifications'=>['cap','Kwalifikacje'],'exams'=>['book','Arkusze']] as $table=>[$ico,$label]): $count=query("SELECT COUNT(*) FROM $table")->fetch_row(); ?><div class="panel"><?= icon($ico) ?><strong><?= $count[0] ?></strong>
<span class="muted"><?= $label ?></span>
</div><?php endforeach;
 ?><?php $fileCount=query('SELECT (SELECT COUNT(*) FROM exam_files)+(SELECT COUNT(pdf_path)+COUNT(answers_path) FROM exams)')->fetch_row(); ?><div class="panel"><?= icon('file') ?><strong><?= $fileCount[0] ?></strong>
<span class="muted">Zapisane ścieżki plików</span>
</div>
</div>
<section class="panel">
<h2>Szybki start</h2>
<div class="feature-grid">
<div><?= icon('file') ?><h3>Dodaj materiały</h3>
<p>Wybierz kwalifikację, sesję i załącz sprawdzone pliki.</p>
<a class="text-link" href="<?= e(url('admin/exams.php')) ?>">Zarządzaj arkuszami →</a>
</div>
<div><?= icon('cap') ?><h3>Uzupełnij kwalifikacje</h3>
<p>Popraw opis lub dodaj kolejną kwalifikację.</p>
<a class="text-link" href="<?= e(url('admin/qualifications.php')) ?>">Lista kwalifikacji →</a>
</div>
<div><?= icon('shield') ?><h3>Zabezpiecz konto</h3>
<p>Po instalacji zmień domyślne hasło administratora.</p>
<a class="text-link" href="<?= e(url('admin/password.php')) ?>">Zmień hasło →</a>
</div>
</div>
</section><?php require ROOT . '/includes/footer.php';
 ?>
