<?php

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
