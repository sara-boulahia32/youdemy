<?php
namespace Models;

use Database\Database;
use PDO;

class Instructor {
    private $id;
    private $name;
    private $title;

    public function __construct($id, $name, $title) {
        $this->id = $id;
        $this->name = $name;
        $this->title = $title;
    }

    public static function getById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Instructors WHERE id_instructor = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new self($row['id_instructor'], $row['name'], $row['title']) : null;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getTitle() {
        return $this->title;
    }
}
