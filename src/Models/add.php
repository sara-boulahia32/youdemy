<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../src/config/autoloader.php');
use Database\Database;
use Models\CourseText;
use Models\CourseVideo;
use Models\CoursTexte;
use Models\CoursVideo;

session_start(); // Start the session to access the logged-in user
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $content_type = $_POST['content_type'];
        $media_path = '';
        $is_approved = 0; // Set the default value for is_approved

        // Get the logged-in user's ID
        if (isset($_SESSION['user_id'])) {
            $id_author = $_SESSION['user_id'];
        } else {
            throw new Exception("User is not logged in.");
        }

        $course_id;
        // Handle file upload or text input based on content type
        if ($content_type === 'video' || $content_type === 'file' || $content_type === 'image') {
            if (isset($_FILES['content']) && $_FILES['content']['error'] == 0) {
                $media_path = '../../public/uploads/' . basename($_FILES['content']['name']);
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
                $media_path = $_POST['content'];

                $course = new CoursTexte($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type);
                $course_id = $course->create();
            } else {
                throw new Exception("Text content cannot be empty.");
            }
        } else {
            throw new Exception("Invalid content type.");
        }

        $selectedTags = json_decode($_POST['selectedTags'], true);

        // Insert course data with id_author
        // $stmt = $db->prepare("INSERT INTO Courses (title, content, category, description, price, media_path, content_type, is_approved, id_author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // $stmt->execute([$title, $media_path, $category, $description, $price, $media_path, $content_type, $is_approved, $id_author]);
        // $course_id = $db->lastInsertId();
        error_log("Course inserted successfully with ID $course_id");

        // Insert tags and link them to the course
        foreach ($selectedTags as $tag) {
            if (empty($tag['name'])) {
                throw new Exception("Tag name is empty.");
            }
            $stmt = $db->prepare("INSERT INTO Tags (name) VALUES (?) ON DUPLICATE KEY UPDATE id_tags=LAST_INSERT_ID(id_tags)");
            $stmt->execute([$tag['name']]);
            $tag_id = $db->lastInsertId();
            error_log("Tag inserted/updated successfully with ID $tag_id");

            $stmt = $db->prepare("INSERT INTO Course_Tags (id_course, id_tags) VALUES (?, ?)");
            if (!$stmt->execute([$course_id, $tag_id])) {
                throw new Exception("Error linking tag ID $tag_id with course ID $course_id.");
            } else {
                error_log("Tag ID $tag_id linked with course ID $course_id successfully.");
            }
        }

        echo json_encode(['success' => 'Course and tags inserted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error inserting course or tags: ' . $e->getMessage()]);
    }
    exit;
}
