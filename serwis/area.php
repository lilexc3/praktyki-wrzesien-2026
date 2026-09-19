<?php
require_once __DIR__ . '/../includes/functions.php';

$areaId = input_id('id');
$area = query('SELECT * FROM areas WHERE id = ?', [$areaId])->fetch_assoc();

if (!$area) {
    not_found('Nie znaleziono obszaru.');
}

$title = $area['name'];

$sql = 'SELECT q.*, COUNT(e.id) AS total
        FROM qualifications q
        LEFT JOIN exams e ON e.qualification_id = q.id
        WHERE q.area_id = ?
        GROUP BY q.id
        ORDER BY q.symbol';
$qualificationItems = query($sql, [$area['id']])->fetch_all(MYSQLI_ASSOC);

require ROOT . '/includes/header.php';
?>

<section class="page-heading" style="--area:<?= e($area['color']) ?>">
    <span class="area-icon"><?= icon($area['icon']) ?></span>
    <div>
        <span class="eyebrow dark">OBSZAR ZAWODOWY</span>
        <h1><?= e($area['name']) ?></h1>
        <p>Wybierz kwalifikację i przejdź do arkuszy egzaminacyjnych.</p>
    </div>
</section>

<div class="qualification-grid">
    <?php foreach ($qualificationItems as $qualificationItem): ?>
        <?php
        $qualificationUrl = url('serwis/qualification.php?symbol=' . $qualificationItem['symbol']);
        $materialCount = $qualificationItem['total'];
        ?>
        <a class="panel qualification-card" href="<?= e($qualificationUrl) ?>">
            <span class="symbol" style="--area:<?= e($area['color']) ?>">
                <?= e($qualificationItem['symbol']) ?>
            </span>
            <h2><?= e($qualificationItem['name']) ?></h2>
            <p><?= e($qualificationItem['description']) ?></p>
            <div class="area-bottom">
                <span><?= e($materialCount) ?> materiałów</span>
                <?= icon('arrow') ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php require ROOT . '/includes/footer.php'; ?>
