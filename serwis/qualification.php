<?php
require_once __DIR__ . '/../includes/functions.php';

$symbol = input('symbol');

if ($symbol !== '') {
    $qualification = query('SELECT * FROM qualifications WHERE symbol = ?', [$symbol])->fetch_assoc();
} else {
    $qualificationId = input_id('id');
    $qualification = query('SELECT * FROM qualifications WHERE id = ?', [$qualificationId])->fetch_assoc();
}

if (!$qualification) {
    not_found('Nie znaleziono kwalifikacji.');
}

$area = query('SELECT * FROM areas WHERE id = ?', [$qualification['area_id']])->fetch_assoc();
$title = $qualification['symbol'];

// Id kwalifikacji jest zawsze stały na tej stronie.
$f = filters(['qualification' => (string)$qualification['id']]);
$hidden = ['symbol' => $qualification['symbol']];
$sections = [
    'teoretyczny' => 'Część teoretyczna',
    'praktyczny' => 'Część praktyczna',
];

require ROOT . '/includes/header.php';
?>

<section class="panel qualification-heading">
    <span class="symbol" style="--area:<?= e($area['color']) ?>">
        <?= e($qualification['symbol']) ?>
    </span>
    <h1><?= e($qualification['name']) ?></h1>
    <p><?= e($qualification['description']) ?></p>
    <a class="text-link" href="<?= e(url('serwis/area.php?id=' . $area['id'])) ?>">
        <?= icon($area['icon']) ?>
        <?= e($area['name']) ?>
    </a>
</section>

<section class="panel">
    <div class="section-heading">
        <h2>Materiały egzaminacyjne</h2>
        <span class="muted">Wybierz rok i sesję</span>
    </div>

    <?php require ROOT . '/includes/filter_form.php'; ?>

    <?php foreach ($sections as $examType => $sectionTitle): ?>
        <?php
        // Gdy użytkownik wybrał jeden typ, nie pokazujemy drugiej sekcji.
        if ($f['type'] !== '' && $f['type'] !== $examType) {
            continue;
        }

        $sectionFilters = $f;
        $sectionFilters['type'] = $examType;
        $total = find_exams($sectionFilters, 20, 0, true);

        $pageParameter = $examType . '_page';
        $page = input_id($pageParameter);

        if ($page < 1) {
            $page = 1;
        }

        $pageCount = max(1, (int)ceil($total / 20));

        if ($page > $pageCount) {
            $page = $pageCount;
        }

        $offset = ($page - 1) * 20;
        $exams = find_exams($sectionFilters, 20, $offset);
        ?>
        <h3 class="group-title">
            <?= icon($examType === 'praktyczny' ? 'gear' : 'book') ?>
            <?= e($sectionTitle) ?>
            <span class="count"><?= e($total) ?></span>
        </h3>

        <?php require ROOT . '/includes/exam_table.php'; ?>

        <?php if ($pageCount > 1): ?>
            <div class="pagination">
                <?php for ($number = 1; $number <= $pageCount; $number++): ?>
                    <?php
                    $parameters = array_filter($_GET, 'is_string');
                    $parameters[$pageParameter] = $number;
                    $pageUrl = '?' . http_build_query($parameters);
                    $currentPageAttribute = '';

                    if ($page === $number) {
                        $currentPageAttribute = ' aria-current="page"';
                    }
                    ?>
                    <a href="<?= e($pageUrl) ?>"<?= $currentPageAttribute ?>><?= e($number) ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>

<?php require ROOT . '/includes/footer.php'; ?>
