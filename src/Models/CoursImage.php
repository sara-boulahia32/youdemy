<?php
namespace Models;

use Database\Database;
use Interfaces\ICreateCourse;
use Models\Course;
class CoursImage extends Course {
    public function create() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO Courses (title, description, category, price, status, media_path, content_type, is_approved, id_author) 
                             VALUES (:title, :description, :category, :price, :status, :media_path, 'image', :is_approved, :id_author)");
        
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category' => $this->category,
            ':price' => $this->price,
            ':status' => $this->status,
            ':media_path' => $this->media_path,
            ':is_approved' => $this->is_approved,
            ':id_author' => $this->id_author
        ]);
        
        $this->id = $db->lastInsertId();
        return $this->id;
    }
}