<?php
// تضمين ملف التحقق من تسجيل الدخول وقاعدة البيانات
require_once 'check_auth.php';

$message = '';
$error = '';

// 1. كود حذف المنتج (Delete) - يعمل عند الضغط على زر السلة الحمراء
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    try {
        // جلب اسم الصورة أولاً لحذفها من مجلد uploads لمنع تراكم الملفات
        $img_stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
        $img_stmt->execute(['id' => $delete_id]);
        $prod_img = $img_stmt->fetchColumn();
        
        if (!empty($prod_img) && file_exists("uploads/" . $prod_img)) {
            unlink("uploads/" . $prod_img); // حذف الصورة من المجلد
        }
        
        // حذف المنتج من قاعدة البيانات
        $del_stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $del_stmt->execute(['id' => $delete_id]);
        
        // إعادة توجيه لمنع تكرار العملية عند تحديث الصفحة
        header("Location: products.php?msg=تم حذف المنتج بنجاح!");
        exit;
    } catch (PDOException $e) {
        $error = "فشل حذف المنتج: " . $e->getMessage();
    }
}

// استقبال رسالة النجاح بعد الحذف التلقائي
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// 2. كود إضافة منتج جديد (Create) عند إرسال الفورم
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    // تنظيف البيانات المدخلة
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    
    // التحقق من رفع الصورة
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // التحقق من امتداد الصورة لأمان السيرفر
        if (in_array($ext, $allowed)) {
            // إنشاء اسم فريد للصورة لمنع تكرار الأسماء
            $image_name = time() . '_' . $filename;
            $target = 'uploads/' . $image_name;
            
            // نقل الصورة للمجلد المخصص
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        } else {
            $error = "امتداد الصورة غير مسموح به! برجاء رفع (JPG, PNG, GIF).";
        }
    }

    if (empty($name) || $price <= 0 || $quantity < 0) {
        $error = "برجاء إدخال بيانات صحيحة لجميع الحقول!";
    }

    // إذا لم يكن هناك أخطاء، يتم الحفظ في قاعدة البيانات
    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image) VALUES (:name, :price, :quantity, :image)");
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image_name
            ]);
            $message = "تم إضافة المنتج بنجاح!";
        } catch (PDOException $e) {
            $error = "فشل إضافة المنتج: " . $e->getMessage();
        }
    }
}

// 3. جلب وقراءة جميع المنتجات من قاعدة البيانات لعرضها في الجدول (Read)
$query = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتجات - CRUD</title>
    <!-- استدعاء وتنسيق ملفات Bootstrap والأيقونات -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-light">

    <!-- شريط التنقل العلوي -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="bi bi-arrow-right-circle me-2"></i> العودة للوحة التحكم
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- عرض التنبيهات ورسائل النجاح أو الخطأ -->
        <?php if(!empty($message)): ?> <div class="alert alert-success shadow-sm"><?php echo $message; ?></div> <?php endif; ?>
        <?php if(!empty($error)): ?> <div class="alert alert-danger shadow-sm"><?php echo $error; ?></div> <?php endif; ?>

        <div class="row g-4">
            
            <!-- جدول عرض المنتجات الحالية (جهة اليمين) -->
            <div class="col-md-8">
                <div class="card p-4 shadow-sm border-0">
                    <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-box-seam me-1"></i> قائمة المنتجات الحالية</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية المتاحة</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($products) == 0): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">لا توجد منتجات مضافة حالياً.</td></tr>
                                <?php else: ?>
                                    <?php foreach($products as $prod): ?>
                                        <tr>
                                            <td>
                                                <?php if(!empty($prod['image']) && file_exists("uploads/" . $prod['image'])): ?>
                                                    <img src="uploads/<?php echo $prod['image']; ?>" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted small">لا توجد صورة</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold"><?php echo $prod['name']; ?></td>
                                            <td class="text-success fw-bold">$ <?php echo number_format($prod['price'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php echo $prod['quantity'] > 5 ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $prod['quantity']; ?> قطعة
                                                </span>
                                            </td>
                                            <td>
                                                <!-- أزرار العمليات (التعديل والحذف) مع تمرير الـ ID المصلح -->
                                                <a href="edit_product.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-warning me-1">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="products.php?delete_id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا المنتج نهائياً؟');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- فورم إضافة منتج جديد (جهة اليسار) -->
            <div class="col-md-4">
                <div class="card p-4 shadow-sm border-0">
                    <h5 class="fw-bold text-primary mb-4"><i class="bi bi-plus-circle-fill me-1"></i> إضافة منتج جديد</h5>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">اسم المنتج</label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: تابلت ابل">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">السعر</label>
                            <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الكمية في المخزن</label>
                            <input type="number" name="quantity" class="form-control" required placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">صورة المنتج</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary w-100 mt-3 fw-bold">حفظ المنتج</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>
</html>