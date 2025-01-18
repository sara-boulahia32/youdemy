<?php
require_once __DIR__ . '/../../src/config/autoloader.php';
use Models\Course;
use Models\User; // Ensure this model is included
use Models\category; // Ensure this model is included

session_start(); // Ensure session is started to check user role

$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = Course::getById($courseId);
if (!$course) {
    die('Course not found.');
}
$tags = $course->getTags();
$category = Category::getById($course->getCategory());
$author = User::getById($course->getauthor()); // Fetch the author details
echo '<pre>'; var_dump($course); echo '</pre>';
echo '<pre>'; var_dump($authorId); echo '</pre>';echo '<pre>'; var_dump($author); echo '</pre>';
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
        </div>


        <!-- Course Card - Right Side -->
        <div class="lg:col-span-1">
          <div class="bg-slate-50 rounded-xl p-6">
            <img 
              src="<?php echo htmlspecialchars($course->getMediaPath()); ?>" 
              alt="Course Preview" 
              class="w-full rounded-lg mb-6"
            />
            
            <div class="text-3xl font-bold text-slate-800 mb-6">
              $<?php echo htmlspecialchars($course->getPrice()); ?>
            </div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
              <form method="POST" action="enroll.php" onsubmit="return enrollCourse(<?php echo $courseId; ?>);">
                <input type="hidden" name="course_id" value="<?php echo $courseId; ?>">
                <button type="submit" class="w-full bg-violet-600 text-white py-3 rounded-lg hover:bg-violet-700 transition-all mb-4">
                  Enroll Now
                </button>
              </form>
            <?php endif; ?>

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
</body>
</html>
