<?php
// Jednorazowy import bazy. Uruchom w terminalu: php tools/import.php
$config = require __DIR__ . '/../config/database.php';
$name = $config['name'];

if ($name === '' || !ctype_alnum(str_replace('_', '', $name))) {
    exit("Nieprawidłowa nazwa bazy.\n");
}

$conn = new mysqli($config['host'], $config['user'], $config['password'], '', $config['port']);

if ($conn->connect_error) {
    exit("Błąd połączenia z bazą.\n");
}

$conn->set_charset('utf8mb4');
$tables = $conn->query("SHOW TABLES FROM `$name`");

if ($tables && $tables->num_rows > 0) {
    exit("Baza zawiera już tabele. Import przerwany.\n");
}

$sql = file_get_contents(__DIR__ . '/../database/baza.sql');
$sql = str_replace('egzamin_zsm3_archiwum', $name, $sql);

if ($conn->multi_query($sql)) {
    while ($conn->more_results()) {
        $conn->next_result();
    }

    if ($conn->error) {
        echo "Błąd importu: " . $conn->error . "\n";
    } else {
        echo "Zaimportowano bazę $name.\n";
    }
} else {
    echo "Błąd importu: " . $conn->error . "\n";
}
