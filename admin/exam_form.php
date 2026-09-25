<?php
require_once __DIR__ . '/../includes/auth.php';
 require_admin();

require_once ROOT . '/includes/uploads.php';

if (!isset($examId)) redirect('admin/exams.php');

$existing = $examId ? query('SELECT * FROM exams WHERE id=?',[$examId])->fetch_assoc() : null;

if ($examId && !$existing) not_found('Nie znaleziono arkusza.');

$data = $existing ?: ['qualification_id'=>'','year'=>date('Y'),'session'=>'czerwiec','exam_type'=>'praktyczny','exam_number'=>'01','title'=>'','description'=>'','source_name'=>'','source_url'=>'','verified_at'=>'','is_sample'=>0,'pdf_path'=>null,'answers_path'=>null];

$error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    check_csrf();

    foreach(['qualification_id','year','session','exam_type','exam_number','title','description','source_name','source_url','verified_at'] as $key) $data[$key]=input($key,$_POST);

    $data['is_sample']=input('is_sample',$_POST)==='1'?1:0;

    $newPaths=[];
 $oldPaths=[];

    $transaction = false;

    try {
        $qualification=query('SELECT * FROM qualifications WHERE id=?',[$data['qualification_id']])->fetch_assoc();

        if (!$qualification) throw new RuntimeException('Wybierz kwalifikację.');

        if (!ctype_digit($data['year']) || (int)$data['year']<2000 || (int)$data['year']>2100) throw new RuntimeException('Rok musi być liczbą od 2000 do 2100.');

        if (!in_array($data['session'],['styczeń','czerwiec','lipiec'],true) || !in_array($data['exam_type'],['teoretyczny','praktyczny'],true)) throw new RuntimeException('Wybierz prawidłową sesję i rodzaj egzaminu.');

        if (!preg_match('/^[A-Za-z0-9-]{1,20}$/D',$data['exam_number'])) throw new RuntimeException('Numer arkusza: 1–20 liter, cyfr lub myślników.');

        foreach(['title'=>255,'source_name'=>150] as $key=>$max) if ($data[$key]==='' || mb_strlen($data[$key])>$max) throw new RuntimeException('Uzupełnij tytuł i źródło. Tytuł: do 255 znaków, źródło: do 150 znaków.');

        if (mb_strlen($data['description'])>10000) throw new RuntimeException('Opis jest za długi (maks. 10000 znaków).');

        if ($data['source_url']!=='' && (strlen($data['source_url'])>1000 || !filter_var($data['source_url'],FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($data['source_url'],PHP_URL_SCHEME) ?? ''),['http','https'],true))) throw new RuntimeException('Adres źródła musi być poprawnym adresem http lub https.');

        if ($data['verified_at']!=='') {
            $date=DateTime::createFromFormat('!Y-m-d',$data['verified_at']);

            if (!$date || $date->format('Y-m-d')!==$data['verified_at'] || $data['verified_at']>date('Y-m-d')) throw new RuntimeException('Podaj prawidłową datę weryfikacji, nie późniejszą niż dzisiaj.');

        } elseif (!$data['is_sample']) throw new RuntimeException('Dla rzeczywistego materiału podaj datę weryfikacji.');

        $same = query('SELECT id FROM exams WHERE qualification_id=? AND year=? AND session=? AND exam_type=? AND exam_number=? AND id<>?', [
            $data['qualification_id'], $data['year'], $data['session'], $data['exam_type'], $data['exam_number'], $examId ?: 0
        ])->fetch_assoc();

        if ($same) throw new RuntimeException('Taki arkusz już istnieje.');

        $folder='uploads/'.$qualification['symbol'].'/'.$data['year'].'/'.str_replace('ń','n',$data['session']).'/'.$data['exam_type'];

        // Pliki zapisujemy przed transakcją, a w razie błędu usuwamy nowe kopie.
        foreach(['pdf_path'=>'pdf','answers_path'=>'answers'] as $field=>$name) {
            $upload=save_upload($_FILES[$name] ?? [],$folder,true);

            if ($upload) { $newPaths[]=$upload['path'];
 $oldPaths[]=$data[$field];
 $data[$field]=$upload['path'];
 }
            elseif (input('remove_'.$name,$_POST)==='1') { $oldPaths[]=$data[$field];
 $data[$field]=null;
 }
        }
        $additional=[];

        if (isset($_FILES['additional']['name']) && is_array($_FILES['additional']['name'])) {
            if (count($_FILES['additional']['name'])>10) throw new RuntimeException('Możesz przesłać najwyżej 10 załączników jednocześnie.');

            foreach($_FILES['additional']['name'] as $i=>$name) {
                $file=[];
 foreach(['name','tmp_name','size','error'] as $key) $file[$key]=$_FILES['additional'][$key][$i];

                $upload=save_upload($file,$folder);

                if($upload) { $additional[]=$upload;
 $newPaths[]=$upload['path'];
 }
            }
        }
        db()->begin_transaction();
        $transaction = true;

        $fields=['qualification_id','year','session','exam_type','exam_number','title','description','source_name','source_url','verified_at','is_sample','pdf_path','answers_path'];

        $values=[];
 foreach($fields as $field) $values[]=$field==='verified_at' && $data[$field]==='' ? null : $data[$field];

        if ($examId) { $values[]=$examId;
 $set=[];
 foreach($fields as $field) $set[]=$field.'=?';
 query('UPDATE exams SET '.implode(',',$set).' WHERE id=?',$values);
 }
        else { query('INSERT INTO exams ('.implode(',',$fields).') VALUES ('.implode(',',array_fill(0,count($fields),'?')).')',$values);
 $examId=(int)db()->insert_id;
 }
        $remove=$_POST['remove_files'] ?? [];

        if(is_array($remove)) foreach($remove as $id) {
            if(!is_scalar($id)) continue;

            $file=query('SELECT * FROM exam_files WHERE id=? AND exam_id=?',[(int)$id,$examId])->fetch_assoc();

            if($file) { $oldPaths[]=$file['file_path'];
 query('DELETE FROM exam_files WHERE id=?',[$file['id']]);
 }
        }
        foreach($additional as $file) query('INSERT INTO exam_files (exam_id,file_name,file_path,file_type) VALUES (?,?,?,?)',[$examId,$file['name'],$file['path'],$file['type']]);

        db()->commit();
        $transaction = false;

    } catch(Throwable $ex) {
        if($transaction) db()->rollback();

        foreach($newPaths as $path) { $file=local_file($path);
 if($file) unlink($file);
 }
        $data['pdf_path']=$existing['pdf_path'] ?? null;
 $data['answers_path']=$existing['answers_path'] ?? null;

        if($ex instanceof RuntimeException) $error=$ex->getMessage();

        else { error_log((string)$ex);
 $error='Nie zapisano materiału. Sprawdź pliki i spróbuj ponownie.';
 }
    }
    if (!$error) { foreach($oldPaths as $path) remove_unused_file($path);
 flash('Arkusz został zapisany.');
 redirect('admin/exams.php');
 }
}
$title=$existing?'Edycja arkusza':'Dodawanie arkusza';
 require ROOT . '/includes/header.php';

?><div class="section-heading">
<div>
<span class="eyebrow dark">BAZA MATERIAŁÓW</span>
<h1><?= $existing?'Edytuj arkusz':'Dodaj nowy arkusz egzaminacyjny' ?></h1>
</div>
<a class="text-link" href="<?= e(url('admin/exams.php')) ?>">← Wróć do listy</a>
</div>
<section class="panel"><?php if($error): ?><div class="notice error" role="alert"><?= e($error) ?> Jeśli wybierano pliki, wybierz je ponownie.</div><?php endif;
 ?><form method="post" enctype="multipart/form-data"><?= csrf() ?><input type="hidden" name="MAX_FILE_SIZE" value="20971520">
<div class="form-grid">
<div>
<label>Kwalifikacja *<select name="qualification_id" required>
<option value="">Wybierz kwalifikację</option><?php foreach(qualifications() as $q): ?><option value="<?= $q['id'] ?>"<?= selected($data['qualification_id'],$q['id']) ?>><?= e($q['symbol'].' — '.$q['name']) ?></option><?php endforeach;
 ?></select>
</label>
<br>
<div class="form-row">
<label>Rok *<input type="number" name="year" min="2000" max="2100" value="<?= e($data['year']) ?>" required>
</label>
<label>Sesja *<select name="session"><?php foreach(['styczeń','czerwiec','lipiec'] as $s): ?><option<?= selected($data['session'],$s) ?>><?= $s ?></option><?php endforeach;
 ?></select>
</label>
<label>Rodzaj egzaminu *<select name="exam_type"><?php foreach(['teoretyczny','praktyczny'] as $s): ?><option<?= selected($data['exam_type'],$s) ?>><?= $s ?></option><?php endforeach;
 ?></select>
</label>
</div>
<br>
<label>Numer arkusza *<input name="exam_number" value="<?= e($data['exam_number']) ?>" maxlength="20" required>
</label>
<br>
<label>Tytuł *<input name="title" value="<?= e($data['title']) ?>" maxlength="255" required>
</label>
<br>
<label>Opis<textarea name="description" maxlength="10000"><?= e($data['description']) ?></textarea>
</label>
<label class="check">
<input type="checkbox" name="is_sample" value="1"<?= $data['is_sample']?' checked':'' ?>> Dane przykładowe (nieoficjalne)</label>
</div>
<div><?php foreach(['pdf'=>['Arkusz PDF','pdf_path'],'answers'=>['Odpowiedzi PDF','answers_path']] as $name=>[$label,$field]): ?><div class="upload-box">
<label><?= $label ?><input type="file" name="<?= $name ?>" accept=".pdf,application/pdf">
<span class="field-note">PDF, maksymalnie 20 MB. Nowy plik zastąpi poprzedni.</span>
</label><?php if($data[$field]): ?><p class="small">Aktualnie: <?= e(basename($data[$field])) ?></p>
<label class="check">
<input type="checkbox" name="remove_<?= $name ?>" value="1">Usuń aktualny plik</label><?php endif;
 ?></div><?php endforeach;
 ?><div class="upload-box">
<label>Pliki dodatkowe<input type="file" name="additional[]" multiple accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx">
<span class="field-note">Do 10 plików. PDF, ZIP, RAR, 7Z, DOC(X), XLS(X). Każdy do 20 MB.</span>
</label><?php if($existing): foreach(query('SELECT * FROM exam_files WHERE exam_id=?',[$existing['id']])->fetch_all(MYSQLI_ASSOC) as $file): ?><label class="check">
<input type="checkbox" name="remove_files[]" value="<?= $file['id'] ?>"> Usuń: <?= e($file['file_name']) ?></label><?php endforeach;
 endif;
 ?></div>
<label>Nazwa źródła *<input name="source_name" maxlength="150" value="<?= e($data['source_name']) ?>" placeholder="np. CKE" required>
</label>
<br>
<label>Link do źródła<input type="url" name="source_url" maxlength="1000" value="<?= e($data['source_url']) ?>" placeholder="https://…">
</label>
<br>
<label>Data sprawdzenia zawartości<input type="date" name="verified_at" value="<?= e($data['verified_at']) ?>" max="<?= date('Y-m-d') ?>">
<span class="field-note">Wymagana dla rzeczywistych materiałów.</span>
</label>
</div>
</div>
<div class="form-actions">
<button class="button">Zapisz arkusz</button>
<a class="button secondary" href="<?= e(url('admin/exams.php')) ?>">Anuluj</a>
</div>
</form>
</section><?php require ROOT . '/includes/footer.php';
 ?>
