<?php
namespace Models;

use Database\Database;
use PDO;
use PDOException;


class Course {
    protected $id;
    protected $title;
    protected $description;
    protected $category;
    protected $price;
    protected $status;
    protected $media_path;
    protected $is_approved;
    protected $id_author;
    protected $content_type;

    public function __construct($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type) {
        // $this->id = $id;
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
    public static function getByStudent($user_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Courses JOIN Reservations ON Courses.id_course = Reservations.id_course WHERE Reservations.id_user = :student_id");
        $stmt->bindValue(':student_id', $user_id, PDO::PARAM_INT);
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
        try {
            // Start transaction
            $db->beginTransaction();
            
            // First delete related records from course_tags
            $stmt = $db->prepare("DELETE FROM Course_Tags WHERE id_course = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Then delete any records from Reservations if they exist
            $stmt = $db->prepare("DELETE FROM Reservations WHERE id_course = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Finally delete the course
            $stmt = $db->prepare("DELETE FROM Courses WHERE id_course = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            // If we got here, commit the transaction
            $db->commit();
            return true;
        } catch (PDOException $e) {
            // If anything went wrong, roll back the transaction
            $db->rollBack();
            error_log("Error deleting course: " . $e->getMessage());
            return false;
        }
    }

    public static function update($id, $title, $description, $category, $price, $status, $media_path, $is_approved, $content_type) {
        // Validate content type
        // $valid_content_types = ['video', 'file', 'image', 'text'];
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
    public static function updateStatus($course_id) {
        $db = Database::getInstance()->getConnection();
        
        // Check the current status of the course
        $stmt = $db->prepare("SELECT status FROM Courses WHERE id_course = :id");
        $stmt->bindValue(':id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($course && $course['status'] === 'Draft') {
            // Update the status to 'Published' if it's currently 'Draft'
            $stmt = $db->prepare("UPDATE Courses SET status = 'Published' WHERE id_course = :id");
            return $stmt->execute([':id' => $course_id]);
        }
    
        return false; // Return false if the course was not in 'Draft'
    }
    
    
    
        public static function getTopCourse() {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("
                SELECT Courses.title, COUNT(Reservations.id_reservation) as students 
                FROM Courses 
                LEFT JOIN Reservations ON Courses.id_course = Reservations.id_course 
                GROUP BY Courses.id_course 
                ORDER BY students DESC 
                LIMIT 1
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    
        public static function countAll() {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT COUNT(*) as total FROM Courses");
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }
    
        public static function getAll() {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM Courses");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function getContentType() {  
        return $this->content_type;; }
}



?>
