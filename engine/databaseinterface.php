<?php

class PDODatabase
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