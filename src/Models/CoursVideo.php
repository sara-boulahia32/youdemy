<?php

namespace Models;


use Database\Database;
// use Interfaces\ICreateCourse;
use Models\Course;

class CoursVideo extends Course {
    public $content;
    public function create() {
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO Courses (title, description, category, price, content, status, media_path, content_type, is_approved, id_author) 
                             VALUES (:title, :description, :content, :category, :price, :status, :media_path, 'video', :is_approved, :id_author)");
        
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':category' => $this->category,
            ':price' => $this->price,
            ':content' => $this->content,
            ':status' => $this->status,
            ':media_path' => $this->media_path,
            ':is_approved' => $this->is_approved,
            ':id_author' => $this->id_author
        ]);
        
        $this->id = $db->lastInsertId();
        return $this->id;
    }
}

