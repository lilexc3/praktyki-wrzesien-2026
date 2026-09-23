<?php require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405);
 header('Allow: POST');
 exit('Wyloguj się przyciskiem w menu.');
 }
check_csrf();
 $_SESSION=[];
 session_regenerate_id(true);
 redirect('admin/login.php');

