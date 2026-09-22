<?php

function local_file($path) {
    if (!$path || !str_starts_with($path, 'uploads/')) return null;

    $base = realpath(ROOT . '/uploads');
 $file = realpath(ROOT . '/' . $path);

    if (!$base || !$file || !is_file($file) || !str_starts_with(str_replace('\\','/',$file), str_replace('\\','/',$base) . '/')) return null;

    return $file;

}
function print_exam_file_button($examId, $kind, $label, $className = '') {
    $downloadUrl = url('serwis/download.php?exam=' . $examId . '&kind=' . $kind);

    echo '<a class="file-button ' . e($className) . '" href="' . e($downloadUrl) . '">';
    echo icon('download');
    echo e($label);
    echo '</a> ';
}

function file_buttons($exam) {
    $pdfFile = local_file($exam['pdf_path'] ?? null);

    if ($pdfFile) {
        print_exam_file_button($exam['id'], 'pdf', 'Arkusz PDF', 'pdf');
    } else {
        echo '<span class="muted small">Plik niedostępny</span> ';
    }

    $answersFile = local_file($exam['answers_path'] ?? null);

    if ($answersFile) {
        print_exam_file_button($exam['id'], 'answers', 'Odpowiedzi', 'answers');
    }

}
