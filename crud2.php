<?php

class CRUDOperations {
	private $pdo;
	
	public function __construct($hpdo) {
		$this->pdo = $hpdo;
	}
	
	public function CreateUser(...$args) {
		if (count($args) >= 2) {
			$name = $this->sanitizeInput($args[0]);
			$email = $this->sanitizeInput($args[1]);
			$sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
			if ($this->pdo->query($sql) === FALSE) {
				echo "Error creating of user record: " . $this->pdo->errorCode();
			} else {
				echo "New user created successfully";
			}
		} else {
            echo "Not enough parameters to create a New user";
        }
	}
	
	public function ReadUser(...$args): array
    {
		$user=[];
		if (count($args) >= 1) {
			$id = $this->sanitizeInput($args[0]);
			$sql = "SELECT * FROM users WHERE id=$id";
			$result = $this->pdo->query($sql);
			$user = $result->fetch(PDO::FETCH_ASSOC); 
			if (count($user)) {
				echo "Info about user with ID: $id readed successfully";
			} else {
				echo "User with ID: $id not found";
			}
		}
		return $user;
	}
	
	public function UpdateUser(...$args) {
		if (count($args) >= 2) {
			$id = $this->sanitizeInput($args[0]);
			$name = $this->sanitizeInput($args[1]);
			if (isset($args[2])) $email = $this->sanitizeInput($args[2]);
			$sql = "UPDATE users SET name='$name'" . (isset($email) ? ", email='$email'" : "") . " WHERE id=$id";
			if ($this->pdo->query($sql) === FALSE) {
				echo "Error updating record: " . $this->pdo->errorCode();
			} else {
				echo "User with ID: $id updated successfully";
			}
		}
	}
	
	public function DeleteUser(...$args) {
		if (count($args) >= 1) {
			$id = $this->sanitizeInput($args[0]);
			$sql = "DELETE FROM users WHERE :id=$id";
			if ($this->pdo->query($sql) === FALSE) {
				echo "Error deleting record: " . $this->pdo->errorCode();
			} else {
				echo "User with ID: $id deleted successfully";
			}
		}
	}
	
	private function sanitizeInput($data) : string{
		return htmlspecialchars(trim($data)); 
	}
}
//DB server MySQL, user must have all grants under existing DB user_set
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'user_set';
$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
	echo "Start\n";
	$pdo = new PDO($dsn, $username, $password, $options);
	$querystr = "CREATE TABLE IF NOT EXISTS users (ID BIGINT NOT NULL AUTO_INCREMENT, name varchar(50) NOT NULL, email varchar(40) DEFAULT '', PRIMARY KEY(ID))";
	if ($pdo->query($querystr)) {
		$crud = new CRUDOperations($pdo);
		$crud->CreateUser("Paul Attredis", "paul_attr@space.com");
		$crud->CreateUser("Vladimir Harrkonen", "vlad_harrk@space.com");
		$querystr = "SELECT * FROM users";
		$result = $pdo->query($querystr);
		print_r($result->fetchAll());
		echo "------------------\n";
		$beings = $crud->ReadUser(1);
		print_r($beings);
		echo "------------------\n";
		$crud->UpdateUser(1, "Muaddib", "muaddib@space.com");
		$querystr = "SELECT * FROM `users`";
		$result = $pdo->query($querystr);
		print_r($result->fetchAll());
		echo "------------------\n";
		$crud->DeleteUser(2);
		$querystr = "SELECT * FROM users";
		$result = $pdo->query($querystr);
		print_r($result->fetchAll());
		echo "------------------\n";
		echo "Done";
	}
} catch (PDOException | TypeError | Exception $exception) {
    echo "HANDLE ERROR: {$exception->getMessage()}";
} finally {
        $pdo = null;
}
