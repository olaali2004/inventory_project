<?php
require_once 'config.php';

// إذا لم يكن هناك جلسة نشطة للمستخدم، يتم توجيهه لصفحة تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>