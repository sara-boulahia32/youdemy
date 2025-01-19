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
    private $id_author;
    private $content_type;

    public function __construct($id, $title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->price = $price;
        $this->status = $status;
        $this->media_path = $media_path;
        $this->is_approved = $is_approved;
        $this->id_author = $id_author;
        $this->content_type = $content_type;
    }

    public static function getPaginated($limit, $offset) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = new self(
                $row['id_course'],
                $row['title'],
                $row['description'],
                $row['category'],
                $row['price'],
                $row['status'],
                $row['media_path'],
                $row['is_approved'],
                $row['id_author'],
                $row['content_type']
            );
        }

        return $courses;
    }

    public static function getTotalCourses() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT COUNT(*) FROM Courses")->fetchColumn();
    }

    public static function getByCategory($category, $limit, $offset) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses WHERE category = :category LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':category', $category, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = new self(
                $row['id_course'],
                $row['title'],
                $row['description'],
                $row['category'],
                $row['price'],
                $row['status'],
                $row['media_path'],
                $row['is_approved'],
                $row['id_author'],
                $row['content_type']

            );
        }

        return $courses;
    }
    public static function search($keyword, $limit, $offset) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT DISTINCT Courses.* FROM Courses
            LEFT JOIN Course_Tags ON Courses.id_course = Course_Tags.id_course
            LEFT JOIN Tags ON Course_Tags.id_tags = Tags.id_tags
            LEFT JOIN Categories ON Courses.category = Categories.id_category
            WHERE Courses.title LIKE :keyword 
               OR Courses.description LIKE :keyword 
               OR Categories.name LIKE :keyword
               OR Tags.name LIKE :keyword
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = new self(
                $row['id_course'],
                $row['title'],
                $row['description'],
                $row['category'],
                $row['price'],
                $row['status'],
                $row['media_path'],
                $row['is_approved'],
                $row['id_author'],
                $row['content_type']

            );
        }

        return $courses;
    }

    public function getTags() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT Tags.id_tags, Tags.name FROM Tags INNER JOIN Course_Tags ON Tags.id_tags = Course_Tags.id_tags WHERE Course_Tags.id_course = ?");
        $stmt->execute([$this->id]);
        $tags = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = new Tag($row['id_tags'], $row['name']);
        }
        return $tags;
    }
    public static function getById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses WHERE id_course = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $row ? new self(
            $row['id_course'],
            $row['title'],
            $row['description'],
            $row['category'],
            $row['price'],
            $row['status'],
            $row['media_path'],
            $row['is_approved'],
            $row['id_author'],
            $row['content_type']


        ) : null;
    }
    public function getEnrollments() { $db = Database::getInstance()->getConnection(); $stmt = $db->prepare("SELECT * FROM Reservations WHERE id_course = ?"); $stmt->execute([$this->id]); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
    public static function getByAuthor($author_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses WHERE id_author = :author_id");
        $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = new self(
                $row['id_course'],
                $row['title'],
                $row['description'],
                $row['category'],
                $row['price'],
                $row['status'],
                $row['media_path'],
                $row['is_approved'],
                $row['id_author'],
                $row['content_type']
            );
        }

        return $courses;
    }

    public static function deleteById($id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM Courses WHERE id_course = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function update($id, $title, $description, $category, $price, $status, $media_path, $is_approved, $content_type) {
        // Validate content type
        $valid_content_types = ['video', 'file', 'image', 'text'];
        // if (!in_array(strtolower($content_type), $valid_content_types)) {
        //     throw new Exception("Invalid content type");
        // }
    
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE Courses SET 
            title = :title, 
            description = :description, 
            category = :category, 
            price = :price, 
            status = :status, 
            media_path = :media_path, 
            is_approved = :is_approved, 
            content_type = :content_type 
            WHERE id_course = :id");
            
        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':description' => $description,
            ':category' => $category,
            ':price' => $price,
            ':status' => $status,
            ':media_path' => $media_path,
            ':is_approved' => $is_approved,
            ':content_type' => strtolower($content_type)  // Ensure lowercase
        ]);
    }
    

    

    public function getTitle() {
        return $this->title;
    }
    public function getid() {
        return $this->id;
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
    public function getauthor() {
        return $this->id_author;
    }
    public function getContentType() { // Your logic here to get the content type 
        return $this->content_type;; }
}



?>
