<?php
// file: includes/navbar.php - منوی ناوبری سایت
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
            <i class="fas fa-file-upload me-2"></i>
            اشتراک‌گذاری فایل
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>">صفحه اصلی</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/public-files.php">فایل‌های عمومی</a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            مدیریت فایل‌ها
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/upload.php">آپلود فایل جدید</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/view-files.php">فایل‌های من</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/shared-files.php">فایل‌های به اشتراک گذاشته شده با من</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            مدیریت سیستم
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/index.php">داشبورد مدیریت</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/users.php">مدیریت کاربران</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/files.php">مدیریت فایل‌ها</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/settings.php">تنظیمات سیستم</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- فرم جستجو -->
            <form class="d-flex mx-auto" action="<?php echo SITE_URL; ?>/search.php" method="GET">
                <input class="form-control ms-2" type="search" name="q" placeholder="جستجوی فایل..." aria-label="Search">
                <button class="btn btn-light" type="submit"><i class="fas fa-search"></i></button>
            </form>
            
            <!-- منوی کاربر -->
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars(Session::get('username')); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/dashboard.php">داشبورد من</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/profile.php">ویرایش پروفایل</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">خروج</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login.php">ورود</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/register.php">ثبت‌نام</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>