<?php
// file: includes/footer.php - قالب فوتر سایت
?>
    </main>
    <!-- پایان محتوای اصلی -->
    
    <!-- فوتر -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">سیستم اشتراک‌گذاری فایل</h5>
                    <p>سیستمی ساده و کاربردی برای آپلود و اشتراک‌گذاری انواع فایل‌ها</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">لینک‌های مفید</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>" class="text-white text-decoration-none">صفحه اصلی</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/public-files.php" class="text-white text-decoration-none">فایل‌های عمومی</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>/dashboard.php" class="text-white text-decoration-none">داشبورد کاربری</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>/upload.php" class="text-white text-decoration-none">آپلود فایل</a></li>
                        <?php else: ?>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>/login.php" class="text-white text-decoration-none">ورود</a></li>
                            <li class="mb-2"><a href="<?php echo SITE_URL; ?>/register.php" class="text-white text-decoration-none">ثبت‌نام</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">تماس با ما</h5>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i> info@example.com</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i> 021-12345678</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-2" title="تلگرام"><i class="fab fa-telegram fa-lg"></i></a>
                        <a href="#" class="text-white me-2" title="اینستاگرام"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white me-2" title="لینکدین"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-white" title="گیت‌هاب"><i class="fab fa-github fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-3">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> سیستم اشتراک‌گذاری فایل. تمامی حقوق محفوظ است.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">طراحی و توسعه: <a href="#" class="text-white text-decoration-none">تیم PHP</a></p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- جاوااسکریپت‌ها -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>