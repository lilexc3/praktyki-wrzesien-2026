<?php require_once __DIR__ . '/../includes/auth.php';
 require_admin();
 $title='Arkusze egzaminacyjne';
 $f=filters();
 $total=find_exams($f,20,0,true);
 $page=min(max(1,(int)input('page')),max(1,(int)ceil($total/20)));
 $exams=find_exams($f,20,($page-1)*20);
 require ROOT . '/includes/header.php';
 ?>
<div class="section-heading">
<div>
<h1>Arkusze egzaminacyjne</h1>
<p><?= $total ?> materiałów w archiwum</p>
</div>
<a class="button" href="<?= e(url('admin/exam_add.php')) ?>">+ Dodaj arkusz</a>
</div>
<section class="panel">
<form class="big-search">
<input name="q" aria-label="Szukaj arkusza" placeholder="Szukaj arkusza…" value="<?= e($f['q']) ?>">
<button class="button">Szukaj</button>
</form><?php $hidden=['q'=>$f['q']];
 require ROOT . '/includes/filter_form.php';
 ?><div class="table-scroll">
<table>
<thead>
<tr>
<th>ID</th>
<th>Kwalifikacja / tytuł</th>
<th>Rok / sesja</th>
<th>Rodzaj</th>
<th>Numer</th>
<th>Akcje</th>
</tr>
</thead>
<tbody><?php foreach($exams as $exam): ?><tr>
<td><?= $exam['id'] ?></td>
<td>
<a href="<?= e(url('serwis/material.php?id='.$exam['id'])) ?>">
<strong><?= e($exam['symbol']) ?></strong> · <?= e($exam['title']) ?></a><?= $exam['is_sample'] ? '<small>Dane przykładowe</small>' : '' ?></td>
<td><?= $exam['year'] ?><small><?= e($exam['session']) ?></small>
</td>
<td><?= e($exam['exam_type']) ?></td>
<td><?= e($exam['exam_number']) ?></td>
<td>
<div class="actions">
<a href="<?= e(url('admin/exam_edit.php?id='.$exam['id'])) ?>">Edytuj</a>
<a class="delete" href="<?= e(url('admin/exam_delete.php?id='.$exam['id'])) ?>">Usuń</a>
</div>
</td>
</tr><?php endforeach;
 ?></tbody>
</table>
</div><?php if(!$exams): ?><div class="empty">Nie znaleziono arkuszy.</div><?php endif;
 pagination($total,$page);
 ?></section><?php require ROOT . '/includes/footer.php';
 ?>
