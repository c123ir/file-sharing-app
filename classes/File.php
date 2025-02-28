private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'حجم فایل بیشتر از حد مجاز در تنظیمات PHP است.';
            case UPLOAD_ERR_FORM_SIZE:
                return 'حجم فایل بیشتر از حد مجاز در فرم است.';
            case UPLOAD_ERR_PARTIAL:
                return 'فایل به صورت ناقص آپلود شده است.';
            case UPLOAD_ERR_NO_FILE:
                return 'هیچ فایلی آپلود نشده است.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'پوشه موقت برای آپلود فایل وجود ندارد.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'نوشتن فایل در دیسک با مشکل مواجه شد.';
            case UPLOAD_ERR_EXTENSION:
                return 'آپلود فایل توسط یک افزونه PHP متوقف شد.';
            default:
                return 'خطای ناشناخته در آپلود فایل.';
        }
    }
    
    /**
     * فرمت‌بندی حجم فایل به صورت خوانا
     */
    private function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * دریافت آمار کلی فایل‌ها
     */
    public function getFileStats() {
        try {
            $stats = [
                'total_files' => 0,
                'total_size' => 0,
                'images_count' => 0,
                'videos_count' => 0,
                'audios_count' => 0,
                'documents_count' => 0,
                'latest_files' => []
            ];
            
            // تعداد کل فایل‌ها
            $this->db->query("SELECT COUNT(*) as count FROM files");
            $result = $this->db->fetch();
            $stats['total_files'] = $result['count'];
            
            // مجموع حجم فایل‌ها
            $this->db->query("SELECT SUM(file_size) as total_size FROM files");
            $result = $this->db->fetch();
            $stats['total_size'] = $result['total_size'] ? $this->formatSize($result['total_size']) : '0 B';
            
            // تعداد فایل‌ها بر اساس نوع
            $this->db->query("SELECT COUNT(*) as count FROM files WHERE file_type = 'image'");
            $result = $this->db->fetch();
            $stats['images_count'] = $result['count'];
            
            $this->db->query("SELECT COUNT(*) as count FROM files WHERE file_type = 'video'");
            $result = $this->db->fetch();
            $stats['videos_count'] = $result['count'];
            
            $this->db->query("SELECT COUNT(*) as count FROM files WHERE file_type = 'audio'");
            $result = $this->db->fetch();
            $stats['audios_count'] = $result['count'];
            
            $this->db->query("SELECT COUNT(*) as count FROM files WHERE file_type = 'document'");
            $result = $this->db->fetch();
            $stats['documents_count'] = $result['count'];
            
            // آخرین فایل‌های آپلود شده
            $this->db->query("SELECT f.*, u.username 
                             FROM files f
                             JOIN users u ON f.user_id = u.id
                             ORDER BY f.created_at DESC
                             LIMIT 5");
            $stats['latest_files'] = $this->db->fetchAll();
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error in File::getFileStats: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * فیلتر فایل‌ها بر اساس نوع
     */
    public function filterByType($userId, $fileType, $offset = 0, $limit = 20, $isAdmin = false) {
        try {
            if ($isAdmin) {
                $this->db->query("SELECT f.*, u.username 
                                 FROM files f
                                 JOIN users u ON f.user_id = u.id
                                 WHERE f.file_type = :file_type
                                 ORDER BY f.created_at DESC
                                 LIMIT :offset, :limit");
                $this->db->bind(':file_type', $fileType);
                $this->db->bind(':offset', $offset, PDO::PARAM_INT);
                $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            } else {
                $this->db->query("SELECT f.*, u.username 
                                 FROM files f
                                 JOIN users u ON f.user_id = u.id
                                 WHERE (f.user_id = :user_id OR f.public = 1)
                                 AND f.file_type = :file_type
                                 ORDER BY f.created_at DESC
                                 LIMIT :offset, :limit");
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':file_type', $fileType);
                $this->db->bind(':offset', $offset, PDO::PARAM_INT);
                $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            }
            
            return $this->db->fetchAll();
        } catch (Exception $e) {
            error_log("Error in File::filterByType: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * دریافت کاربرانی که فایل با آنها به اشتراک گذاشته شده است
     */
    public function getFileSharedUsers($fileId) {
        try {
            $this->db->query("SELECT sf.*, u.username, u.email, u.full_name
                             FROM shared_files sf
                             JOIN users u ON sf.shared_with = u.id
                             WHERE sf.file_id = :file_id
                             ORDER BY sf.created_at DESC");
            $this->db->bind(':file_id', $fileId);
            
            return $this->db->fetchAll();
        } catch (Exception $e) {
            error_log("Error in File::getFileSharedUsers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * تولید لینک دانلود موقت
     */
    public function generateTemporaryDownloadLink($fileId, $expiryMinutes = 60) {
        try {
            // تولید توکن منحصربه‌فرد
            $token = bin2hex(random_bytes(16));
            
            // زمان انقضا
            $expiryTime = date('Y-m-d H:i:s', time() + ($expiryMinutes * 60));
            
            // ذخیره توکن در جدول لینک‌های موقت
            $linkData = [
                'file_id' => $fileId,
                'token' => $token,
                'expires_at' => $expiryTime,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('temporary_links', $linkData);
            
            // ساخت URL
            $downloadUrl = SITE_URL . '/download-temp.php?token=' . $token;
            
            return [
                'url' => $downloadUrl,
                'token' => $token,
                'expires_at' => $expiryTime
            ];
        } catch (Exception $e) {
            error_log("Error in File::generateTemporaryDownloadLink: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * بررسی اعتبار لینک دانلود موقت
     */
    public function validateTemporaryLink($token) {
        try {
            $this->db->query("SELECT tl.*, f.*
                             FROM temporary_links tl
                             JOIN files f ON tl.file_id = f.id
                             WHERE tl.token = :token AND tl.expires_at > NOW()");
            $this->db->bind(':token', $token);
            
            $result = $this->db->fetch();
            
            return $result ? $result : false;
        } catch (Exception $e) {
            error_log("Error in File::validateTemporaryLink: " . $e->getMessage());
            return false;
        }
    }
}