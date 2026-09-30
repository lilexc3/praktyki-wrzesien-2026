<?php

const ROOT = __DIR__ . '/..';

ini_set('display_errors', '0');

date_default_timezone_set('Europe/Warsaw');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);

if (session_status() === PHP_SESSION_NONE) session_start();

header('X-Content-Type-Options: nosniff');

header('X-Frame-Options: SAMEORIGIN');

header('Referrer-Policy: strict-origin-when-cross-origin');

// Zamienia tekst na bezpieczny HTML. Używamy jej przed wyświetleniem danych z bazy.
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Zwraca datę weryfikacji albo prostą informację, gdy daty jeszcze nie ma.
function verification_date($date) {
    if ($date === null || $date === '') {
        return 'Jeszcze nie zweryfikowano';
    }

    return $date;
}
function input($key, $data = null) {
    if ($data === null) {
        $data = $_GET;
    }

    if (!isset($data[$key]) || !is_string($data[$key])) {
        return '';
    }

    return trim($data[$key]);

}
function input_id($key, $data = null) {
    $value = input($key, $data);

    if (!ctype_digit($value)) {
        return 0;
    }

    return (int)$value;

}
function url($path = '') {
    $script = '/index.php';

    if (isset($_SERVER['SCRIPT_NAME'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    }

    $base = dirname($script);

    if (in_array(basename($base), ['serwis', 'admin'])) {
        $base = dirname($base);
    }

    return rtrim($base, '/.') . '/' . ltrim($path, '/');

}
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}
function db() {
    static $conn;

    if (!$conn) {
        $c = require ROOT . '/config/database.php';

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli($c['host'], $c['user'], $c['password'], $c['name'], $c['port']);

        if ($conn->connect_error) {
            exit('Nie można połączyć się z bazą danych.');
        }

        $conn->set_charset('utf8mb4');

    }
    return $conn;

}
function query($sql, $params = []) {
    $statement = db()->prepare($sql);

    if ($params) {
        $types = '';

        foreach ($params as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        $statement->bind_param($types, ...$params);
    }

    $statement->execute();
    $result = $statement->get_result();

    return $result === false ? true : $result;
}

function show_error($error) {
    error_log($error->getMessage());
    http_response_code(500);
    echo '<!doctype html><html lang="pl"><meta charset="utf-8"><title>Przerwa techniczna</title><body><h1>Nie można teraz wyświetlić strony</h1><p>Sprawdź połączenie z bazą danych oraz instrukcję instalacji w README.txt. Szczegóły zapisano w dzienniku PHP.</p></body></html>';
}

set_exception_handler('show_error');

function csrf() {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';

}
function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', input('csrf', $_POST)) || empty($_SESSION['csrf'])) {
        http_response_code(403);
 exit('Sesja formularza wygasła. Wróć do strony i spróbuj ponownie.');

    }
}
function flash($message) {
    $_SESSION['flash'] = $message;
}
function not_found($message = 'Nie znaleziono strony.') {
    http_response_code(404);
 $title = 'Nie znaleziono';
 require ROOT . '/includes/header.php';

    echo '<section class="panel empty"><h1>' . e($message) . '</h1><a class="button" href="' . e(url()) . '">Strona główna</a></section>';

    require ROOT . '/includes/footer.php';
 exit;

}
function areas() {
    $sql = 'SELECT a.*, COUNT(q.id) AS total
            FROM areas a
            LEFT JOIN qualifications q ON q.area_id = a.id
            GROUP BY a.id
            ORDER BY a.id';

    return query($sql)->fetch_all(MYSQLI_ASSOC);
}

function qualifications() {
    $sql = 'SELECT * FROM qualifications ORDER BY symbol';

    return query($sql)->fetch_all(MYSQLI_ASSOC);
}
function qualification_count($count) {
    if ($count === 1) {
        return '1 kwalifikacja';
    }

    $ending = $count % 10;
    $teens = $count % 100;

    if ($ending >= 2 && $ending <= 4 && ($teens < 12 || $teens > 14)) {
        return $count . ' kwalifikacje';
    }

    return $count . ' kwalifikacji';

}
function icon($name, $class = '') {
    $paths = [
        'cap'=>'<path d="m2 9 10-5 10 5-10 5L2 9Zm4 3v6c4 3 8 3 12 0v-6M2 9v8"/>',
        'home'=>'<path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/>',
        'monitor'=>'<rect x="3" y="3" width="18" height="13" rx="2"/><path d="M8 21h8m-4-5v5"/>',
        'gear'=>'<path d="m9 3-1 3-3 1-2 3 2 2-1 3 3 2 2 4h5l1-3 3-1 3-4-2-2 1-3-3-2-2-3H9Z"/><circle cx="12" cy="12" r="3"/>',
        'car'=>'<path d="m5 8 2-5h10l2 5M3 9h18v9H3V9Zm2 9v3m14-3v3M6 13h2m8 0h2"/>',
        'plane'=>'<path d="m12 2 2 8 8 5v2l-8-2v5l3 2H7l3-2v-5l-8 2v-2l8-5 2-8Z"/>',
        'ship'=>'<path d="M8 10V5h8v5m-4-5V2M3 12l9-3 9 3-4 7H7l-4-7Zm-1 9c2 2 4 0 6 0s2 2 4 0 4 0 6 0 2 2 4 0"/>',
        'search'=>'<circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/>',
        'file'=>'<path d="M5 2h9l5 5v15H5V2Zm9 0v6h5M8 12h8m-8 4h6"/>',
        'download'=>'<path d="M12 3v12m-5-5 5 5 5-5M3 15v6h18v-6"/>',
        'arrow'=>'<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'info'=>'<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10v1"/>',
        'book'=>'<path d="M12 5C8 2 4 3 2 4v16c4-2 7-1 10 1 3-2 6-3 10-1V4c-4-2-7-1-10 1Zm0 0v16"/>',
        'shield'=>'<path d="m12 2 9 4v6c0 5-5 8-9 10-4-2-9-5-9-10V6l9-4Zm-5 9 3 3 7-7"/>',
        'menu'=>'<path d="M3 5h18M3 12h18M3 19h18"/>',
        'grid'=>'<path d="M3 3h7v7H3Zm11 0h7v7h-7ZM3 14h7v7H3Zm11 0h7v7h-7Z"/>',
    ];

    return '<svg class="icon ' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['file']) . '</svg>';

}
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

    $additionalFiles = query('SELECT * FROM exam_files WHERE exam_id = ?', [$exam['id']]);
    $additionalFiles = $additionalFiles->fetch_all(MYSQLI_ASSOC);

    foreach ($additionalFiles as $file) {
        if (!local_file($file['file_path'])) {
            continue;
        }

        $downloadUrl = url('serwis/download.php?file=' . $file['id']);

        echo '<a class="file-button" href="' . e($downloadUrl) . '">';
        echo icon('download');
        echo e($file['file_name']);
        echo '</a> ';
    }
}
function filters($fixed = []) {
    $result = [];
    $filterNames = ['q', 'qualification', 'year', 'session', 'type', 'number'];

    foreach ($filterNames as $name) {
        if (isset($fixed[$name])) {
            $result[$name] = $fixed[$name];
        } else {
            $result[$name] = input($name);
        }
    }

    $result['q'] = mb_substr($result['q'], 0, 150);

    $yearIsInvalid = $result['year'] !== '' && (!ctype_digit($result['year']) || (int)$result['year'] < 2000 || (int)$result['year'] > 2100);

    if ($yearIsInvalid) {
        $result['year'] = '';
    }

    if (!in_array($result['session'], ['', 'styczeń', 'czerwiec', 'lipiec'], true)) {
        $result['session'] = '';
    }

    if (!in_array($result['type'], ['', 'teoretyczny', 'praktyczny'], true)) {
        $result['type'] = '';
    }

    if ($result['qualification'] !== '' && !ctype_digit((string)$result['qualification'])) {
        $result['qualification'] = '';
    }

    $result['number'] = mb_substr($result['number'], 0, 20);

    return $result;

}
function find_exams($filters, $limit = 20, $offset = 0, $count = false) {
    $conditions = [];
    $params = [];
    $filterColumns = [
        'qualification' => 'qualification_id',
        'year' => 'year',
        'session' => 'session',
        'type' => 'exam_type',
        'number' => 'exam_number',
    ];

    // Te nazwy kolumn pochodzą z kodu, nie z adresu URL.
    foreach ($filterColumns as $filterName => $columnName) {
        $value = $filters[$filterName] ?? '';

        if ($value === '') {
            continue;
        }

        $conditions[] = 'e.' . $columnName . ' = ?';
        $params[] = $value;
    }

    $searchText = $filters['q'] ?? '';
    $words = preg_split('/\s+/u', $searchText, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($words as $word) {
        $conditions[] = "CONCAT_WS(' ', a.name, q.symbol, q.name, e.year, e.session, e.exam_type, e.exam_number, e.title) LIKE ?";
        $params[] = '%' . str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $word) . '%';
    }

    if ($count) {
        $sql = 'SELECT COUNT(*)';
    } else {
        $sql = 'SELECT e.*, q.symbol, q.name AS qualification_name, a.color';
    }

    $sql .= ' FROM exams e
              JOIN qualifications q ON q.id = e.qualification_id
              JOIN areas a ON a.id = q.area_id';

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    if ($count) {
        $result = query($sql, $params);
        $row = $result->fetch_row();

        return (int)$row[0];
    }

    $safeLimit = max(1, $limit);
    $safeOffset = max(0, $offset);

    $sql .= " ORDER BY e.year DESC, FIELD(e.session, 'styczeń', 'czerwiec', 'lipiec') DESC, e.id DESC";
    $sql .= ' LIMIT ' . $safeLimit . ' OFFSET ' . $safeOffset;

    return query($sql, $params)->fetch_all(MYSQLI_ASSOC);

}
function selected($currentValue, $optionValue) {
    if ((string)$currentValue === (string)$optionValue) {
        return ' selected';
    }

    return '';
}
function pagination($total, $page, $size = 20) {
    $pages = (int)ceil($total/$size);
 if ($pages < 2) return;

    echo '<nav class="pagination" aria-label="Strony wyników">';

    for ($i=max(1,$page-2); $i<=min($pages,$page+2); $i++) {
        $params = [];
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                $params[$key] = $value;
            }
        }
 $params['page']=$i;

        echo '<a ' . ($i===$page ? 'aria-current="page" ' : '') . 'href="?' . e(http_build_query($params)) . '">' . $i . '</a>';

    } echo '</nav>';

}