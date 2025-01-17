<?php
namespace Models;

use Database\Database;
use PDO;

class Course {
    private $id;
    private $title;
    private $description;
    private $category;
    private $price;
    private $status;
    private $media_path;
    private $is_approved;

    public function __construct($id, $title, $description, $category, $price, $status, $media_path, $is_approved) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->price = $price;
        $this->status = $status;
        $this->media_path = $media_path;
        $this->is_approved = $is_approved;
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses");
        $stmt->execute();
        $courses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courses[] = new self($row['id_course'], $row['title'], $row['description'], $row['category'], $row['price'], $row['status'], $row['media_path'], $row['is_approved']);
        }
        return $courses;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getMediaPath() {
        return $this->media_path;
    }

    public function isApproved() {
        return $this->is_approved;
    }

    public function getTags() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT Tags.id_tags, Tags.name FROM Tags INNER JOIN Course_Tags ON Tags.id_tags = Course_Tags.id_tags WHERE Course_Tags.id_course = ?");
        $stmt->execute([$this->id]);
        $tags = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = new Tag($row['id_tags'], $row['name']);
        }
    
        // Log the fetched tags for debugging
        error_log("Course ID: " . $this->id . " Tags: " . json_encode($tags));
        
        return $tags;
    }
    
}


?>
