<?php

interface DatabaseInterface
{
    public function connect($host, $username, $password, $database);

    public function query($sql, $params = []);

    public function fetch($sql, $params = []);

    public function fetchAll($sql, $params = []);

    public function insert($table, $data);

    public function update($table, $data, $condition);

    public function delete($table, $condition);
}

class PDODatabase implements DatabaseInterface
{
    private $connection;

    public function connect($host, $username, $password, $database)
    {
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function fetch($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $params = array_values($data);

        $statement = $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $condition)
    {
        $setClause = implode(', ', array_map(function ($column) {
            return "$column=?";
        }, array_keys($data)));

        $sql = "UPDATE $table SET $setClause WHERE $condition";
        $params = array_values($data);

        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $condition)
    {
        $sql = "DELETE FROM $table WHERE $condition";
        return $this->query($sql)->rowCount();
    }
}

/* Examples

$database = new PDODatabase();
$database->connect('localhost', 'username', 'password', 'database');

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
];
$insertedId = $database->insert('users', $data);
echo "Inserted ID: $insertedId\n";

$data = [
    'name' => 'Jane Smith',
    'age' => 30
];
$affectedRows = $database->update('users', $data, 'id = 1');
echo "Affected rows: $affectedRows\n";

$affectedRows = $database->delete('users', 'id = 1');
echo "Affected rows: $affectedRows\n";
*/

?>