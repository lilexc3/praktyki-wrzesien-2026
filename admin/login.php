<?php require_once __DIR__ . '/../includes/auth.php';

if (!empty($_SESSION['admin_id'])) redirect('admin/');

$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();

    if (($_SESSION['login_wait'] ?? 0) > time()) $error='Zbyt wiele prób. Spróbuj ponownie za minutę.';

    else {
        $admin=query('SELECT * FROM admins WHERE username=?',[input('username',$_POST)])->fetch_assoc();

        if ($admin && password_verify(input('password',$_POST),$admin['password_hash'])) {
            session_regenerate_id(true);
 $_SESSION['admin_id']=$admin['id'];
 $_SESSION['last_active']=time();
 $_SESSION['csrf']=bin2hex(random_bytes(32));
 unset($_SESSION['login_attempts'],$_SESSION['login_wait']);
 redirect('admin/');

        }
        $_SESSION['login_attempts']=($_SESSION['login_attempts'] ?? 0)+1;

        if ($_SESSION['login_attempts']>=5) { $_SESSION['login_wait']=time()+60;
 $_SESSION['login_attempts']=0;
 }
        $error='Nieprawidłowy login lub hasło.';

    }
}
$title='Logowanie';
 require ROOT . '/includes/header.php';
 ?>
<section class="panel login-panel">
<span class="brand-icon"><?= icon('shield') ?></span>
<span class="eyebrow dark">STREFA ADMINISTRATORA</span>
<h1>Witaj ponownie</h1>
<p>Zaloguj się, aby zarządzać archiwum.</p><?php if($error): ?><div class="notice error" role="alert"><?= e($error) ?></div><?php endif;
 ?><form method="post"><?= csrf() ?><label>Login<input name="username" autocomplete="username" maxlength="60" required value="<?= e(input('username',$_POST)) ?>">
</label>
<label>Hasło<input type="password" name="password" autocomplete="current-password" required>
</label>
<button class="button">Zaloguj się <?= icon('arrow') ?></button>
</form>
</section><?php require ROOT . '/includes/footer.php';
 ?>
