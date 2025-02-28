<?php
// file: includes/header.php - قالب هدر سایت
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | سیستم اشتراک‌گذاری فایل' : 'سیستم اشتراک‌گذاری فایل'; ?></title>
    <!-- فونت‌آوسم -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- بوت‌استرپ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css">
    <!-- استایل اختصاصی -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- نوار ناوبری -->
    <?php include_once INCLUDES_PATH . '/navbar.php'; ?>
    
    <!-- پیام‌های سیستمی -->
    <div class="container mt-4">
        <?php displayFlashMessages(); ?>
    </div>
    
    <!-- شروع محتوای اصلی -->
    <main class="py-4"></main>