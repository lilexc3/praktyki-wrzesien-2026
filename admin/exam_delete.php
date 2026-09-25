<?php require_once __DIR__ . '/../includes/auth.php';
 require_admin();
 require_once ROOT.'/includes/uploads.php';

$exam=query('SELECT * FROM exams WHERE id=?',[input_id('id')])->fetch_assoc();
 if(!$exam) not_found('Nie znaleziono arkusza.');

if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
 $paths=[$exam['pdf_path'],$exam['answers_path']];

    foreach(query('SELECT file_path FROM exam_files WHERE exam_id=?',[$exam['id']])->fetch_all(MYSQLI_ASSOC) as $file) $paths[]=$file['file_path'];

    query('DELETE FROM exams WHERE id=?',[$exam['id']]);

    foreach($paths as $path) remove_unused_file($path);

    flash('Arkusz i jego nieużywane pliki zostały usunięte.');
 redirect('admin/exams.php');

}
$title='Usuwanie arkusza';
 require ROOT . '/includes/header.php';
 ?><section class="panel">
<h1>Usunąć arkusz?</h1>
<p><?= e($exam['title']) ?> · <?= e($exam['year'].' '.$exam['session']) ?></p>
<div class="notice">Usunięcie obejmuje rekord, załączniki i pliki, z których nie korzystają inne materiały. Tej operacji nie można cofnąć.</div>
<form method="post"><?= csrf() ?><button class="button danger">Usuń arkusz i pliki</button>
<a class="button secondary" href="<?= e(url('admin/exams.php')) ?>">Anuluj</a>
</form>
</section><?php require ROOT . '/includes/footer.php';
 ?>
