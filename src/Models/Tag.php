<?php
namespace Models;

use Database\Database;
use PDO;
use PDOException;


class Tag {
    private $id;
    private $name;

    public function __construct($id = null, $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    public function add() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO Tag (name) VALUES (:name)");
        $stmt->bindParam(':name', $this->name);

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
            return true;
        } else {
            return false;
        }
    }

    public static function getById($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Tag WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Tag($result['id'], $result['name']);
        }
        return null;
    }

    public static function getAll() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT * FROM Tags");
        $stmt->execute(); $tags = []; while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $tags[] = new self($row['id_tags'], $row['name']); } return $tags;
    }

    public static function search($query) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Tags WHERE name LIKE ?");
        $stmt->execute(["%$query%"]);
        $tags = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = new self($row['id_tags'], $row['name']);
        }
        return $tags;
    }
    public function update() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE Tag SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function getId() {
        return $this->id;
    }

    public function getname() {
        return $this->name;
    }

    public function setname($name) {
        $this->name = $name;
    }
    
}
