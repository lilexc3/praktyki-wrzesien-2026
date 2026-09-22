<?php
require_once __DIR__ . '/../includes/functions.php';

$examId = input_id('id');
$sql = 'SELECT e.*, q.symbol, q.name AS qualification_name, a.name AS area_name
        FROM exams e
        JOIN qualifications q ON q.id = e.qualification_id
        JOIN areas a ON a.id = q.area_id
        WHERE e.id = ?';
$exam = query($sql, [$examId])->fetch_assoc();

if (!$exam) {
    not_found('Nie znaleziono materiału.');
}

$title = $exam['symbol'] . ' · ' . $exam['year'] . ' ' . $exam['session'];
$details = [
    'area_name' => 'Obszar',
    'symbol' => 'Kwalifikacja',
    'year' => 'Rok',
    'session' => 'Sesja',
    'exam_type' => 'Rodzaj egzaminu',
    'exam_number' => 'Numer arkusza',
    'source_name' => 'Źródło',
    'verified_at' => 'Data weryfikacji',
];
$pdfIsAvailable = local_file($exam['pdf_path']) !== null;
$hasSourceLink = $exam['source_url'] !== '';

require ROOT . '/includes/header.php';
?>

<section class="panel">
    <div class="page-heading">
        <span class="document-icon"><?= icon('file') ?></span>
        <div>
            <span class="eyebrow dark"><?= e($exam['symbol']) ?> / MATERIAŁ EGZAMINACYJNY</span>
            <h1><?= e($exam['title']) ?></h1>
            <p><?= e($exam['qualification_name']) ?></p>
        </div>
    </div>

    <?php if ($exam['is_sample']): ?>
        <div class="notice">
            Dane przykładowe — ten rekord służy do prezentacji serwisu i nie jest oficjalnym arkuszem.
        </div>
    <?php endif; ?>

    <div class="detail-grid">
        <div>
            <h2>Informacje o materiale</h2>
            <dl>
                <?php foreach ($details as $key => $label): ?>
                    <?php
                    $value = $exam[$key];

                    if ($key === 'verified_at') {
                        $value = verification_date($value);
                    }
                    ?>
                    <div>
                        <dt><?= e($label) ?></dt>
                        <dd><?= e($value) ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <p class="description"><?= nl2br(e($exam['description'])) ?></p>
            <a class="text-link" href="<?= e(url('serwis/qualification.php?symbol=' . $exam['symbol'])) ?>">
                Wszystkie arkusze <?= e($exam['symbol']) ?>
                <?= icon('arrow') ?>
            </a>
        </div>

        <div class="download-panel">
            <h2>Pliki do pobrania</h2>
            <p>Materiały zapisane lokalnie, dostępne także bez internetu.</p>

            <div class="download-list">
                <?php file_buttons($exam); ?>
            </div>

            <?php if ($pdfIsAvailable): ?>
                <?php $pdfUrl = url('serwis/download.php?exam=' . $exam['id'] . '&kind=pdf&view=1'); ?>
                <a class="text-link" target="_blank" rel="noopener" href="<?= e($pdfUrl) ?>">
                    Otwórz PDF w przeglądarce ↗
                </a>
            <?php endif; ?>

            <?php if ($hasSourceLink): ?>
                <hr>
                <h3>Źródło materiału</h3>
                <a class="text-link" target="_blank" rel="noopener noreferrer" href="<?= e($exam['source_url']) ?>">
                    <?= e($exam['source_name']) ?> ↗
                </a>
                <p class="small">Otwarcie strony źródłowej wymaga internetu.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require ROOT . '/includes/footer.php'; ?>
