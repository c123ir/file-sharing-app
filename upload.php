<?php
// file: upload.php - صفحه آپلود فایل

// بارگذاری تنظیمات
require_once 'config/config.php';

// محدود کردن دسترسی به کاربران وارد شده
requireLogin();

// تنظیم عنوان صفحه
$pageTitle = 'آپلود فایل جدید';

// تنظیم متغیرهای مورد نیاز
$userId = Session::getCurrentUserId();
$success = false;
$errors = [];
$uploadedFile = null;

// پردازش فرم آپلود
if (isPostRequest()) {
    // بررسی توکن CSRF
    if (!verifyCsrfToken()) {
        redirect(SITE_URL . '/upload.php');
    }
    
    // دریافت داده‌های فرم
    $description = $_POST['description'] ?? '';
    $isPublic = isset($_POST['is_public']) ? true : false;
    
    // بررسی فایل آپلود شده
    if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'لطفاً یک فایل برای آپلود انتخاب کنید.';
    } else {
        // آپلود فایل
        $file = new File();
        $result = $file->upload($_FILES['file'], $userId, $description, $isPublic);
        
        if ($result['success']) {
            $uploadedFile = $result;
            $success = true;
            
            // تنظیم پیام موفقیت در نشست
            Session::setFlash('success', 'فایل با موفقیت آپلود شد.');
            
            // ریدایرکت به صفحه جزئیات فایل
            redirect(SITE_URL . '/file-details.php?id=' . $result['file_id']);
        } else {
            $errors[] = $result['error'];
        }
    }
}

// بارگذاری قالب هدر
include_once INCLUDES_PATH . '/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h2 class="h5 mb-0"><i class="fas fa-cloud-upload-alt me-2"></i> آپلود فایل جدید</h2>
                </div>
                <div class="card-body p-4">
                    <!-- نمایش خطاها -->
                    <?php displayErrors($errors); ?>
                    
                    <!-- فرم آپلود -->
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                        <!-- توکن CSRF -->
                        <?php csrfField(); ?>
                        
                        <!-- انتخاب فایل -->
                        <div class="mb-4">
                            <label for="file" class="form-label">انتخاب فایل <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="file" name="file" required>
                            <div class="form-text">
                                <strong>انواع فایل‌های مجاز:</strong> تصاویر (JPG, PNG, GIF, WebP)، ویدیوها (MP4, MPEG, WebM)، فایل‌های صوتی (MP3, WAV, OGG)، اسناد (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT)
                                <br>
                                <strong>حداکثر حجم فایل:</strong> <?php echo formatFileSize(MAX_FILE_SIZE); ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="upload-preview text-center d-none">
                                        <img id="image-preview" class="img-fluid img-thumbnail mb-2 d-none" style="max-height: 200px;">
                                        <div id="file-info" class="text-muted small mb-0"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- توضیحات فایل -->
                        <div class="mb-3">
                            <label for="description" class="form-label">توضیحات فایل</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            <div class="form-text">توضیحات مختصری درباره فایل بنویسید. این توضیحات به کاربران دیگر کمک می‌کند تا محتوای فایل را بهتر درک کنند.</div>
                        </div>
                        
                        <!-- تنظیمات حریم خصوصی -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" <?php echo isset($_POST['is_public']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_public">
                                    این فایل عمومی باشد (قابل مشاهده برای همه کاربران)
                                </label>
                            </div>
                        </div>
                        
                        <!-- دکمه‌های ارسال -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-1"></i> آپلود فایل
                            </button>
                            <a href="<?php echo SITE_URL; ?>/dashboard.php" class="btn btn-outline-secondary">انصراف</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- اسکریپت پیش‌نمایش فایل -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const imagePreview = document.getElementById('image-preview');
    const fileInfo = document.getElementById('file-info');
    const uploadPreview = document.querySelector('.upload-preview');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            
            // نمایش اطلاعات فایل
            uploadPreview.classList.remove('d-none');
            fileInfo.innerHTML = `<strong>نام فایل:</strong> ${file.name}<br><strong>نوع فایل:</strong> ${file.type}<br><strong>حجم:</strong> ${formatBytes(file.size)}`;
            
            // نمایش پیش‌نمایش برای تصاویر
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('d-none');
            }
        } else {
            uploadPreview.classList.add('d-none');
        }
    });
    
    // تابع فرمت‌بندی اندازه فایل
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});
</script>

<?php
// بارگذاری قالب فوتر
include_once INCLUDES_PATH . '/footer.php';
?>