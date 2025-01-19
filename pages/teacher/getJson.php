<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;
use Database\Database;

if (isset($_GET['id'])) {
    $course_id = (int) $_GET['id'];

    // Fetch the course from the database
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM Courses WHERE id_course = :id");
    $stmt->bindValue(':id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        echo json_encode($course);
    } else {
        echo json_encode(['error' => 'Course not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
