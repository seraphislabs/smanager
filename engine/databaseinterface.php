<?php

class PDODatabase
{
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $pdo;

    public function __construct($host, $username, $password, $dbname)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect();
    }

    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function ValidateLogin($_email, $_password) {
        // Retreive password hash from database
			$retVal = $this->GetLoginPasswordHash($_email);
			if (!empty($retVal)) {
				if (PasswordEncrypt::Check($_password, $retVal)) {
					return true;
				}
			}

            return false;
    }

    function GetLoginPasswordHash($_email) {
        $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
        $stmt->bindParam(":email", $_email);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $passHash = $results[0]['password'];

            return $passHash;
        }
    }

    public function GetLoginCompanyID($_email, $_password) {
        if ($this->ValidateLogin($_email, $_password)) {
            $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $companyId = $results[0]['companyid'];

                return $companyId;
            }
        }
    }

    public function GetLoginInformation($_email, $_password) {
        if ($this->ValidateLogin($_email, $_password)) {
            $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $results[0];
            }
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