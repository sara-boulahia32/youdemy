<?php
require_once __DIR__ . '/../../src/config/autoloader.php';
use Models\Course;
use Models\User; // Ensure this model is included
use Database\Database;
use models\category;

session_start(); // Ensure session is started to check user role

// Handle the enrollment logic


$showSuccessAlert = false; 
$showErrorAlert = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
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

        $showSuccessAlert = true; 

    } catch (Exception $e) {
        // Show error alert using SweetAlert
        $showErrorAlert = true; 

    }
}

$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = Course::getById($courseId);
if (!$course) {
    die('Course not found.');
}
$tags = $course->getTags();
$category = Category::getById($course->getCategory());
$author = User::getById($course->getauthor()); // Fetch the author details
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-slate-50">
    

  <main class="max-w-7xl mx-auto px-4 py-12">
    <!-- Course Header -->
    <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content - Left Side -->
        <div class="lg:col-span-2">
          <div class="flex gap-3 mb-6">
            <?php foreach ($course->getTags() as $tag):  ?>
                <span class="px-4 py-1 bg-violet-50 text-violet-600 rounded-full text-sm"><?php echo htmlspecialchars($tag->getName()); ?></span>
            <?php endforeach; ?>
          </div>

          <h1 class="text-3xl font-bold text-slate-800 mb-4">
            <?php echo htmlspecialchars($course->getTitle()); ?>
          </h1>

          <p class="text-slate-600 mb-6">
            <?php echo htmlspecialchars($course->getDescription()); ?>
          </p>

          <div class="flex items-center gap-6 mb-8">
            <div class="flex items-center gap-2">
              <div class="flex">
                <!-- Static Star Icons -->
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.049 2.927a1 1 0 011.902 0l1.26 3.882a1 1 0 00.95.69h4.1a1 1 0 01.592 1.806l-3.3 2.39a1 1 0 00-.364 1.118l1.259 3.881a1 1 0 01-1.541 1.118l-3.3-2.39a1 1 0 00-1.176 0l-3.3 2.39a1 1 0 01-1.54-1.118l1.259-3.881a1 1 0 00-.363-1.118L2.098 9.305a1 1 0 01.592-1.806h4.1a1 1 0 00.95-.69l1.26-3.882z"/>
                </svg>
                <!-- Repeat for other stars -->
              </div>
              <span class="text-slate-600">4.8 (2,450 ratings)</span>
            </div>
            <div class="text-slate-600">
              15,234 students enrolled
            </div>
          </div>

          <!-- Author Info -->
          <div class="flex items-center gap-4 mb-8">
            <img src="https://via.placeholder.com/48" alt="Author" class="w-12 h-12 rounded-full">
            <div>
              <h3 class="font-medium text-slate-800"><?php echo htmlspecialchars($author->getName()); ?></h3>
              
              <p class="text-sm text-slate-600"><?php echo htmlspecialchars($author->getEmail()); ?></p>
            </div>
          </div>
          <!-- Category Info --> <div class="flex items-center gap-4 mb-8"> <span class="text-slate-600 text-sm">Category: <?php echo htmlspecialchars($category->getName()); ?></span> </div> </div>
        </div>


<!-- Course Card - Right Side -->
<div class="lg:col-span-1">
  <div class="bg-slate-50 rounded-xl p-6">
    <?php if ($course->getContentType() === 'image'): ?>
      <img 
        src="<?php echo htmlspecialchars($course->getMediaPath()); ?>" 
        alt="Course Preview" 
        class="w-full rounded-lg mb-6"
      />
    <?php elseif ($course->getContentType() === 'video'): ?>
      <video controls class="w-full rounded-lg mb-6">
        <source src="<?php echo htmlspecialchars($course->getMediaPath()); ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    <?php elseif ($course->getContentType() === 'file' || $course->getContentType() === 'document'): ?>
      <a href="<?php echo htmlspecialchars($course->getMediaPath()); ?>" target="_blank" class="block w-full bg-violet-100 text-violet-800 text-center py-3 rounded-lg mb-6">Download Content</a>
    <?php else: ?>
      <p class="w-full bg-violet-100 text-violet-800 text-center py-3 rounded-lg mb-6">
        <?php echo htmlspecialchars($course->getMediaPath()); ?>
      </p>
    <?php endif; ?>

    <div class="text-3xl font-bold text-slate-800 mb-6">
      $<?php echo htmlspecialchars($course->getPrice()); ?>
    </div>

    <form method="POST" action="">
      <input type="hidden" name="course_id" value="<?php echo $courseId; ?>">
      <button type="submit" class="w-full bg-violet-600 text-white py-3 rounded-lg hover:bg-violet-700 transition-all mb-4">
        Enroll Now
      </button>
    </form>
  </div>
</div>

            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-slate-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 12H9v-2h2v2zm0-4H9V5h2v5z"/>
                </svg>
                <span class="text-slate-600">42 hours of content</span>
              </div>
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-slate-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M12 2a2 2 0 00-2 2v10.5L7.293 12.207a1 1 0 00-1.414 0l-3.5 3.5a1 1 0 101.414 1.414L7.5 14.5l3.5 3.5V16a2 2 0 002-2V4a2 2 0 00-2-2z"/>
                </svg>
                <span class="text-slate-600">24 modules</span>
              </div>
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-slate-400" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M4 4a1 1 0 00-.707 1.707l6.586 6.586a1 1 0 001.414 0l6.586-6.586A1 1 0 0016 4H4z"/>
                </svg>
                <span class="text-slate-600">Certificate of completion</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Course Content -->
    <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
      <h2 class="text-2xl font-bold text-slate-800 mb-6">Course Content</h2>
      <div class="space-y-4">
        <div class="border border-slate-200 rounded-lg p-4 cursor-pointer hover:bg-slate-50">
          Module 1: Introduction to Web Development
        </div>
        <div class="border border-slate-200 rounded-lg p-4 cursor-pointer hover:bg-slate-50">
          Module 2: Introduction to Java Script
        </div>
        <div class="border border-slate-200 rounded-lg p-4 cursor-pointer hover:bg-slate-50">
          Module 3: Introduction to PHP
        </div>
        <!-- Repeat for other modules -->
      </div>
    </div>
  </main>

  <script>
    function enrollCourse(courseId) {
      Swal.fire({
        title: 'Success!',
        text: 'You have been successfully enrolled in the course.',
        icon: 'success',
        confirmButtonText: 'OK'
      });
      return true; // Submit the form
    }
  </script>
 

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if ($showSuccessAlert): ?>
  Swal.fire({
    title: 'Success!',
    text: 'You have been successfully enrolled in the course.',
    icon: 'success',
    confirmButtonText: 'OK'
  });
<?php elseif ($showErrorAlert): ?>
  Swal.fire({
    title: 'Error!',
    text: 'There was an error enrolling in the course.',
    icon: 'error',
    confirmButtonText: 'OK'
  });
<?php endif; ?>
</script>



</body>
</html>





<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body class="min-h-screen bg-slate-50 p-8">
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-slate-800">Teacher Dashboard</h1>
      <p class="text-slate-600 mt-2">Manage your courses and track performance</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Students -->
      <div class="bg-white rounded-lg shadow-sm p-6 flex items-center">
        <div class="p-2 bg-violet-100 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-4-4H6a4 4 0 00-4 4v1h5M12 11a4 4 0 100-8 4 4 0 000 8z"/>
          </svg>
        </div>
        <div class="ml-4">
          <p class="text-sm text-slate-600">Total Students</p>
          <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_students; ?></h3>
        </div>
      </div>

      <!-- Total Courses -->
      <div class="bg-white rounded-lg shadow-sm p-6 flex items-center">
        <div class="p-2 bg-blue-100 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M4 8h16M4 16h16M4 12h16m-7-4v8"/>
          </svg>
        </div>
        <div class="ml-4">
          <p class="text-sm text-slate-600">Total Courses</p>
          <h3 class="text-2xl font-bold text-slate-800"><?php echo $total_courses; ?></h3>
        </div>
      </div>
    </div>

    <!-- Add/Edit Course Form -->
    <div id="courseFormContainer" class="hidden mb-8">
      <form id="courseForm" method="POST" action="">
        <input type="hidden" name="course_id" id="course_id">
        <div class="bg-white rounded-lg shadow-sm p-6">
          <h2 class="text-2xl font-bold text-slate-800 mb-4">Course Form</h2>
          <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" id="title" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
          </div>
          <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <input type="text" name="category" id="category" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input type="text" name="price" id="price" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <input type="text" name="status" id="status" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div class="mb-4">
            <label for="media_path" class="block text-sm font-medium text-gray-700">Media Path</label>
            <input type="text" name="media_path" id="media_path" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
          <div class="mb-4">
            <label for="content_type" class="block text-sm font-medium text-gray-700">Content Type</label>
            [_{{{CITATION{{{_1{](https://github.com/nateq13f/Python_Projects/tree/89b015a7feab8938a9ca13b0a4a30707c57ffcf5/AWS_RottenPotatoes_website%2Fhtml%2FPlaystation_games%2FBatmanArkCity.php)[_{{{CITATION{{{_2{](https://github.com/victoryndukwu/Dashboard/tree/d676d6ce4ff030d87b0b8aada6dadbf5dfef567c/pages%2FDashboard.js)<