<?php
// file: login.php - صفحه ورود کاربران

// بارگذاری تنظیمات
require_once 'config/config.php';

// ریدایرکت کاربران وارد شده به داشبورد
redirectLoggedInUsers();

// تنظیم عنوان صفحه
$pageTitle = 'ورود به سیستم';

// آرایه برای نگهداری خطاها
$errors = [];

// پردازش فرم ورود
if (isPostRequest()) {
    // بررسی توکن CSRF
    if (!verifyCsrfToken()) {
        redirect(SITE_URL . '/login.php');
    }
    
    // دریافت داده‌های فرم
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // اعتبارسنجی داده‌های ورودی
    if (empty($username)) {
        $errors[] = 'نام کاربری یا ایمیل الزامی است.';
    }
    
    if (empty($password)) {
        $errors[] = 'رمز عبور الزامی است.';
    }
    
    // اگر خطایی وجود نداشت
    if (empty($errors)) {
        // تلاش برای ورود کاربر
        $user = new User();
        $loggedIn = $user->login($username, $password, $remember);
        
        if ($loggedIn) {
            // تنظیم پیام موفقیت
            Session::setFlash('success', 'ورود موفقیت‌آمیز. خوش آمدید!');
            
            // ریدایرکت به داشبورد
            redirect(SITE_URL . '/dashboard.php');
        } else {
            $errors[] = 'نام کاربری/ایمیل یا رمز عبور اشتباه است.';
        }
    }
}

// بارگذاری قالب هدر
include_once INCLUDES_PATH . '/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h2 class="h5 mb-0"><i class="fas fa-sign-in-alt me-2"></i> ورود به سیستم</h2>
                </div>
                <div class="card-body p-4">
                    <!-- نمایش خطاها -->
                    <?php displayErrors($errors); ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <!-- توکن CSRF -->
                        <?php csrfField(); ?>
                        
                        <!-- نام کاربری یا ایمیل -->
                        <div class="mb-3">
                            <label for="username" class="form-label">نام کاربری یا ایمیل</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                        
                        <!-- رمز عبور -->
                        <div class="mb-3">
                            <label for="password" class="form-label">رمز عبور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <!-- مرا به خاطر بسپار -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">مرا به خاطر بسپار</label>
                        </div>
                        
                        <!-- دکمه ارسال -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">ورود</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo SITE_URL; ?>/forgot-password.php">رمز عبور خود را فراموش کرده‌اید؟</a>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 text-center">
                    حساب کاربری ندارید؟ <a href="<?php echo SITE_URL; ?>/register.php">ثبت‌نام کنید</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// بارگذاری قالب فوتر
include_once INCLUDES_PATH . '/footer.php';
?>