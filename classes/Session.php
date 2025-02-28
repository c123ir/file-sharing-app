<?php
/**
 * کلاس مدیریت نشست‌ها
 */
class Session {
    /**
     * شروع نشست
     */
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            // تنظیم نام نشست
            session_name('file_sharing_session');
            
            // تنظیم مسیر ذخیره نشست (اختیاری - اگر می‌خواهید محل ذخیره نشست را تغییر دهید)
            // session_save_path(ROOT_PATH . '/tmp/sessions');
            
            // تنظیم طول عمر کوکی نشست
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            // شروع نشست
            session_start();
            
            // تنظیم زمان انقضا
            if (!isset($_SESSION['expires_at'])) {
                $_SESSION['expires_at'] = time() + SESSION_LIFETIME;
            } else if ($_SESSION['expires_at'] < time()) {
                // اگر نشست منقضی شده باشد، آن را پاک کن
                self::destroy();
                session_start();
                $_SESSION['expires_at'] = time() + SESSION_LIFETIME;
            }
        }
    }
    
    /**
     * تنظیم یک مقدار در نشست
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * دریافت مقدار از نشست
     */
    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * بررسی وجود کلید در نشست
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * حذف مقدار از نشست
     */
    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * پاک کردن همه مقادیر نشست
     */
    public static function clear() {
        $_SESSION = [];
    }
    
    /**
     * نابودی کامل نشست
     */
    public static function destroy() {
        self::clear();
        
        // پاک کردن کوکی نشست
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // نابودی نشست
        session_destroy();
    }
    
    /**
     * تنظیم پیام فلش (پیام موقت)
     */
    public static function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * دریافت پیام فلش و حذف آن
     */
    public static function getFlash($key, $default = null) {
        $message = isset($_SESSION['flash'][$key]) ? $_SESSION['flash'][$key] : $default;
        if (isset($_SESSION['flash'][$key])) {
            unset($_SESSION['flash'][$key]);
        }
        return $message;
    }
    
    /**
     * بررسی وجود پیام فلش
     */
    public static function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
    
    /**
     * تولید یک شناسه تصادفی برای توکن CSRF
     */
    public static function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }
    
    /**
     * بررسی صحت توکن CSRF
     */
    public static function validateCsrfToken($token) {
        return self::has('csrf_token') && self::get('csrf_token') === $token;
    }
    
    /**
     * تنظیم اطلاعات کاربر وارد شده
     */
    public static function setLoggedInUser($user) {
        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('email', $user['email']);
        self::set('is_logged_in', true);
        
        // اگر remember me را انتخاب کرده باشد، زمان طولانی‌تری تنظیم کن
        if (isset($user['remember_me']) && $user['remember_me']) {
            $_SESSION['expires_at'] = time() + (30 * 24 * 60 * 60); // 30 روز
        }
    }
    
    /**
     * بررسی وضعیت ورود کاربر
     */
    public static function isLoggedIn() {
        return self::get('is_logged_in', false);
    }
    
    /**
     * خروج کاربر
     */
    public static function logout() {
        self::destroy();
    }

    /**
     * دریافت شناسه کاربر فعلی
     */
    public static function getCurrentUserId() {
        return self::get('user_id');
    }
}