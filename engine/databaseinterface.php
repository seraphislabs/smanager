<?php
$pagesDir = __DIR__ . '/database';
$pages = scandir($pagesDir);
foreach ($pages as $page) {
    if (pathinfo($page, PATHINFO_EXTENSION) === 'php') {
        require_once $pagesDir . '/' . $page;
    }
}

class DatabaseManager
{
    use DatabaseAccounts;
    use DatabaseEmployees;
    use DatabaseEmployeeRoles;
    use DatabaseEmployeeShifts;
    use DatabaseLocations;
    use DatabaseHolidaySchedules;
    use DatabaseContacts;
    use DatabaseMaster;
}

class DBI {
    private static $instance = null;
    private static $instance2 = null;
    private $conn;

    private function __construct($_database) {
        $this->connect($_database);
    }

    public static function getInstance($_database) {
        if ($_database == "servicemanager") {
            if (!self::$instance) {
                self::$instance = new DBI($_database);
            }
            return self::$instance;
        }
        else {
            if (!self::$instance2) {
                self::$instance2 = new DBI($_database);
            }
            return self::$instance2;
        }
    }

    public static function closeAll() {
        if (self::$instance) {
            self::$instance->close();
        }

        if (self::$instance2) {
            self::$instance2->close();
        }
    }

    public function close() {
        $this->conn = null;
    }

    public function insert($tableName, $data) {
        if(empty($data)) {
            return false;
        }
    
        $columns = array_keys($data);
        $placeholders = array_map(function($item) {
            return ':' . $item;
        }, $columns);
    
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
    
        $stmt = $this->conn->prepare($query);
    
        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }
    
        $stmt->execute($params);
        return $this->conn->lastInsertId();
    }

    private function connect($_database) {
        $dbInfo = $GLOBALS['dbInfo'];

        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=' . $_database, $dbInfo['dusername'], $dbInfo['dpassword']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo 'Connection Error: ' . $exception->getMessage();
        }

        return $this->conn;
    }

    public function fetchAll($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($tableName, $whereCondition) {
        if (empty($whereCondition)) {
            return false;
        }

        $query = sprintf(
            'DELETE FROM %s WHERE %s',
            $tableName,
            $whereCondition
        );

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function exists($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public function fetch($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function batchInsert($tableName, $data) {
        if(empty($data)) {
            return false;
        }

        $firstRow = current($data);
        $columns = array_keys($firstRow);

        $placeholders = array_map(function($item) {
            return ':' . $item;
        }, $columns);

        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->conn->prepare($query);

        foreach ($data as $row) {
            $params = [];
            foreach ($row as $key => $value) {
                $params[":$key"] = $value;
            }
            $stmt->execute($params);
        }

        return true;
    }

    public function update($tableName, $data, $whereConditions) {
        if (empty($data) || empty($whereConditions)) {
            return false;
        }
    
        // Prepare the SET clause for the update query
        $setClause = implode(', ', array_map(function ($column) {
            return $column . ' = :set_' . $column;
        }, array_keys($data)));
    
        // Prepare the WHERE clause for the update query
        $whereClause = implode(' AND ', array_map(function ($condition, $index) {
            return $condition[0] . ' ' . $condition[1] . ' :where_' . $index;
        }, $whereConditions, array_keys($whereConditions)));
    
        $query = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $tableName,
            $setClause,
            $whereClause
        );
    
        $stmt = $this->conn->prepare($query);
    
        // Prepare SET parameters
        $setParams = array_combine(
            array_map(function ($key) { return ':set_' . $key; }, array_keys($data)),
            $data
        );
    
        // Prepare WHERE parameters
        $whereParams = array_combine(
            array_map(function ($index) { return ':where_' . $index; }, array_keys($whereConditions)),
            array_column($whereConditions, 2)
        );
    
        // Merge SET and WHERE parameters
        $params = array_merge($setParams, $whereParams);
    
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}

class RDB {
    private static $instance = null;
    private $redis;

    private function __construct() {
        $dbInfo = $GLOBALS['dbInfo'];
        $this->redis = new Redis();
        $this->redis->connect($dbInfo['rip'], 6379);
        $this->redis->auth($dbInfo['rpassword']);
    }

    public static function closeInstance() {
        if (self::$instance) {
            self::$instance->close();
            self::$instance = null;
        }
    }

    public function close() {
        $this->redis->close();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new RDB();
        }
        return self::$instance;
    }

    public function get($key) {
        $got = $this->redis->get($key);

        if ($got !== false) {
            return json_decode($got, true);
        }
        return false;
    }

    public function set($key, $value, $expiry = 0) {
        if ($expiry > 0) {
            return $this->redis->setex($key, $expiry, $value);
        } else {
            return $this->redis->set($key, $value);
        }
    }

    public function extendExpiry($key, $expiry) {
        return $this->redis->expire($key, $expiry);
    }

    public function delete($key) {
        return $this->redis->del($key);
    }

    public function exists($key) {
        return $this->redis->exists($key);
    }
}