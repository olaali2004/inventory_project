<?php
// تفعيل إظهار الأخطاء لحل أي مشكلة فوراً
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بدء الجلسة بأمان لحماية الصفحات وتتبع المستخدمين
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); 
    session_start();
}

$host = 'localhost';
$db_name = 'inventory_db'; 
$username = 'root';        
$password = '';            

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>