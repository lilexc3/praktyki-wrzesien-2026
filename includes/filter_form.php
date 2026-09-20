<?php
if (!isset($f)) { $f = filters(); }
if (!isset($hidden)) { $hidden = []; }
?>
<form class="filters" method="get">
    <?php foreach ($hidden as $key => $value): ?>
        <input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>">
    <?php endforeach; ?>
    <label>Rok
        <input type="number" name="year" value="<?= e($f['year']) ?>" min="2000" max="2100">
    </label>
    <button type="submit">Filtruj</button>
</form>
