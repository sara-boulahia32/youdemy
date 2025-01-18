<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../src/config/autoloader.php');
use Database\Database;
use Models\CourseText;
use Models\CourseVideo;

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

        // Handle file upload or text input based on content type
        if ($content_type === 'video' || $content_type === 'file' || $content_type === 'image') {
            if (isset($_FILES['content']) && $_FILES['content']['error'] == 0) {
                $media_path = '../../public/uploads/' . basename($_FILES['content']['name']);
                if (!move_uploaded_file($_FILES['content']['tmp_name'], $media_path)) {
                    throw new Exception("Failed to upload file.");
                }
            } else {
                throw new Exception("No file uploaded or upload error.");
            }
        } else if ($content_type === 'text') {
            $media_path = $_POST['content'];
        }

        $selectedTags = json_decode($_POST['selectedTags'], true);

        // Insert course data based on content type
        $stmt = $db->prepare("INSERT INTO Courses (title, content, category, description, price, media_path, content_type, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $media_path, $category, $description, $price, $media_path, $content_type, $is_approved]);
        $course_id = $db->lastInsertId();
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
