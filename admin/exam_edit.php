<?php
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$examId = input_id('id');

if ($examId < 1) not_found('Nie znaleziono arkusza.');

require __DIR__ . '/exam_form.php';

