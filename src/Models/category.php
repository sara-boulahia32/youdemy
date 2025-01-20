<?php
namespace Models;

use Database\Database;
use PDO;

class Category {
    private $id;
    private $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = new self($row['id_category'], $row['name']);
        }

        return $categories;
    }

    public static function getById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories WHERE id_category = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new self($row['id_category'], $row['name']) : null;
    }
    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO Categories (name) VALUES (:name)");
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function deleteById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM Categories WHERE id_category = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function getCourseDistribution() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT category, COUNT(*) as count 
            FROM Courses 
            GROUP BY category
        ");
        $distribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $distribution[$row['category']] = $row['count'];
        }
        return $distribution;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}
