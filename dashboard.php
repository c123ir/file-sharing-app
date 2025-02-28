<?php
// file: dashboard.php - صفحه داشبورد کاربر

// بارگذاری تنظیمات
require_once 'config/config.php';

// محدود کردن دسترسی به کاربران وارد شده
requireLogin();

// دریافت اطلاعات کاربر جاری
$userId = Session::getCurrentUserId();
$user = new User();
$userInfo = $user->findById($userId);

// دریافت آمار فایل‌های کاربر
$file = new File();
$userStats = $file->getUserStats($userId);

// دریافت آخرین فایل‌های کاربر
$latestFiles = $file->findByUserId($userId, 0, 5);

// دریافت آخرین فایل‌های به اشتراک گذاشته شده
$sharedFiles = $file->findSharedWithUser($userId, 0, 5);

// تنظیم عنوان صفحه
$pageTitle = 'داشبورد کاربری';

// بارگذاری قالب هدر
include_once INCLUDES_PATH . '/header.php';
?>

<div class="container py-4">
    <!-- هدر داشبورد -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">داشبورد کاربری</h1>
            <p class="text-muted">به داشبورد مدیریت فایل‌های خود خوش آمدید. از اینجا می‌توانید فایل‌ها را مدیریت کنید.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo SITE_URL; ?>/upload.php" class="btn btn-primary">
                <i class="fas fa-cloud-upload-alt me-1"></i> آپلود فایل جدید
            </a>
        </div>
    </div>
    
    <!-- آمار کاربر -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 text-muted mb-0">کل فایل‌ها</h2>
                            <p class="h3 text-primary mb-0 mt-2"><?php echo $userStats['total_files']; ?></p>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 text-muted mb-0">تصاویر</h2>
                            <p class="h3 text-primary mb-0 mt-2"><?php echo $userStats['images_count']; ?></p>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fas fa-image"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 text-muted mb-0">ویدیوها</h2>
                            <p class="h3 text-primary mb-0 mt-2"><?php echo $userStats['videos_count']; ?></p>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fas fa-video"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h5 text-muted mb-0">حجم کل</h2>
                            <p class="h3 text-primary mb-0 mt-2"><?php echo formatFileSize($userStats['total_size']); ?></p>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- بخش اصلی -->
    <div class="row">
        <!-- آخرین فایل‌های آپلود شده -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">آخرین فایل‌های آپلود شده</h3>
                        <a href="<?php echo SITE_URL; ?>/view-files.php" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($latestFiles)): ?>
                        <div class="p-4 text-center">
                            <i class="fas fa-info-circle text-muted mb-2" style="font-size: 48px;"></i>
                            <p class="mb-0">هنوز هیچ فایلی آپلود نکرده‌اید.</p>
                            <div class="mt-3">
                                <a href="<?php echo SITE_URL; ?>/upload.php" class="btn btn-primary">آپلود اولین فایل</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>نام فایل</th>
                                        <th>نوع</th>
                                        <th>اندازه</th>
                                        <th>تاریخ</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latestFiles as $file): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 text-primary">
                                                        <?php echo getFileTypeIcon($file['file_type']); ?>
                                                    </div>
                                                    <div>
                                                        <?php echo htmlspecialchars(truncateText($file['original_filename'], 30)); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $file['file_type']; ?></td>
                                            <td><?php echo formatFileSize($file['file_size']); ?></td>
                                            <td><?php echo formatPersianDate($file['created_at']); ?></td>
                                            <td>
                                                <?php if ($file['public']): ?>
                                                    <span class="badge bg-success">عمومی</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">خصوصی</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo SITE_URL; ?>/file-details.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-outline-primary" title="مشاهده"><i class="fas fa-eye"></i></a>
                                                    <a href="<?php echo getDownloadUrl($file['id']); ?>" class="btn btn-sm btn-outline-success" title="دانلود"><i class="fas fa-download"></i></a>
                                                    <a href="<?php echo SITE_URL; ?>/share-file.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-outline-info" title="اشتراک‌گذاری"><i class="fas fa-share-alt"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- سایدبار -->
        <div class="col-lg-4">
            <!-- پروفایل کاربر -->
            <div class="card shadow-sm mb-4">
            <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; font-size: 36px;">
                            <?php echo strtoupper(substr($userInfo['username'], 0, 1)); ?>
                        </div>
                        <h4 class="mb-0"><?php echo !empty($userInfo['full_name']) ? htmlspecialchars($userInfo['full_name']) : htmlspecialchars($userInfo['username']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($userInfo['email']); ?></p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo SITE_URL; ?>/profile.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-edit me-2"></i> ویرایش پروفایل</span>
                            <i class="fas fa-chevron-left text-muted"></i>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/change-password.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-key me-2"></i> تغییر رمز عبور</span>
                            <i class="fas fa-chevron-left text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- فایل‌های به اشتراک گذاشته شده با من -->
            <div class="card shadow-sm">
                <div class="card-header bg-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">فایل‌های به اشتراک گذاشته شده با من</h3>
                        <a href="<?php echo SITE_URL; ?>/shared-files.php" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($sharedFiles)): ?>
                        <div class="p-4 text-center">
                            <p class="text-muted mb-0">هنوز فایلی با شما به اشتراک گذاشته نشده است.</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($sharedFiles as $file): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 text-primary">
                                                <?php echo getFileTypeIcon($file['file_type']); ?>
                                            </div>
                                            <div>
                                                <div><?php echo htmlspecialchars(truncateText($file['original_filename'], 20)); ?></div>
                                                <small class="text-muted">اشتراک گذاشته شده توسط: <?php echo htmlspecialchars($file['shared_by_username']); ?></small>
                                            </div>
                                        </div>
                                        <a href="<?php echo SITE_URL; ?>/file-details.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- نوار آمار پیشرفته -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white p-3">
                    <h3 class="h5 mb-0">آمار تفکیکی فایل‌ها</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4 class="h6 mb-3">توزیع فایل‌ها بر اساس نوع</h4>
                                <div class="progress-stacked">
                                    <?php
                                    $totalFiles = $userStats['total_files'];
                                    if ($totalFiles > 0):
                                        $imagePercent = round(($userStats['images_count'] / $totalFiles) * 100);
                                        $videoPercent = round(($userStats['videos_count'] / $totalFiles) * 100);
                                        $audioPercent = round(($userStats['audios_count'] / $totalFiles) * 100);
                                        $docsPercent = round(($userStats['documents_count'] / $totalFiles) * 100);
                                    ?>
                                    <div class="progress" role="progressbar" style="width: <?php echo $imagePercent; ?>%" aria-valuenow="<?php echo $imagePercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success"><?php echo $imagePercent > 5 ? $imagePercent . '%' : ''; ?></div>
                                    </div>
                                    <div class="progress" role="progressbar" style="width: <?php echo $videoPercent; ?>%" aria-valuenow="<?php echo $videoPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger"><?php echo $videoPercent > 5 ? $videoPercent . '%' : ''; ?></div>
                                    </div>
                                    <div class="progress" role="progressbar" style="width: <?php echo $audioPercent; ?>%" aria-valuenow="<?php echo $audioPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning"><?php echo $audioPercent > 5 ? $audioPercent . '%' : ''; ?></div>
                                    </div>
                                    <div class="progress" role="progressbar" style="width: <?php echo $docsPercent; ?>%" aria-valuenow="<?php echo $docsPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-info"><?php echo $docsPercent > 5 ? $docsPercent . '%' : ''; ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6 col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 12px; height: 12px; background-color: var(--bs-success);"></div>
                                            <span class="small">تصاویر</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 12px; height: 12px; background-color: var(--bs-danger);"></div>
                                            <span class="small">ویدیوها</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 12px; height: 12px; background-color: var(--bs-warning);"></div>
                                            <span class="small">فایل‌های صوتی</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2" style="width: 12px; height: 12px; background-color: var(--bs-info);"></div>
                                            <span class="small">اسناد</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <h4 class="h6 mb-3">نسبت فایل‌های عمومی و خصوصی</h4>
                                <?php
                                if ($totalFiles > 0):
                                    $publicPercent = round(($userStats['public_files'] / $totalFiles) * 100);
                                    $privatePercent = 100 - $publicPercent;
                                ?>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $publicPercent; ?>%" aria-valuenow="<?php echo $publicPercent; ?>" aria-valuemin="0" aria-valuemax="100">عمومی (<?php echo $publicPercent; ?>%)</div>
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: <?php echo $privatePercent; ?>%" aria-valuenow="<?php echo $privatePercent; ?>" aria-valuemin="0" aria-valuemax="100">خصوصی (<?php echo $privatePercent; ?>%)</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="small"><?php echo $userStats['public_files']; ?> فایل عمومی</div>
                                    <div class="small"><?php echo $totalFiles - $userStats['public_files']; ?> فایل خصوصی</div>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">هنوز فایلی آپلود نکرده‌اید.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// بارگذاری قالب فوتر
include_once INCLUDES_PATH . '/footer.php';
?>