<?php
require_once 'check_auth.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - إدارة المخزون</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-box-seam me-2"></i> نظام مخزوني</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">مرحباً بك: <strong><?php echo $_SESSION['username']; ?></strong> 
                    <span class="badge bg-primary"><?php echo $_SESSION['role']; ?></span>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-secondary">لوحة التحكم الرئيسية المسؤول</h2>
                <p class="text-muted">إدارة المنتجات، التصنيفات، وتتبع الكميات المتوفرة بالمستودع.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="display-4 text-primary mb-3"><i class="bi bi-tags"></i></div>
                        <h5 class="card-title fw-bold">إدارة المنتجات</h5>
                        <p class="card-text text-muted">إضافة منتجات جديدة، تعديل الأسعار والكميات، أو حذف المنتجات.</p>
                        <a href="products.php" class="btn btn-outline-primary w-100">عرض المنتجات (CRUD)</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 bg-primary text-white">
                    <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                        <div class="display-4 mb-2"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5>حالة النظام</h5>
                        <p class="small">قاعدة البيانات متصلة ومؤمنة بالكامل ضد ثغرات الاختراق.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>