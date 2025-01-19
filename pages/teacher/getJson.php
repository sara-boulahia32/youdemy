<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;
use Database\Database;

$db = Database::getInstance()->getConnection();

if (isset($_GET['id'])) {
    $course_id = (int) $_GET['id'];

    // Fetch the course from the database
    $stmt = $db->prepare("SELECT * FROM Courses WHERE id_course = :id");
    $stmt->bindValue(':id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        echo json_encode($course);
    } else {
        echo json_encode(['error' => 'Course not found']);
    }
}  else {
    // Fetch Categories with IDs
    $categories_stmt = $db->query("SELECT id_category, name FROM Categories");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

    $content_types = ['video', 'file', 'image', 'text'];

    echo json_encode([
        'categories' => $categories,
        'statuses' => ['Draft', 'Published'],
        'content_types' => $content_types
    ]);
}
?>
