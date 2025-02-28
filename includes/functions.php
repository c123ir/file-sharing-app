<?php
/**
 * فایل توابع کمکی برنامه
 */

/**
 * ریدایرکت به یک URL
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * نمایش پیام‌های خطا
 */
function displayErrors($errors) {
    if (is_array($errors) && !empty($errors)) {
        echo '<div class="alert alert-danger">';
        echo '<ul class="mb-0">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

/**
 * نمایش پیام موفقیت
 */
function displaySuccess($message) {
    if (!empty($message)) {
        echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
    }
}

/**
 * نمایش پیام‌های فلش
 */
function displayFlashMessages() {
    if (Session::hasFlash('success')) {
        displaySuccess(Session::getFlash('success'));
    }
    
    if (Session::hasFlash('error')) {
        displayErrors([Session::getFlash('error')]);
    }
    
    if (Session::hasFlash('errors') && is_array(Session::getFlash('errors'))) {
        displayErrors(Session::getFlash('errors'));
    }
}

/**
 * اعتبارسنجی دیتای فرم
 */
function validateFormData($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $fieldRules) {
        // اگر فیلد الزامی است و وجود ندارد یا خالی است
        if (in_array('required', $fieldRules) && (!isset($data[$field]) || trim($data[$field]) === '')) {
            $errors[] = "فیلد {$field} الزامی است.";
            continue;
        }
        
        // اگر فیلد وجود دارد، قوانین دیگر را بررسی کن
        if (isset($data[$field]) && !empty($data[$field])) {
            foreach ($fieldRules as $rule) {
                // بررسی ایمیل
                if ($rule === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "فرمت ایمیل نامعتبر است.";
                }
                
                // بررسی حداقل طول
                if (strpos($rule, 'min:') === 0) {
                    $minLength = (int) substr($rule, 4);
                    if (strlen($data[$field]) < $minLength) {
                        $errors[] = "فیلد {$field} باید حداقل {$minLength} کاراکتر باشد.";
                    }
                }
                
                // بررسی حداکثر طول
                if (strpos($rule, 'max:') === 0) {
                    $maxLength = (int) substr($rule, 4);
                    if (strlen($data[$field]) > $maxLength) {
                        $errors[] = "فیلد {$field} نباید بیشتر از {$maxLength} کاراکتر باشد.";
                    }
                }
                
                // بررسی مطابقت با فیلد دیگر (برای تأیید رمز عبور)
                if (strpos($rule, 'match:') === 0) {
                    $matchField = substr($rule, 6);
                    if (!isset($data[$matchField]) || $data[$field] !== $data[$matchField]) {
                        $errors[] = "فیلد {$field} با فیلد {$matchField} مطابقت ندارد.";
                    }
                }
                
                // بررسی عدد بودن
                if ($rule === 'numeric' && !is_numeric($data[$field])) {
                    $errors[] = "فیلد {$field} باید عدد باشد.";
                }
            }
        }
    }
    
    return $errors;
}

/**
 * ایمن‌سازی داده‌های ورودی
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
    } else {
        $data = htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * بررسی درخواست POST
 */
function isPostRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * بررسی درخواست GET
 */
function isGetRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * تولید توکن CSRF
 */
function generateCsrfToken() {
    return Session::generateCsrfToken();
}

/**
 * نمایش فیلد توکن CSRF در فرم
 */
function csrfField() {
    $token = generateCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * بررسی توکن CSRF
 */
function verifyCsrfToken() {
    if (!isset($_POST['csrf_token']) || !Session::validateCsrfToken($_POST['csrf_token'])) {
        Session::setFlash('error', 'خطای امنیتی: درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
        return false;
    }
    return true;
}

/**
 * بررسی لاگین بودن کاربر
 */
function isLoggedIn() {
    return Session::isLoggedIn();
}

/**
 * بررسی دسترسی مدیر
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = Session::getCurrentUserId();
    $user = new User();
    return $user->isAdmin($userId);
}

/**
 * بررسی اجازه دسترسی برای یک فایل
 */
function checkFileAccess($fileId, $requireEditAccess = false) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = Session::getCurrentUserId();
    
    // اگر کاربر مدیر است، همیشه دسترسی دارد
    if (isAdmin()) {
        return true;
    }
    
    $file = new File();
    return $file->checkUserAccess($fileId, $userId, $requireEditAccess);
}

/**
 * نمایش فرمت اندازه فایل به صورت خوانا
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * نمایش زمان به صورت خوانا به فارسی
 */
function formatPersianDate($timestamp) {
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }
    
    $date = date('Y-m-d H:i:s', $timestamp);
    
    // برای تبدیل تاریخ به شمسی می‌توان از کتابخانه‌های PHP استفاده کرد
    // در اینجا فرمت فعلی را برمی‌گردانیم
    return $date;
}

/**
 * دریافت آیکون مناسب برای نوع فایل
 */
function getFileTypeIcon($fileType) {
    switch ($fileType) {
        case 'image':
            return '<i class="fas fa-image"></i>';
        case 'video':
            return '<i class="fas fa-video"></i>';
        case 'audio':
            return '<i class="fas fa-music"></i>';
        case 'document':
            return '<i class="fas fa-file-alt"></i>';
        default:
            return '<i class="fas fa-file"></i>';
    }
}

/**
 * برش متن و افزودن سه نقطه
 */
function truncateText($text, $length = 50) {
    if (mb_strlen($text, 'UTF-8') > $length) {
        return mb_substr($text, 0, $length, 'UTF-8') . '...';
    }
    return $text;
}

/**
 * بررسی امکان پخش آنلاین فایل
 */
function isStreamableFile($mimeType) {
    $streamableTypes = [
        // تصاویر
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        // ویدئوها
        'video/mp4', 'video/webm', 'video/ogg',
        // صوت‌ها
        'audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/mp3',
        // PDF
        'application/pdf'
    ];
    
    return in_array($mimeType, $streamableTypes);
}

/**
 * تولید لینک دانلود
 */
function getDownloadUrl($fileId) {
    return SITE_URL . '/download.php?id=' . $fileId;
}

/**
 * بررسی اینکه آیا یک مسیر، URL است
 */
function isUrl($path) {
    return filter_var($path, FILTER_VALIDATE_URL) !== false;
}

/**
 * محدود کردن دسترسی به صفحات مدیریت
 */
function requireAdmin() {
    if (!isAdmin()) {
        Session::setFlash('error', 'شما اجازه دسترسی به این بخش را ندارید.');
        redirect(SITE_URL . '/index.php');
    }
}

/**
 * محدود کردن دسترسی به کاربران وارد شده
 */
function requireLogin() {
    if (!isLoggedIn()) {
        Session::setFlash('error', 'لطفاً برای دسترسی به این صفحه وارد سایت شوید.');
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * هدایت کاربران وارد شده به داشبورد
 */
function redirectLoggedInUsers() {
    if (isLoggedIn()) {
        redirect(SITE_URL . '/dashboard.php');
    }
}