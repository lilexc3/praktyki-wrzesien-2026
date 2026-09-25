<?php
require_once __DIR__ . '/functions.php';

// Zapisuje jeden plik przesłany z formularza administratora.
// Zwraca null, gdy użytkownik nie wybrał pliku.
function save_upload($file, $folder, $pdfOnly = false) {
    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Nie udało się przesłać pliku. Sprawdź limit uploadu w php.ini.');
    }

    $fileSize = $file['size'];

    if ($fileSize <= 0 || $fileSize > 20 * 1024 * 1024) {
        throw new RuntimeException('Plik musi mieć od 1 bajta do 20 MB.');
    }

    $originalName = $file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedMimeTypes = [
        'pdf' => ['application/pdf'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/x-rar', 'application/x-rar-compressed', 'application/vnd.rar'],
        '7z' => ['application/x-7z-compressed'],
        'doc' => ['application/msword', 'application/CDFV2', 'application/x-ole-storage'],
        'xls' => ['application/vnd.ms-excel', 'application/CDFV2', 'application/x-ole-storage'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ],
    ];

    if (!isset($allowedMimeTypes[$extension])) {
        throw new RuntimeException('Niedozwolony format pliku.');
    }

    if ($pdfOnly && $extension !== 'pdf') {
        throw new RuntimeException('Arkusz i odpowiedzi muszą być plikami PDF.');
    }

    $temporaryPath = $file['tmp_name'];

    if (!is_uploaded_file($temporaryPath)) {
        throw new RuntimeException('Nieprawidłowy plik uploadu.');
    }

    $detectedMimeType = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);

    if (!in_array($detectedMimeType, $allowedMimeTypes[$extension], true)) {
        throw new RuntimeException('Zawartość pliku nie pasuje do rozszerzenia: ' . $extension . '.');
    }

    // DOCX i XLSX są archiwami ZIP, więc sprawdzamy ich podstawową strukturę.
    if ($extension === 'docx' || $extension === 'xlsx') {
        validate_office_document($temporaryPath, $extension);
    }

    validate_upload_folder($folder);

    $directory = ROOT . '/' . $folder;

    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Nie można utworzyć katalogu uploadu.');
    }

    $generatedName = create_upload_name($folder, $extension);
    $relativePath = $folder . '/' . $generatedName;
    $destinationPath = ROOT . '/' . $relativePath;

    if (!move_uploaded_file($temporaryPath, $destinationPath)) {
        throw new RuntimeException('Nie można zapisać pliku. Sprawdź uprawnienia katalogu uploads.');
    }

    return [
        'path' => $relativePath,
        'name' => mb_substr(basename(str_replace('\\', '/', $originalName)), 0, 200),
        'type' => $extension,
    ];
}

function validate_office_document($temporaryPath, $extension) {
    $zip = new ZipArchive();

    if ($zip->open($temporaryPath) !== true) {
        throw new RuntimeException('Nieprawidłowy dokument Office.');
    }

    $officeFile = 'word/document.xml';

    if ($extension === 'xlsx') {
        $officeFile = 'xl/workbook.xml';
    }

    $hasContentTypesFile = $zip->locateName('[Content_Types].xml') !== false;
    $hasMainOfficeFile = $zip->locateName($officeFile) !== false;
    $zip->close();

    if (!$hasContentTypesFile || !$hasMainOfficeFile) {
        throw new RuntimeException('Nieprawidłowa struktura dokumentu Office.');
    }
}

function validate_upload_folder($folder) {
    $folderPattern = '~^uploads/[A-Z]{2,4}\.[0-9]{2}/[0-9]{4}/(styczen|czerwiec|lipiec)/(praktyczny|teoretyczny)$~D';

    if (!preg_match($folderPattern, $folder)) {
        throw new RuntimeException('Nieprawidłowy katalog plików.');
    }
}

function create_upload_name($folder, $extension) {
    $folderPart = str_replace(['uploads/', '/', '.'], ['', '_', ''], $folder);
    $randomPart = bin2hex(random_bytes(8));

    return $folderPart . '_' . $randomPart . '.' . $extension;
}

// Usuwa plik tylko wtedy, gdy żaden materiał już go nie używa.
function remove_unused_file($path) {
    if (!$path) {
        return;
    }

    $usedInExam = query(
        'SELECT id FROM exams WHERE pdf_path = ? OR answers_path = ? LIMIT 1',
        [$path, $path]
    )->fetch_assoc();
    $usedAsAdditionalFile = query(
        'SELECT id FROM exam_files WHERE file_path = ? LIMIT 1',
        [$path]
    )->fetch_assoc();

    if ($usedInExam || $usedAsAdditionalFile) {
        return;
    }

    $filePath = local_file($path);

    if ($filePath && !unlink($filePath)) {
        error_log('Nie usunięto nieużywanego pliku: ' . $path);
    }
}
