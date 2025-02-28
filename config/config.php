<?php
// تنظیمات اصلی سایت
define('SITE_NAME', 'آپلود و اشتراک‌گذاری فایل');
define('SITE_URL', 'http://localhost/file-sharing-app'); // تغییر به آدرس سایت شما در سرور

// مسیرهای پوشه‌ها
define('ROOT_PATH', dirname(__FILE__, 2));
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// محدودیت‌های فایل
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 مگابایت
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3']);
define('ALLOWED_DOCUMENT_TYPES', [
    'application/pdf', 
    'application/msword', 
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain'
]);

// زمانی که کاربر وارد سیستم می‌ماند (به ثانیه)
define('SESSION_LIFETIME', 60 * 60 * 24); // 24 ساعت

// کلید امنیتی برای رمزگذاری
define('SECRET_KEY', 'your_secret_key_change_this'); // این مقدار را به یک رشته تصادفی تغییر دهید

// تنظیمات اعلان خطا (در محیط توسعه true و در محیط تولید false)
define('DISPLAY_ERRORS', true);

if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// تنظیمات منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// بارگذاری فایل‌های مورد نیاز
require_once ROOT_PATH . '/config/database.php';
require_once CLASSES_PATH . '/Database.php';
require_once CLASSES_PATH . '/Session.php';
require_once CLASSES_PATH . '/User.php';
require_once CLASSES_PATH . '/File.php';
require_once INCLUDES_PATH . '/functions.php';

// شروع نشست
Session::start();