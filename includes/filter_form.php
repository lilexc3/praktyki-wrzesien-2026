<?php
// Strona może przekazać własne filtry lub ukryte wartości (np. symbol kwalifikacji).
if (!isset($f)) {
    $f = filters();
}

if (!isset($hidden)) {
    $hidden = [];
}

$availableYears = query('SELECT DISTINCT year FROM exams ORDER BY year DESC')->fetch_all(MYSQLI_ASSOC);
$sessions = ['styczeń', 'czerwiec', 'lipiec'];
$examTypes = ['teoretyczny', 'praktyczny'];
$showQualificationSelect = !isset($qualification);

// Przycisk „Wyczyść” zostawia symbol na stronie konkretnej kwalifikacji.
$resetUrl = strtok($_SERVER['REQUEST_URI'], '?');

if (!empty($hidden['symbol'])) {
    $resetUrl .= '?symbol=' . urlencode($hidden['symbol']);
}
?>

<form class="filters" method="get">
    <?php foreach ($hidden as $key => $value): ?>
        <input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>">
    <?php endforeach; ?>

    <?php if ($showQualificationSelect): ?>
        <label>
            Kwalifikacja
            <select name="qualification">
                <option value="">Wszystkie kwalifikacje</option>
                <?php foreach (qualifications() as $qualificationItem): ?>
                    <option value="<?= e($qualificationItem['id']) ?>"
                        <?= selected($f['qualification'], $qualificationItem['id']) ?>>
                        <?= e($qualificationItem['symbol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php endif; ?>

    <label>
        Rok
        <select name="year">
            <option value="">Wszystkie lata</option>
            <?php foreach ($availableYears as $year): ?>
                <option value="<?= e($year['year']) ?>" <?= selected($f['year'], $year['year']) ?>>
                    <?= e($year['year']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Sesja
        <select name="session">
            <option value="">Wszystkie sesje</option>
            <?php foreach ($sessions as $session): ?>
                <option value="<?= e($session) ?>" <?= selected($f['session'], $session) ?>>
                    <?= e($session) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Rodzaj egzaminu
        <select name="type">
            <option value="">Wszystkie rodzaje</option>
            <?php foreach ($examTypes as $examType): ?>
                <option value="<?= e($examType) ?>" <?= selected($f['type'], $examType) ?>>
                    <?= e($examType) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Numer arkusza
        <input name="number" value="<?= e($f['number']) ?>" placeholder="np. 01" maxlength="20">
    </label>

    <button class="button" type="submit">Filtruj</button>
    <a class="reset" href="<?= e($resetUrl) ?>">Wyczyść</a>
</form>
