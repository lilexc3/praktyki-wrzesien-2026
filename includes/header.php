<?php
if (!isset($title)) {
    $title = 'Strona główna';
}

$adminPage = str_contains($_SERVER['SCRIPT_NAME'], '/admin/');
$allAreas = areas();
$flashMessage = '';
$topbarLabel = 'TWOJA BAZA WIEDZY';

if ($adminPage) {
    $topbarLabel = 'PANEL ADMINISTRATORA';
}

if (isset($_SESSION['flash'])) {
    $flashMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c2034">
    <title><?= e($title) ?> • EGZAMIN-ARCHIWUM ZSM3</title>
    <link rel="icon" href="<?= e(url('assets/img/zsm3.png')) ?>" type="image/png">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">

    <?php if ($adminPage): ?>
        <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
    <?php endif; ?>

    <script src="<?= e(url('assets/js/app.js')) ?>" defer></script>
</head>
<body>
    <a class="skip-link" href="#main">Przejdź do treści</a>

    <?php require ROOT . '/includes/sidebar.php'; ?>

    <div class="workspace">
        <header class="topbar">
            <button class="menu-toggle" aria-label="Otwórz menu" aria-expanded="false" aria-controls="sidebar">
                <?= icon('menu') ?>
            </button>

            <span class="topbar-label"><?= $topbarLabel ?></span>

            <form class="top-search" action="<?= e(url('serwis/search.php')) ?>">
                <label class="sr-only" for="top-q">Szukaj materiałów</label>
                <?= icon('search') ?>
                <input id="top-q" name="q" placeholder="Szukaj kwalifikacji, symbolu, arkusza…"
                    value="<?= e(input('q')) ?>">
                <button aria-label="Szukaj">↵</button>
            </form>

            <span class="local-status"><i></i> Serwis lokalny</span>
            <img class="avatar" src="<?= e(url('assets/img/zsm3.png')) ?>" alt="Zespół Szkół Mechanicznych nr 3" width="42" height="42">
        </header>

        <main id="main">
            <div class="breadcrumb">
                <a href="<?= e(url()) ?>">Strona główna</a>

                <?php if ($title !== 'Strona główna'): ?>
                    <span>/</span>
                    <?= e($title) ?>
                <?php endif; ?>
            </div>

            <?php if ($flashMessage !== ''): ?>
                <div class="notice success" role="status">
                    <?= e($flashMessage) ?>
                </div>
            <?php endif; ?>
