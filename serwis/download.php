<?php require_once __DIR__ . '/../includes/functions.php';

if(input('file') !== '') { $row=query('SELECT file_path AS path,file_name AS name FROM exam_files WHERE id=?',[input_id('file')])->fetch_assoc();
 }
else { $kind=input('kind');
 if (!in_array($kind,['pdf','answers'])) not_found('Nie znaleziono pliku.');
 $column=$kind==='pdf'?'pdf_path':'answers_path';
 $row=query("SELECT $column AS path FROM exams WHERE id=?",[input_id('exam')])->fetch_assoc();
 }
$file=$row ? local_file($row['path']) : null;
 if (!$file) not_found('Plik niedostępny');

$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));

header('Content-Type: ' . ($ext==='pdf'?'application/pdf':'application/octet-stream'));

$downloadName = $row['name'] ?? '';

// Nazwa załącznika w bazie może być tylko etykietą (np. „Materiały do zadania — ZIP”).
// W takim przypadku używamy rzeczywistej nazwy pliku, aby pobrany plik zachował rozszerzenie.
if ($downloadName === '' || pathinfo($downloadName, PATHINFO_EXTENSION) === '') {
    $downloadName = basename($file);
}

$name=preg_replace('/[^a-zA-Z0-9._-]/','_', $downloadName);

header('Content-Disposition: ' . ($ext==='pdf' && input('view')==='1'?'inline':'attachment') . '; filename="' . $name . '"');

header('Content-Length: ' . filesize($file));
 session_write_close();
 readfile($file);
 exit;

