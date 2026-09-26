<?php
// Ten plik dostaje listę materiałów w zmiennej $exams.
// Gdy strona nie przekaże listy, pokazujemy bezpieczny pusty widok.
if (!isset($exams)) {
    $exams = [];
}
?>

<?php if (!$exams): ?>
    <div class="empty">
        <?= icon('search') ?>
        <h3>Nie znaleziono materiałów</h3>
        <p>Spróbuj zmienić wyszukiwaną frazę lub wybrać inne filtry.</p>
    </div>
<?php else: ?>
    <p class="table-scroll-hint">Przesuń tabelę w poziomie, aby zobaczyć wszystkie kolumny.</p>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Kwalifikacja / materiał</th>
                    <th>Rodzaj</th>
                    <th>Rok / sesja</th>
                    <th>Źródło / weryfikacja</th>
                    <th>Pliki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <?php
                    $materialUrl = url('serwis/material.php?id=' . $exam['id']);
                    $typeName = ucfirst($exam['exam_type']);
                    $sessionName = ucfirst($exam['session']);
                    $sampleNote = '';

                    if ($exam['is_sample']) {
                        $sampleNote = ' · Dane przykładowe';
                    }
                    ?>
                    <tr>
                        <td>
                            <a class="material-link" href="<?= e($materialUrl) ?>">
                                <span class="symbol" style="--area:<?= e($exam['color']) ?>">
                                    <?= e($exam['symbol']) ?>
                                </span>
                                <strong><?= e($exam['title']) ?></strong>
                            </a>
                            <small>Nr <?= e($exam['exam_number']) ?><?= e($sampleNote) ?></small>
                        </td>
                        <td>
                            <span class="type <?= e($exam['exam_type']) ?>">
                                <?= e($typeName) ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= e($exam['year']) ?></strong>
                            <small><?= e($sessionName) ?></small>
                        </td>
                        <td>
                            <?= e($exam['source_name']) ?>
                            <small><?= e(verification_date($exam['verified_at'])) ?></small>
                        </td>
                        <td>
                            <div class="file-actions">
                                <?php file_buttons($exam); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
