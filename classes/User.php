<?php
class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $username;
    public $password;
    public $email;
    public $first_name;
    public $last_name;
    public $phone;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                (username, password, email, first_name, last_name, phone)
                VALUES
                (:username, :password, :email, :first_name, :last_name, :phone)";

        $stmt = $this->conn->prepare($query);

        // Clean and hash data
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':phone', $this->phone);

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function login($username, $password) {
        $query = "SELECT user_id, username, password, first_name, last_name 
                 FROM " . $this->table . " 
                 WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password'])) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                return true;
            }
        }
        return false;
    }

    public function getUserProfile($user_id) {
        $query = "SELECT user_id, username, email, first_name, last_name, phone, created_at 
                 FROM " . $this->table . " 
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($user_id, $data) {
        $query = "UPDATE " . $this->table . "
                 SET first_name = :first_name,
                     last_name = :last_name,
                     email = :email,
                     phone = :phone
                 WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $data['first_name'] = htmlspecialchars(strip_tags($data['first_name']));
        $data['last_name'] = htmlspecialchars(strip_tags($data['last_name']));
        $data['email'] = htmlspecialchars(strip_tags($data['email']));
        $data['phone'] = htmlspecialchars(strip_tags($data['phone']));

        // Bind parameters
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $user_id);

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updatePassword($user_id, $current_password, $new_password) {
        // First verify current password
        $query = "SELECT password FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($current_password, $row['password'])) {
            // Update to new password
            $query = "UPDATE " . $this->table . "
                     SET password = :password
                     WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id);

            return $stmt->execute();
        }
        return false;
    }

    public function usernameExists() {
        $query = "SELECT user_id FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function emailExists() {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
} 