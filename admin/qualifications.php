<?php
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$error = '';
$editId = input_id('id');
$editedQualification = null;

if ($editId > 0) {
    $editedQualification = query('SELECT * FROM qualifications WHERE id = ?', [$editId])->fetch_assoc();
}

if (input('id') !== '' && !$editedQualification) {
    not_found('Nie znaleziono kwalifikacji.');
}

$formData = [
    'area_id' => '',
    'symbol' => '',
    'name' => '',
    'description' => '',
];

if ($editedQualification) {
    $formData = $editedQualification;
}

function area_exists($areaId) {
    $area = query('SELECT id FROM areas WHERE id = ?', [$areaId])->fetch_assoc();

    return $area !== null;
}

function valid_qualification_text($formData) {
    $nameLength = mb_strlen($formData['name']);
    $descriptionLength = mb_strlen($formData['description']);

    $hasValidName = $nameLength >= 5 && $nameLength <= 255;
    $hasValidDescription = $descriptionLength >= 5 && $descriptionLength <= 3000;

    return $hasValidName && $hasValidDescription;
}

function qualification_symbol_exists($symbol, $currentId) {
    $sql = 'SELECT id FROM qualifications WHERE symbol = ? AND id <> ?';
    $qualification = query($sql, [$symbol, $currentId])->fetch_assoc();

    return $qualification !== null;
}

function save_qualification($formData, $editId) {
    $values = [
        $formData['area_id'],
        $formData['symbol'],
        $formData['name'],
        $formData['description'],
    ];

    if ($editId > 0) {
        $values[] = $editId;
        $sql = 'UPDATE qualifications
                SET area_id = ?, symbol = ?, name = ?, description = ?
                WHERE id = ?';
        query($sql, $values);

        return;
    }

    $sql = 'INSERT INTO qualifications (area_id, symbol, name, description)
            VALUES (?, ?, ?, ?)';
    query($sql, $values);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $formData['area_id'] = input('area_id', $_POST);
    $formData['symbol'] = strtoupper(input('symbol', $_POST));
    $formData['name'] = input('name', $_POST);
    $formData['description'] = input('description', $_POST);

    if (!preg_match('/^[A-Z]{2,4}\.[0-9]{2}$/D', $formData['symbol'])) {
        $error = 'Podaj symbol w formacie INF.03.';
    } elseif (!area_exists($formData['area_id'])) {
        $error = 'Wybierz prawidłowy obszar.';
    } elseif (!valid_qualification_text($formData)) {
        $error = 'Uzupełnij nazwę (5–255 znaków) i opis (5–3000 znaków).';
    } elseif (qualification_symbol_exists($formData['symbol'], $editId)) {
        $error = 'Kwalifikacja o tym symbolu już istnieje.';
    } else {
        save_qualification($formData, $editId);
        flash('Kwalifikacja została zapisana.');
        redirect('admin/qualifications.php');
    }
}

$title = 'Kwalifikacje';
require ROOT . '/includes/header.php';

$formTitle = 'Dodaj kwalifikację';

if ($editedQualification) {
    $formTitle = 'Edytuj kwalifikację';
}
?>

<div class="section-heading">
    <h1>Kwalifikacje zawodowe</h1>
</div>

<section class="panel">
    <h2><?= e($formTitle) ?></h2>

    <?php if ($error !== ''): ?>
        <div class="notice error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <?= csrf() ?>
        <div class="form-grid">
            <label>
                Obszar
                <select name="area_id" required>
                    <option value="">Wybierz obszar</option>
                    <?php foreach ($allAreas as $areaOption): ?>
                        <option value="<?= e($areaOption['id']) ?>"
                            <?= selected($formData['area_id'], $areaOption['id']) ?>>
                            <?= e($areaOption['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Symbol
                <input name="symbol" value="<?= e($formData['symbol']) ?>" placeholder="INF.03" maxlength="7" required>
            </label>

            <label class="wide">
                Pełna nazwa
                <input name="name" value="<?= e($formData['name']) ?>" maxlength="255" required>
            </label>

            <label class="wide">
                Opis
                <textarea name="description" maxlength="3000" required><?= e($formData['description']) ?></textarea>
            </label>
        </div>

        <div class="form-actions">
            <button class="button">Zapisz kwalifikację</button>

            <?php if ($editedQualification): ?>
                <a class="button secondary" href="<?= e(url('admin/qualifications.php')) ?>">Anuluj</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="panel">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Symbol</th>
                    <th>Nazwa</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (qualifications() as $qualificationItem): ?>
                    <tr>
                        <td><strong><?= e($qualificationItem['symbol']) ?></strong></td>
                        <td><?= e($qualificationItem['name']) ?></td>
                        <td>
                            <a class="text-link" href="?id=<?= e($qualificationItem['id']) ?>">Edytuj</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require ROOT . '/includes/footer.php'; ?>
