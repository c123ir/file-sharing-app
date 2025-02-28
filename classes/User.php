<?php
/**
 * کلاس مدیریت کاربران
 */
class User {
    private $db;
    
    /**
     * سازنده کلاس
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * ثبت‌نام کاربر جدید
     */
    public function register($data) {
        try {
            // هش کردن رمز عبور
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // آماده‌سازی داده‌ها برای درج
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $hashedPassword,
                'full_name' => $data['full_name'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // درج کاربر در پایگاه داده
            $userId = $this->db->insert('users', $userData);
            
            return $userId;
        } catch (Exception $e) {
            // رهگیری خطا برای اهداف دیباگ
            error_log("Error in User::register: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ورود کاربر
     */
    public function login($username, $password, $remember = false) {
        try {
            // بررسی کاربر با نام کاربری یا ایمیل
            $this->db->query("SELECT * FROM users WHERE username = :username OR email = :email");
            $this->db->bind(':username', $username);
            $this->db->bind(':email', $username);
            
            $user = $this->db->fetch();
            
            // اگر کاربر پیدا شد و رمز عبور صحیح است
            if ($user && password_verify($password, $user['password'])) {
                // به‌روزرسانی آخرین زمان ورود
                $this->db->query("UPDATE users SET last_login = NOW() WHERE id = :id");
                $this->db->bind(':id', $user['id']);
                $this->db->execute();
                
                // اضافه کردن گزینه remember me
                $user['remember_me'] = $remember;
                
                // تنظیم اطلاعات کاربر در نشست
                Session::setLoggedInUser($user);
                
                return $user;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error in User::login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * یافتن کاربر با شناسه
     */
    public function findById($id) {
        try {
            $this->db->query("SELECT id, username, email, full_name, created_at FROM users WHERE id = :id");
            $this->db->bind(':id', $id);
            
            return $this->db->fetch();
        } catch (Exception $e) {
            error_log("Error in User::findById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * یافتن کاربر با نام کاربری
     */
    public function findByUsername($username) {
        try {
            $this->db->query("SELECT * FROM users WHERE username = :username");
            $this->db->bind(':username', $username);
            
            return $this->db->fetch();
        } catch (Exception $e) {
            error_log("Error in User::findByUsername: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * یافتن کاربر با ایمیل
     */
    public function findByEmail($email) {
        try {
            $this->db->query("SELECT * FROM users WHERE email = :email");
            $this->db->bind(':email', $email);
            
            return $this->db->fetch();
        } catch (Exception $e) {
            error_log("Error in User::findByEmail: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * به‌روزرسانی اطلاعات کاربر
     */
    public function update($id, $data) {
        try {
            // آماده‌سازی داده‌ها برای به‌روزرسانی
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'] ?? ''
            ];
            
            // به‌روزرسانی کاربر در پایگاه داده
            return $this->db->update('users', $userData, "id = {$id}");
        } catch (Exception $e) {
            error_log("Error in User::update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * تغییر رمز عبور کاربر
     */
    public function changePassword($id, $newPassword) {
        try {
            // هش کردن رمز عبور جدید
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // به‌روزرسانی رمز عبور در پایگاه داده
            $this->db->query("UPDATE users SET password = :password WHERE id = :id");
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':id', $id);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in User::changePassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * حذف کاربر
     */
    public function delete($id) {
        try {
            $this->db->query("DELETE FROM users WHERE id = :id");
            $this->db->bind(':id', $id);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in User::delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * گرفتن همه کاربران (برای مدیریت)
     */
    public function getAllUsers() {
        try {
            $this->db->query("SELECT id, username, email, full_name, created_at, last_login FROM users ORDER BY created_at DESC");
            
            return $this->db->fetchAll();
        } catch (Exception $e) {
            error_log("Error in User::getAllUsers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * بررسی وجود نام کاربری یا ایمیل
     */
    public function exists($username, $email) {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM users WHERE username = :username OR email = :email");
            $this->db->bind(':username', $username);
            $this->db->bind(':email', $email);
            
            $result = $this->db->fetch();
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error in User::exists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * جستجوی کاربران
     */
    public function search($searchTerm) {
        try {
            $this->db->query("SELECT id, username, email, full_name FROM users 
                             WHERE username LIKE :search 
                             OR email LIKE :search 
                             OR full_name LIKE :search");
            $this->db->bind(':search', "%{$searchTerm}%");
            
            return $this->db->fetchAll();
        } catch (Exception $e) {
            error_log("Error in User::search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * گرفتن تعداد کل فایل‌های کاربر
     */
    public function countUserFiles($userId) {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM files WHERE user_id = :user_id");
            $this->db->bind(':user_id', $userId);
            
            $result = $this->db->fetch();
            
            return $result['count'];
        } catch (Exception $e) {
            error_log("Error in User::countUserFiles: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * گرفتن حجم کل فایل‌های کاربر
     */
    public function totalUserFileSize($userId) {
        try {
            $this->db->query("SELECT SUM(file_size) as total_size FROM files WHERE user_id = :user_id");
            $this->db->bind(':user_id', $userId);
            
            $result = $this->db->fetch();
            
            return $result['total_size'] ? $result['total_size'] : 0;
        } catch (Exception $e) {
            error_log("Error in User::totalUserFileSize: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * بررسی اینکه آیا کاربر مدیر است
     */
    public function isAdmin($userId) {
        try {
            $this->db->query("SELECT is_admin FROM users WHERE id = :id");
            $this->db->bind(':id', $userId);
            
            $result = $this->db->fetch();
            
            return $result && isset($result['is_admin']) && $result['is_admin'] == 1;
        } catch (Exception $e) {
            error_log("Error in User::isAdmin: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تنظیم وضعیت مدیر برای کاربر
     */
    public function setAdminStatus($userId, $isAdmin) {
        try {
            $this->db->query("UPDATE users SET is_admin = :is_admin WHERE id = :id");
            $this->db->bind(':is_admin', $isAdmin ? 1 : 0);
            $this->db->bind(':id', $userId);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in User::setAdminStatus: " . $e->getMessage());
            return false;
        }
    }
}