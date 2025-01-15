<?php

namespace Models;

use Database\Database;
use PDO;
use PDOException;

class User {
    private $id;
    private $name;
    private $email;
    private $passwordHash;
    private $role; 
    private $is_active;

    public function __construct($id, $name, $email, $passwordHash = null, $role = 'student', $is_active = 1) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->is_active = $is_active;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; } 
    public function getIsActive() { return $this->is_active; }

    // Password hashing method
    private function setPasswordHash($password) {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }

    // Save user to the database
    public function save() {
        $db = Database::getInstance()->getConnection();
        try {
            if ($this->id) {
                // Update user
                $stmt = $db->prepare("UPDATE users SET name = :name, email = :email, role = :role, is_active = :is_active WHERE id = :id");
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':role', $this->role, PDO::PARAM_STR);
                $stmt->bindParam(':is_active', $this->is_active, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Insert new user
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (:name, :email, :password, :role, :is_active)");
                $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $this->passwordHash, PDO::PARAM_STR);
                $stmt->bindParam(':role', $this->role, PDO::PARAM_STR);
                $stmt->bindParam(':is_active', $this->is_active, PDO::PARAM_INT);
                $stmt->execute();
                $this->id = $db->lastInsertId(); // Set the new user ID
            }
            return $this->id;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new \Exception("An error occurred while saving the user: " . $e->getMessage());
        }
    }

    // Search user by name
    public function searchUserByName($name) {
        $db = Database::getInstance()->getConnection();

        // Prepare the SQL query
        $stmt = $db->prepare("SELECT * FROM users WHERE name LIKE :name");

        // Bind the parameter for name search (using wildcards for partial match)
        $stmt->bindValue(':name', '%' . $name . '%', PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch all matching users
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return an array of User objects
        $users = [];
        foreach ($results as $result) {
            $users[] = new User(
                $result['id'],
                $result['name'],
                $result['email'],
                $result['password'],
                $result['role'], 
                $result['is_active']
            );
        }

        return $users;
    }

    // Get user by ID
    public function getUserById($id) {
        $db = Database::getInstance()->getConnection();

        // Prepare the SQL query
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User(
                $result['id'],
                $result['name'],
                $result['email'],
                $result['password'],
                $result['role'], 
                $result['is_active']
            );
        }

        return null; // User not found
    }

    // Static method to search user by email
    public static function findByEmail($email) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User($result['id_user'], $result['name'], $result['email'], $result['password'], $result['role'], $result['is_active']);
        }

        return null;
    }

    // Method to register a new user (signup)
    public static function signup($name, $email, $password, $role, $is_active) {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }

        // Validate password length
        if (strlen($password) < 6) {
            throw new \Exception("Password must be at least 6 characters long");
        }

        // Sanitize name fields
        $name = htmlspecialchars($name);

        // Check if email already exists
        if (self::findByEmail($email)) {
            throw new \Exception("Email is already registered");
        }

        // Create a new user object
        $user = new User(null, $name, $email, null, $role, $is_active);
        $user->setPasswordHash($password); // Hash the password
        return $user->save();
    }

    // Method to login (signin)
    public static function signin($email, $password) {
        $user = self::findByEmail($email);

        // Check if user exists and password is correct
        if (!$user || !password_verify($password, $user->passwordHash)) {
            throw new \Exception("Invalid email or password");
        }

        return $user; // Successful login
    }

    // Method to change the user's password
    public function changePassword($newPassword) {
        $this->setPasswordHash($newPassword); // Hash the new password
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $this->passwordHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
