<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../src/config/autoloader.php');
use Database\Database;
use Models\Reservation;

session_start(); // Start the session to access the logged-in user
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        $course_id = $_POST['course_id'];
        $start_date = date('Y-m-d'); // Start date is today
        $end_date = date('Y-m-d', strtotime('+1 year')); // End date is one year from today

        // Get the logged-in user's ID
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            throw new Exception("User is not logged in.");
        }

        // Insert the reservation
        $stmt = $db->prepare("INSERT INTO Reservations (id_user, id_course, startDate, endDate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $start_date, $end_date]);

        echo json_encode(['success' => 'Enrolled successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error enrolling in the course: ' . $e->getMessage()]);
    }
    exit;
}
?>
