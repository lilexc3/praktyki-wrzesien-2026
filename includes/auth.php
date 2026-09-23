<?php
require_once __DIR__ . '/functions.php';

function require_admin() {
    if (empty($_SESSION['admin_id'])) redirect('admin/login.php');

    if (time() - ($_SESSION['last_active'] ?? 0) > 1800) {
        unset($_SESSION['admin_id']);
 flash('Sesja wygasła. Zaloguj się ponownie.');
 redirect('admin/login.php');

    }
    $_SESSION['last_active'] = time();

    header('Cache-Control: no-store');

}
