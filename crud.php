<?php
class CRUDOperations {
	private $mysqli = null;
	
	public function __constructor($hmysqli) {
		$this->mysqli = $hmysqli;
	}
	public function CreateUser(...$args) {
		if (count($args) >= 2) {
			$name = $this->sanitizeInput($args[0]);
			$email = $this->sanitizeInput($args[1]);
			$sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
			if ($this->mysqli->query($sql) === TRUE) {
				echo "New user created successfully";
			} else {
				echo "Error creating of user record: " . $this->mysqli->error;
			}
		} else {
            echo "Not enough parameters to create New user";
        }
	}
	
	public function ReadUser(...$args): array
    {
		$user=[];
		if (count($args) >= 1) {
			$id = $this->sanitizeInput($args[0]);
			$sql = "SELECT * FROM users WHERE id=$id";
			$result = $this->mysqli->query($sql);
			$user = $result->fetch_assoc(); 
			if (count($user)) {
				echo "Info about user with ID: $id readed successfully";
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
			if ($this->mysqli->query($sql) === TRUE) {
				echo "User with ID: $id updated successfully";
			} else {
				echo "Error updating record: " . $this->mysqli->error;
			}
		}
	}
	
	public function DeleteUser(...$args) {
		if (count($args) >= 1) {
			$id = $this->sanitizeInput($args[0]);
			$sql = "DELETE FROM users WHERE id=$id";
			if ($this->mysqli->query($sql) === TRUE) {
				echo "User with ID: $id deleted successfully";
			} else {
				echo "Error deleting record: " . $this->mysqli->error;
			}
		}
	}
	
	private function sanitizeInput($data) {
		return $this->mysqli->real_escape_string(htmlspecialchars(trim($data))); 
	}
}
//DB server MySQL, user must have all grants under existing DB user_set
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'user_set';
$mysqli = new mysqli($host, $username, $password);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
try {
	echo "Start\n";
	$mysqli->select_db($database);
	$querystr = "CREATE TABLE IF NOT EXISTS users (ID BIGINT NOT NULL AUTO_INCREMENT, name varchar(50) NOT NULL, email varchar(40) DEFAULT '', PRIMARY KEY(ID)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
	$mysqli->query($querystr, MYSQLI_USE_RESULT);
	$crud = new CRUDOperations($mysqli);
	$crud->CreateUser("Paul Attredis", "paul_attr@space.com");
	$crud->CreateUser("Vladimir Harrkonen", "vlad_harrk@space.com");
	$querystr = "SELECT * FROM users";
	$result = $mysqli->query($querystr);
	print_r($result->fetch_all(MYSQLI_ASSOC));
	echo "------------------\n";
	$beings = $crud->ReadUser(1);
	print_r($beings);
	echo "------------------\n";
	$crud->UpdateUser(1, "Muaddib", "muaddib@space.com");
	$querystr = "SELECT * FROM `users`";
	$result = $mysqli->query($querystr);
	print_r($result->fetch_all(MYSQLI_ASSOC));
	echo "------------------\n";
	$crud->DeleteUser(2);
	$querystr = "SELECT * FROM users";
	$result = $mysqli->query($querystr);
	print_r($result->fetch_all(MYSQLI_ASSOC));
	echo "------------------\n";
	echo "Done";
	
} catch (mysqli_sql_exception | TypeError | Exception $exception) {
    echo "HANDLE ERROR: {$exception->getMessage()}";
} finally {
        $mysqli->close();
}
