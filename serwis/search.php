<?php require_once __DIR__ . '/../includes/functions.php';
 $title='Wyszukiwarka';
 $f=filters();
 $total=find_exams($f,20,0,true);
 $page=min(max(1,(int)input('page')),max(1,(int)ceil($total/20)));
 $exams=find_exams($f,20,($page-1)*20);
 require ROOT . '/includes/header.php';
 ?>
<div class="section-heading">
<div>
<span class="eyebrow dark">ZNAJDŹ TO, CZEGO POTRZEBUJESZ</span>
<h1>Wyszukiwarka materiałów</h1>
<p>Symbol, rok, sesja lub nazwa kwalifikacji — wybór należy do Ciebie.</p>
</div>
</div>
<form class="big-search" method="get">
<label for="search-q" class="sr-only">Szukana fraza</label><?= icon('search') ?><input id="search-q" name="q" value="<?= e($f['q']) ?>" placeholder="np. INF.03 2025" maxlength="150">
<button class="button orange">Szukaj <?= icon('arrow') ?></button>
</form>
<section class="panel"><?php $hidden=['q'=>$f['q']];
 require ROOT . '/includes/filter_form.php';
 ?><div class="section-heading">
<h2>Wyniki wyszukiwania <span class="count"><?= $total ?></span>
</h2>
<span class="muted small"><?= $f['q'] ? 'Dla frazy: ' . e($f['q']) : 'Wszystkie materiały' ?></span>
</div><?php require ROOT . '/includes/exam_table.php';
 pagination($total,$page);
 ?></section><?php require ROOT . '/includes/footer.php';
 ?>
