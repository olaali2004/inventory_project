<?php
require_once 'config.php';

try {
    // التحقق أولاً إذا كان حساب الـ admin موجوداً لمنع التكرار
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // تشفير كلمة المرور admin123 بشكل آمن (Password Hashing)
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        
        // إدخال حساب المسؤول (Admin) في جدول المستخدمين
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $insert->execute([
            'username' => 'admin',
            'email'    => 'admin@test.com',
            'password' => $hashed_password,
            'role'     => 'admin'
        ]);
        echo "<h3 style='color:green; text-align:center; margin-top:50px;'>تم إنشاء حساب المسؤول (admin) بنجاح في قاعدة البيانات!</h3>";
    } else {
        echo "<h3 style='color:orange; text-align:center; margin-top:50px;'>الحساب موجود بالفعل في قاعدة البيانات!</h3>";
    }
} catch (PDOException $e) {
    echo "حدث خطأ أثناء إنشاء الحساب: " . $e->getMessage();
}
?>