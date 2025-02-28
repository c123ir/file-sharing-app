<?php
/**
 * کلاس مدیریت پایگاه داده
 */
class Database {
    private static $instance = null;
    private $connection;
    private $error;
    private $statement;
    private $dbConnected = false;

    /**
     * سازنده کلاس برای اتصال به پایگاه داده
     */
    private function __construct() {
        // اتصال PDO به پایگاه داده
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->dbConnected = true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /**
     * دریافت نمونه از کلاس (الگوی Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * آماده‌سازی دستور SQL با پارامترهای امن
     */
    public function query($sql) {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }

    /**
     * اجرای یک prepared statement با پارامترها
     */
    public function execute($params = []) {
        return $this->statement->execute($params);
    }

    /**
     * گرفتن همه نتایج
     */
    public function fetchAll() {
        $this->execute();
        return $this->statement->fetchAll();
    }

    /**
     * گرفتن یک سطر از نتایج
     */
    public function fetch() {
        $this->execute();
        return $this->statement->fetch();
    }

    /**
     * گرفتن یک سطر با ID خاص
     */
    public function fetchById($table, $id, $idColumn = 'id') {
        $this->query("SELECT * FROM {$table} WHERE {$idColumn} = :id");
        $this->bind(':id', $id);
        return $this->fetch();
    }

    /**
     * شمارش تعداد سطرهای نتیجه
     */
    public function rowCount() {
        return $this->statement->rowCount();
    }

    /**
     * گرفتن آخرین ID درج شده
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * اتصال پارامترها به دستور SQL
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * شروع یک تراکنش
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * تعهد تراکنش
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * بازگشت تراکنش
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * بررسی وضعیت اتصال به پایگاه داده
     */
    public function isConnected() {
        return $this->dbConnected;
    }

    /**
     * گرفتن خطای اتصال پایگاه داده
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * درج داده در پایگاه داده
     */
    public function insert($table, $data) {
        // ساخت کوئری
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        // اجرای کوئری
        $this->query($sql);
        
        // اتصال پارامترها
        foreach ($data as $key => $value) {
            $this->bind(':' . $key, $value);
        }
        
        // اجرا
        return $this->execute() ? $this->lastInsertId() : false;
    }
    
    /**
     * به‌روزرسانی داده در پایگاه داده
     */
    public function update($table, $data, $where) {
        // ساخت بخش SET کوئری
        $set = '';
        foreach ($data as $key => $value) {
            $set .= $key . ' = :' . $key . ', ';
        }
        $set = rtrim($set, ', ');
        
        // ساخت کل کوئری
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        // اجرای کوئری
        $this->query($sql);
        
        // اتصال پارامترها
        foreach ($data as $key => $value) {
            $this->bind(':' . $key, $value);
        }
        
        // اجرا
        return $this->execute();
    }
    
    /**
     * حذف داده از پایگاه داده
     */
    public function delete($table, $where) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql);
        return $this->execute();
    }
}