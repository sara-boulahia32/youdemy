<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../src/config/autoloader.php');
use Database\Database;
use Models\CoursTexte;
use Models\CoursFile;
use Models\CoursVideo;

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Start transaction for data consistency
        $db->beginTransaction();
        
        // Basic input sanitization
        $title = htmlspecialchars(trim($_POST['title']));
        $description = htmlspecialchars(trim($_POST['description']));
        $category = isset($_POST['category']) && !empty($_POST['category']) 
    ? htmlspecialchars(trim($_POST['category'])) 
    : null;

if ($category === null) {
    throw new Exception("Category cannot be null or empty.");
}

        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $status = 'Draft';
        $content_type = strtolower(trim($_POST['content_type']));
        $media_path = '';
        $is_approved = 0;

        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User is not logged in.");
        }
        $id_author = $_SESSION['user_id'];

        $course_id;
        
        // Handle file upload or text input based on content type
        if ($content_type === 'video' || $content_type === 'file' || $content_type === 'image') {
            if (isset($_FILES['content']) && $_FILES['content']['error'] == 0) {
                // Validate file type and size
                $allowed_types = [
                    'video' => ['video/mp4', 'video/webm'],
                    'image' => ['image/jpeg', 'image/png', 'image/gif'],
                    'file' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                ];
                
                $file_type = $_FILES['content']['type'];
                if (!in_array($file_type, $allowed_types[$content_type])) {
                    throw new Exception("Invalid file type.");
                }
                
                // Limit file size (10MB)
                if ($_FILES['content']['size'] > 10485760) {
                    throw new Exception("File too large. Maximum size is 10MB.");
                }
                
                // Generate secure filename
                $file_extension = pathinfo($_FILES['content']['name'], PATHINFO_EXTENSION);
                $secure_filename = uniqid() . '_' . time() . '.' . $file_extension;
                $media_path = '../../public/uploads/' . $secure_filename;
                
                if (!move_uploaded_file($_FILES['content']['tmp_name'], $media_path)) {
                    throw new Exception("Failed to upload file.");
                }

                $course = new CoursVideo($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type);
                $course_id = $course->create();
            } else {
                throw new Exception("No file uploaded or upload error.");
            }
        } else if ($content_type === 'text') {
            if (isset($_POST['content']) && !empty($_POST['content'])) {
                $media_path = htmlspecialchars($_POST['content']);
                
                $course = new CoursTexte($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type);
                $course_id = $course->create();
            } else {
                throw new Exception("Text content cannot be empty.");
            }
        } else {
            throw new Exception("Invalid content type.");
        }

        // Process tags
        $selectedTags = json_decode($_POST['selectedTags'], true);
        if (!is_array($selectedTags)) {
            throw new Exception("Invalid tags format.");
        }

        foreach ($selectedTags as $tag) {
            if (empty($tag['name']) || strlen($tag['name']) > 50) {
                throw new Exception("Invalid tag name.");
            }
            $tagName = htmlspecialchars(trim($tag['name']));
            
            $stmt = $db->prepare("INSERT INTO Tags (name) VALUES (?) ON DUPLICATE KEY UPDATE id_tags=LAST_INSERT_ID(id_tags)");
            $stmt->execute([$tagName]);
            $tag_id = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO Course_Tags (id_course, id_tags) VALUES (?, ?)");
            if (!$stmt->execute([$course_id, $tag_id])) {
                throw new Exception("Error linking tag.");
            }
        }

        $db->commit();
        echo json_encode(['success' => 'Course and tags inserted successfully']);
        
    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollBack();
        }
        http_response_code(500);
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>