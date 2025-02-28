<?php
// file: register.php - صفحه ثبت‌نام کاربران

// بارگذاری تنظیمات
require_once 'config/config.php';

// ریدایرکت کاربران وارد شده به داشبورد
redirectLoggedInUsers();

// تنظیم عنوان صفحه
$pageTitle = 'ثبت‌نام در سیستم';

// آرایه برای نگهداری خطاها
$errors = [];

// پردازش فرم ثبت‌نام
if (isPostRequest()) {
    // بررسی توکن CSRF
    if (!verifyCsrfToken()) {
        redirect(SITE_URL . '/register.php');
    }
    
    // دریافت داده‌های فرم
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    
    // قوانین اعتبارسنجی
    $rules = [
        'username' => ['required', 'min:3', 'max:50'],
        'email' => ['required', 'email', 'max:100'],
        'password' => ['required', 'min:6', 'max:100'],
        'confirm_password' => ['required', 'match:password'],
        'full_name' => ['max:100']
    ];
    
    // اعتبارسنجی داده‌های ورودی
    $validationErrors = validateFormData($_POST, $rules);
    
    if (!empty($validationErrors)) {
        $errors = array_merge($errors, $validationErrors);
    }
    
    // بررسی نام کاربری و ایمیل تکراری
    if (empty($errors)) {
        $user = new User();
        
        if ($user->exists($username, $email)) {
            $errors[] = 'نام کاربری یا ایمیل قبلاً ثبت شده است.';
        }
    }
    
    // اگر خطایی وجود نداشت
    if (empty($errors)) {
        // ایجاد کاربر جدید
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $fullName
        ];
        
        $user = new User();
        $userId = $user->register($userData);
        
        if ($userId) {
            // تنظیم پیام موفقیت
            Session::setFlash('success', 'ثبت‌نام با موفقیت انجام شد. اکنون می‌توانید وارد سیستم شوید.');
            
            // ریدایرکت به صفحه ورود
            redirect(SITE_URL . '/login.php');
        } else {
            $errors[] = 'خطا در ثبت‌نام. لطفاً دوباره تلاش کنید.';
        }
    }
}

// بارگذاری قالب هدر
include_once INCLUDES_PATH . '/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h2 class="h5 mb-0"><i class="fas fa-user-plus me-2"></i> ثبت‌نام در سیستم</h2>
                </div>
                <div class="card-body p-4">
                    <!-- نمایش خطاها -->
                    <?php displayErrors($errors); ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <!-- توکن CSRF -->
                        <?php csrfField(); ?>
                        
                        <!-- نام کاربری -->
                        <div class="mb-3">
                            <label for="username" class="form-label">نام کاربری <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            <div class="form-text">نام کاربری باید حداقل 3 کاراکتر باشد.</div>
                        </div>
                        
                        <!-- ایمیل -->
                        <div class="mb-3">
                            <label for="email" class="form-label">آدرس ایمیل <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                        
                        <!-- نام کامل -->
                        <div class="mb-3">
                            <label for="full_name" class="form-label">نام و نام خانوادگی</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                        
                        <!-- رمز عبور -->
                        <div class="mb-3">
                            <label for="password" class="form-label">رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">رمز عبور باید حداقل 6 کاراکتر باشد.</div>
                        </div>
                        
                        <!-- تکرار رمز عبور -->
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <!-- قوانین و مقررات -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                <a href="<?php echo SITE_URL; ?>/terms.php" target="_blank">قوانین و مقررات</a> سایت را می‌پذیرم.
                            </label>
                        </div>
                        
                        <!-- دکمه ارسال -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">ثبت‌نام</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light p-3 text-center">
                    قبلاً ثبت‌نام کرده‌اید؟ <a href="<?php echo SITE_URL; ?>/login.php">وارد شوی