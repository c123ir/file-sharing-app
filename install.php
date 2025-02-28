<?php
/**
 * اسکریپت نصب و راه‌اندازی پایگاه داده
 */

// بارگذاری تنظیمات پیکربندی
require_once 'config/config.php';

// تنظیم هدر
header('Content-Type: text/html; charset=utf-8');

// بررسی درخواست نصب
$install = isset($_POST['install']) ? true : false;
$success = false;
$error = '';

if ($install) {
    try {
        // اتصال به سرور MySQL بدون انتخاب پایگاه داده
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // ایجاد پایگاه داده
        $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET ' . DB_CHARSET . ' COLLATE ' . DB_COLLATE);
        
        // انتخاب پایگاه داده
        $pdo->exec('USE ' . DB_NAME);
        
        // ایجاد جدول کاربران
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            is_admin TINYINT(1) DEFAULT 0,
            last_login DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE);
        
        // ایجاد جدول فایل‌ها
        $pdo->exec("CREATE TABLE IF NOT EXISTS files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_type ENUM('image', 'video', 'audio', 'document') NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            user_id INT NOT NULL,
            description TEXT,
            download_count INT DEFAULT 0,
            public TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE);
        
        // ایجاد جدول فایل‌های به اشتراک گذاشته شده
        $pdo->exec("CREATE TABLE IF NOT EXISTS shared_files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id INT NOT NULL,
            shared_by INT NOT NULL,
            shared_with INT NOT NULL,
            can_edit TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
            FOREIGN KEY (shared_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (shared_with) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE);
        
        // ایجاد جدول برچسب‌های فایل
        $pdo->exec("CREATE TABLE IF NOT EXISTS file_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_id INT NOT NULL,
            tag_name VARCHAR(50) NOT NULL,
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COLLATE=" . DB_COLLATE);
        
        // ایجاد کاربر مدیر پیش‌فرض
        $username = 'admin';
        $email = 'admin@example.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        
        // بررسی وجود کاربر مدیر
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() === 0) {
            // درج کاربر مدیر
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, is_admin) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$username, $email, $password, 'مدیر سیستم']);
        }
        
        $success = true;
    } catch (PDOException $e) {
        $error = 'خطا در ایجاد پایگاه داده: ' . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب سیستم اشتراک‌گذاری فایل</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .install-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 48px;
            color: #007bff;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="logo">
                <i class="fas fa-file-upload"></i>
            </div>
            <h1>نصب سیستم اشتراک‌گذاری فایل</h1>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <p><i class="fas fa-check-circle"></i> پایگاه داده با موفقیت ایجاد شد!</p>
                    <p>اطلاعات ورود مدیر سیستم:</p>
                    <ul>
                        <li>نام کاربری: admin</li>
                        <li>رمز عبور: admin123</li>
                    </ul>
                    <p>لطفاً پس از ورود به سیستم، رمز عبور مدیر را تغییر دهید.</p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary">رفتن به صفحه اصلی</a>
                    </div>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
                <form method="post">
                    <button type="submit" name="install" class="btn btn-primary">تلاش مجدد</button>
                </form>
            <?php else: ?>
                <p>این اسکریپت پایگاه داده مورد نیاز سیستم اشتراک‌گذاری فایل را ایجاد خواهد کرد.</p>
                <p>اطمینان حاصل کنید که:</p>
                <ul>
                    <li>تنظیمات اتصال به پایگاه داده در <code>config/database.php</code> به درستی تنظیم شده باشد.</li>
                    <li>کاربر MySQL دسترسی لازم برای ایجاد پایگاه داده و جداول را داشته باشد.</li>
                </ul>
                <div class="alert alert-info">
                    <p>بعد از نصب پایگاه داده، یک کاربر مدیر با مشخصات زیر ایجاد خواهد شد:</p>
                    <ul>
                        <li>نام کاربری: admin</li>
                        <li>رمز عبور: admin123</li>
                    </ul>
                </div>
                <form method="post" class="mt-4">
                    <button type="submit" name="install" class="btn btn-primary btn-lg w-100">نصب پایگاه داده</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>