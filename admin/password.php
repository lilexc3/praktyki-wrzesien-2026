<?php require_once __DIR__ . '/../includes/auth.php';
 require_admin();
 $error='';

if($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();
 $old=input('old',$_POST);
 $new=input('new',$_POST);

    $result=query('SELECT password_hash FROM admins WHERE id=?',[$_SESSION['admin_id']]);
 $row=$result->fetch_assoc();
 $hash=$row['password_hash'];

    if(!password_verify($old,$hash)) $error='Aktualne hasło jest nieprawidłowe.';

    elseif(strlen($new)<10 || strlen($new)>72) $error='Nowe hasło musi mieć od 10 do 72 bajtów.';

    elseif($new !== input('repeat',$_POST)) $error='Nowe hasła nie są identyczne.';

    else { query('UPDATE admins SET password_hash=? WHERE id=?',[password_hash($new,PASSWORD_DEFAULT),$_SESSION['admin_id']]);
 session_regenerate_id(true);
 flash('Hasło zostało zmienione.');
 redirect('admin/');
 }
}
$title='Zmiana hasła';
 require ROOT . '/includes/header.php';
 ?><section class="panel login-panel">
<h1>Zmień hasło</h1><?php if($error): ?><div class="notice error"><?= e($error) ?></div><?php endif;
 ?><form method="post"><?= csrf() ?><label>Aktualne hasło<input type="password" name="old" autocomplete="current-password" required>
</label>
<label>Nowe hasło<input type="password" name="new" autocomplete="new-password" minlength="10" maxlength="72" required>
</label>
<label>Powtórz nowe hasło<input type="password" name="repeat" autocomplete="new-password" required>
</label>
<button class="button">Zapisz hasło</button>
</form>
</section><?php require ROOT . '/includes/footer.php';
 ?>
