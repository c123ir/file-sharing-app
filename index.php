<?php
// file: index.php - صفحه اصلی سیستم اشتراک‌گذاری فایل

// بارگذاری تنظیمات
require_once 'config/config.php';

// دریافت آخرین فایل‌های عمومی
$file = new File();
$latestFiles = $file->findPublicFiles(0, 10);

// اطلاعات آماری
$stats = $file->getStats();

// تنظیم عنوان صفحه
$pageTitle = 'سیستم اشتراک‌گذاری فایل';

// بارگذاری قالب هدر
include_once INCLUDES_PATH . '/header.php';
?>

<!-- بخش معرفی -->
<div class="jumbotron bg-primary text-white rounded-3 p-5 mb-4">
    <div class="container">
        <h1 class="display-4">به سیستم اشتراک‌گذاری فایل خوش آمدید</h1>
        <p class="lead">به راحتی فایل‌های خود را آپلود کنید و با دیگران به اشتراک بگذارید.</p>
        <?php if (!isLoggedIn()): ?>
            <div class="mt-4">
                <a href="register.php" class="btn btn-light btn-lg me-2">ثبت‌نام</a>
                <a href="login.php" class="btn btn-outline-light btn-lg">ورود</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="upload.php" class="btn btn-light btn-lg me-2">آپلود فایل جدید</a>
                <a href="dashboard.php" class="btn btn-outline-light btn-lg">داشبورد من</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ویژگی‌های سیستم -->
<div class="container mb-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-upload fs-1 text-primary mb-3"></i>
                    <h3 class="card-title h5">آپلود آسان</h3>
                    <p class="card-text">انواع فایل‌های تصویری، ویدئویی، صوتی و اسناد را به راحتی آپلود کنید.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-share-alt fs-1 text-primary mb-3"></i>
                    <h3 class="card-title h5">اشتراک‌گذاری</h3>
                    <p class="card-text">فایل‌های خود را به صورت عمومی یا خصوصی با دیگران به اشتراک بگذارید.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-shield-alt fs-1 text-primary mb-3"></i>
                    <h3 class="card-title h5">امنیت</h3>
                    <p class="card-text">فایل‌های شما با امنیت بالا ذخیره می‌شوند و قابل مدیریت هستند.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-tachometer-alt fs-1 text-primary mb-3"></i>
                    <h3 class="card-title h5">مدیریت آسان</h3>
                    <p class="card-text">داشبورد کاربری برای مدیریت راحت فایل‌ها و اشتراک‌گذاری‌ها.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آمار سیستم -->
<div class="container mb-5">
    <div class="card bg-light">
        <div class="card-body">
            <h2 class="card-title h4 mb-4">آمار سیستم</h2>
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="h2 text-primary mb-0"><?php echo $stats['total_files']; ?></div>
                    <div class="text-muted">فایل آپلود شده</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="h2 text-primary mb-0"><?php echo $stats['images_count']; ?></div>
                    <div class="text-muted">تصویر</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="h2 text-primary mb-0"><?php echo $stats['videos_count']; ?></div>
                    <div class="text-muted">ویدئو</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="h2 text-primary mb-0"><?php echo formatFileSize($stats['total_size']); ?></div>
                    <div class="text-muted">حجم کل فایل‌ها</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آخرین فایل‌های عمومی -->
<?php if (!empty($latestFiles)): ?>
<div class="container mb-5">
    <h2 class="h4 mb-4">آخرین فایل‌های عمومی</h2>
    <div class="row">
        <?php foreach ($latestFiles as $file): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 fs-3">
                                <?php echo getFileTypeIcon($file['file_type']); ?>
                            </div>
                            <div>
                                <h3 class="card-title h6 mb-0"><?php echo htmlspecialchars(truncateText($file['original_filename'], 30)); ?></h3>
                                <div class="text-muted small">آپلود توسط: <?php echo htmlspecialchars($file['username']); ?></div>
                            </div>
                        </div>
                        
                        <?php if (!empty($file['description'])): ?>
                            <p class="card-text small"><?php echo htmlspecialchars(truncateText($file['description'], 100)); ?></p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <div class="text-muted small">
                                <i class="fas fa-file-alt"></i> <?php echo formatFileSize($file['file_size']); ?>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-download"></i> <?php echo $file['download_count']; ?> دانلود
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="file-details.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-primary">مشاهده و دانلود</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="public-files.php" class="btn btn-outline-primary">مشاهده همه فایل‌های عمومی</a>
    </div>
</div>
<?php endif; ?>

<!-- بخش راهنما -->
<div class="container mb-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="h3 mb-4">چگونه کار می‌کند؟</h2>
            <div class="accordion" id="howToAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            ثبت‌نام و ورود
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#howToAccordion">
                        <div class="accordion-body">
                            برای استفاده از سیستم، ابتدا باید ثبت‌نام کنید. پس از ثبت‌نام و ورود به سیستم، می‌توانید از امکانات کامل سیستم استفاده کنید.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            آپلود فایل
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#howToAccordion">
                        <div class="accordion-body">
                            از بخش «آپلود فایل» در داشبورد، می‌توانید فایل‌های مختلف را آپلود کنید. انواع فایل‌های تصویری، ویدئویی، صوتی و اسناد قابل آپلود هستند.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            اشتراک‌گذاری
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#howToAccordion">
                        <div class="accordion-body">
                            فایل‌های آپلود شده را می‌توانید به صورت خصوصی یا عمومی مدیریت کنید. برای اشتراک‌گذاری خصوصی، نام کاربری یا ایمیل فرد مورد نظر را وارد کنید.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center py-4">
            <i class="fas fa-cloud-upload-alt text-primary" style="font-size: 180px; opacity: 0.2;"></i>
        </div>
    </div>
</div>

<?php
// بارگذاری قالب فوتر
include_once INCLUDES_PATH . '/footer.php';
?>