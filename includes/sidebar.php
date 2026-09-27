<?php
if (!isset($adminPage)) {
    $adminPage = false;
}

if (!isset($allAreas)) {
    $allAreas = areas();
}

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$informationPages = [
    ['file' => 'about', 'icon' => 'info', 'label' => 'O serwisie'],
    ['file' => 'sources', 'icon' => 'book', 'label' => 'Źródła materiałów'],
    ['file' => 'help', 'icon' => 'shield', 'label' => 'Pomoc i instrukcja'],
];
$adminPages = [
    ['file' => 'index', 'label' => 'Panel główny'],
    ['file' => 'exams', 'label' => 'Arkusze'],
    ['file' => 'qualifications', 'label' => 'Kwalifikacje'],
    ['file' => 'password', 'label' => 'Zmień hasło'],
];
$adminIsLoggedIn = $adminPage && isset($_SESSION['admin_id']);
?>

<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= e(url()) ?>">
        <img class="brand-logo" src="<?= e(url('assets/img/zsm3.png')) ?>" alt="Emblemat ZSM3 w Krakowie" width="58" height="58">
        <span>
            EGZAMIN
            <span class="brand-sub">ARCHIWUM ZSM3</span>
        </span>
    </a>

    <div class="sidebar-caption">STREFA UCZNIA</div>
    <nav aria-label="Nawigacja główna">
        <?php
        $homeClass = '';

        if ($currentPage === 'index.php' && !$adminPage) {
            $homeClass = 'active';
        }
        ?>
        <a class="nav-link <?= $homeClass ?>" href="<?= e(url()) ?>">
            <?= icon('home') ?>
            Strona główna
        </a>

        <?php foreach ($allAreas as $navigationArea): ?>
            <?php
            $areaClass = '';

            if (isset($area) && $area['id'] === $navigationArea['id']) {
                $areaClass = 'active';
            }

            $shortAreaName = explode(' i ', $navigationArea['name'])[0];
            $areaUrl = url('serwis/area.php?id=' . $navigationArea['id']);
            ?>
            <a class="nav-link <?= $areaClass ?>" href="<?= e($areaUrl) ?>">
                <?= icon($navigationArea['icon']) ?>
                <?= e($shortAreaName) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-caption divider">WARTO WIEDZIEĆ</div>
    <nav aria-label="Informacje">
        <?php foreach ($informationPages as $informationPage): ?>
            <?php
            $informationPageFile = $informationPage['file'] . '.php';
            $informationClass = '';

            if ($currentPage === $informationPageFile) {
                $informationClass = 'active';
            }

            $informationUrl = url('serwis/' . $informationPageFile);
            ?>
            <a class="nav-link <?= $informationClass ?>" href="<?= e($informationUrl) ?>">
                <?= icon($informationPage['icon']) ?>
                <?= e($informationPage['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ($adminIsLoggedIn): ?>
        <div class="sidebar-caption divider">ADMINISTRACJA</div>
        <nav aria-label="Administracja">
            <?php foreach ($adminPages as $adminNavigationPage): ?>
                <?php $adminUrl = url('admin/' . $adminNavigationPage['file'] . '.php'); ?>
                <a class="nav-link" href="<?= e($adminUrl) ?>">
                    <?= icon('grid') ?>
                    <?= e($adminNavigationPage['label']) ?>
                </a>
            <?php endforeach; ?>

            <form action="<?= e(url('admin/logout.php')) ?>" method="post">
                <?= csrf() ?>
                <button class="nav-link">
                    <?= icon('arrow') ?>
                    Wyloguj się
                </button>
            </form>
        </nav>
    <?php endif; ?>

    <div class="sidebar-bottom">
        <div class="offline-card">
            <?= icon('shield') ?>
            <strong>Ucz się we własnym tempie</strong>
            <p>Wszystkie zapisane pliki masz pod ręką. Także bez internetu.</p>
        </div>

        <a class="nav-link" href="<?= e(url('admin/')) ?>">
            <?= icon('gear') ?>
            Panel administratora
        </a>

        <small>PROJEKT PRAKTYK ZAWODOWYCH<br>ZSM3 · Archiwum egzaminów</small>
    </div>
</aside>
