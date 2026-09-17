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

    <div class="sidebar-bottom">
        <div class="offline-card">
            <?= icon('shield') ?>
            <strong>Ucz się we własnym tempie</strong>
            <p>Wszystkie zapisane pliki masz pod ręką. Także bez internetu.</p>
        </div>


        <small>PROJEKT PRAKTYK ZAWODOWYCH<br>ZSM3 · Archiwum egzaminów</small>
    </div>
</aside>
