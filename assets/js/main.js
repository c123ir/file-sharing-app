/*
 * file: assets/js/main.js - اسکریپت‌های سفارشی سیستم اشتراک‌گذاری فایل
 */

document.addEventListener('DOMContentLoaded', function() {
    // --------- مدیریت پیام‌های سیستم ---------
    
    // حذف اتوماتیک پیام‌های هشدار و موفقیت بعد از چند ثانیه
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            fadeOut(alert);
        }, 5000); // ۵ ثانیه
    });
    
    // دکمه بستن هشدار
    const closeButtons = document.querySelectorAll('.alert .btn-close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            fadeOut(alert);
        });
    });
    
    // --------- آپلود فایل ---------
    
    // نمایش پیش‌نمایش فایل آپلود شده
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            previewFile(this);
        });
        
        // منطقه رها کردن فایل
        const dropZone = document.querySelector('.upload-zone');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.add('dragover');
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.remove('dragover');
                }, false);
            });
            
            dropZone.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                fileInput.files = files;
                previewFile(fileInput);
            }, false);
            
            dropZone.addEventListener('click', function() {
                fileInput.click();
            });
        }
    }
    
    // --------- اشتراک‌گذاری فایل ---------
    
    // کپی کردن لینک اشتراک‌گذاری
    const copyLinkButtons = document.querySelectorAll('.copy-link-btn');
    copyLinkButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const linkInput = document.getElementById(this.getAttribute('data-input'));
            
            // انتخاب متن
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // برای موبایل
            
            // کپی کردن در کلیپ‌بورد
            document.execCommand('copy');
            
            // تغییر متن دکمه
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> کپی شد';
            
            // برگرداندن به حالت اولیه
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    });
    
    // --------- جدول‌های داده ---------
    
    // فعال‌سازی دکمه انتخاب همه
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.table-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateBulkActionButtons();
        });
        
        // به‌روزرسانی دکمه انتخاب همه بر اساس وضعیت چک‌باکس‌ها
        const checkboxes = document.querySelectorAll('.table-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // بررسی وضعیت همه چک‌باکس‌ها
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                const someChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                
                // به‌روزرسانی وضعیت چک‌باکس "انتخاب همه"
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && someChecked;
                
                updateBulkActionButtons();
            });
        });
    }
    
    // --------- فیلترینگ و مرتب‌سازی ---------
    
    // فیلتر کردن جدول‌ها
    const tableFilter = document.getElementById('table-filter');
    if (tableFilter) {
        tableFilter.addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.filterable-table tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // --------- توابع کمکی ---------
    
    // محو شدن تدریجی المان
    function fadeOut(element) {
        let opacity = 1;
        const timer = setInterval(function() {
            if (opacity <= 0.1) {
                clearInterval(timer);
                element.style.display = 'none';
                element.parentNode.removeChild(element);
            }
            element.style.opacity = opacity;
            opacity -= 0.1;
        }, 50);
    }
    
    // نمایش پیش‌نمایش فایل
    function previewFile(input) {
        const previewContainer = document.querySelector('.file-preview-container');
        const previewElement = document.getElementById('file-preview');
        const fileInfo = document.getElementById('file-info');
        
        if (!previewContainer || !fileInfo) return;
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // نمایش اطلاعات فایل
            fileInfo.innerHTML = `
                <div><strong>نام فایل:</strong> ${file.name}</div>
                <div><strong>نوع:</strong> ${file.type || 'نامشخص'}</div>
                <div><strong>حجم:</strong> ${formatFileSize(file.size)}</div>
            `;
            
            // نمایش پیش‌نمایش بر اساس نوع فایل
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.innerHTML = `<img src="${e.target.result}" class="img-fluid" alt="پیش‌نمایش">`;
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                previewElement.innerHTML = `<div class="text-center p-3"><i class="fas fa-video fa-3x text-muted"></i><p class="mt-2">فایل ویدیویی</p></div>`;
            } else if (file.type.startsWith('audio/')) {
                previewElement.innerHTML = `<div class="text-center p-3"><i class="fas fa-music fa-3x text-muted"></i><p class="mt-2">فایل صوتی</p></div>`;
            } else if (file.type === 'application/pdf') {
                previewElement.innerHTML = `<div class="text-center p-3"><i class="fas fa-file-pdf fa-3x text-muted"></i><p class="mt-2">فایل PDF</p></div>`;
            } else {
                previewElement.innerHTML = `<div class="text-center p-3"><i class="fas fa-file fa-3x text-muted"></i><p class="mt-2">فایل</p></div>`;
            }
            
            previewContainer.classList.remove('d-none');
        } else {
            previewContainer.classList.add('d-none');
        }
    }
    
    // فرمت کردن اندازه فایل
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 بایت';
        
        const sizes = ['بایت', 'کیلوبایت', 'مگابایت', 'گیگابایت', 'ترابایت'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        
        return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // به‌روزرسانی دکمه‌های عملیات دسته‌جمعی
    function updateBulkActionButtons() {
        const bulkActionButtons = document.querySelectorAll('.bulk-action-btn');
        const anyChecked = document.querySelector('.table-checkbox:checked');
        
        bulkActionButtons.forEach(function(button) {
            if (anyChecked) {
                button.removeAttribute('disabled');
            } else {
                button.setAttribute('disabled', 'disabled');
            }
        });
    }
});