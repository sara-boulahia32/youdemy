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
        if($stmt->execute()){
            return $stmt;
        }else{
            return null;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
}
