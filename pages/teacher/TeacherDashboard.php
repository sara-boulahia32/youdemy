<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;


session_start(); // Ensure session is started to check user role

// Ensure the user is a teacher
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
//     header("Location: login.php");
//     exit();
// }

$teacher_id = $_SESSION['user_id'];

// Handle delete course request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course_id'])) {
    $course_id = $_POST['delete_course_id'];
    Course::deleteById($course_id);
    // Success alert will be triggered by JavaScript
}

// Handle add/edit course request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_course'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $media_path = $_POST['media_path'];
    $content_type = $_POST['content_type'];
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;

    if ($course_id) {
        // Update course
        Course::update($course_id, $title, $description, $category, $price, $status, $media_path, $is_approved, $content_type);
    } 
    // Success alert will be triggered by JavaScript
}

// Fetch the teacher's courses
$coursesPerPage = isset($_GET['coursesPerPage']) ? (int) $_GET['coursesPerPage'] : 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $coursesPerPage;

$courses = Course::getByAuthor($teacher_id);
$total_courses = count($courses);
$total_pages = ceil($total_courses / $coursesPerPage);

// Paginate courses
$paginated_courses = array_slice($courses, $offset, $coursesPerPage);

// Fetch statistics
$total_students = 0; // Calculate the total number of students enrolled in the teacher's courses
foreach ($courses as $course) {
    $total_students += count($course->getEnrollments());
}
$total_courses = count($courses);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    <div id="coursesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
  <?php if (!empty($paginated_courses)): ?>
    <?php foreach ($paginated_courses as $course): ?>
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
        <!-- Course Image -->
        <?php if ($course->getMediaPath()): ?>
          <div class="relative">
            <img src="<?php echo htmlspecialchars($course->getMediaPath() ?? ''); ?>" alt="<?php echo htmlspecialchars($course->getTitle() ?? ''); ?>" class="w-full h-48 object-cover rounded-t-2xl">
            <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-violet-600">
              $<?php echo htmlspecialchars($course->getPrice() ?? ''); ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Course Content -->
        <div class="p-6">
          <!-- Tags -->
          <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($course->getTags() as $tag): ?>
              <span class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm">
                <?php echo htmlspecialchars($tag->getName() ?? ''); ?>
              </span>
            <?php endforeach; ?>
          </div>

          <!-- Title & Description -->
          <h3 class="text-xl font-semibold text-slate-800 mb-2"><?php echo htmlspecialchars($course->getTitle() ?? ''); ?></h3>
          <p class="text-slate-600 text-sm mb-4"><?php echo htmlspecialchars(substr($course->getDescription() ?? '', 0, 100)); ?>...</p>

          <!-- Stats -->
          <div class="flex items-center gap-4 text-sm text-slate-600 mb-4">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.92 1.653-.92 1.953 0l2.179 6.699a1 1 0 00.95.691h7.044c.969 0 1.371 1.24.588 1.81l-5.693 4.147a1 1 0 00-.36 1.118l2.052 6.672c.296.953-.755 1.735-1.535 1.155l-5.738-4.285a1 1 0 00-1.174 0l-5.738 4.285c-.78.58-1.83-.202-1.535-1.155l2.052-6.672a1 1 0 00-.36-1.118l-5.693-4.147c-.783-.57-.38-1.81.588-1.81h7.044a1 1 0 00.95-.691l2.179-6.699z"/>
              </svg>
              4.8
            </div>
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 12h.01M10 16h.01M10 20h.01M14 12h.01M14 16h.01M14 20h.01"/>
              </svg>
              <?php echo count($course->getEnrollments()); ?> students
            </div>
          </div>

          <!-- Instructor -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <img src="../../public/assets/img/Free Vector _ Hand drawn bookstore landing page template.jpeg" alt="Instructor" class="w-8 h-8 rounded-full">
              <span class="text-sm text-slate-600">Sarah Johnson</span>
            </div>
            <!-- Buttons -->
            <div class="flex space-x-2">
              <button onclick="editCourse(<?php echo $course->getId(); ?>)" class="bg-transparent border-2 border-violet-600 text-violet-600 px-4 py-2 rounded-md hover:bg-violet-600 hover:text-white transition-all">
                Edit
              </button>
              <button onclick="confirmDeleteCourse(<?php echo $course->getId(); ?>)" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-all">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-slate-600">No courses found.</p>
  <?php endif; ?>
</div>

<!-- Pagination -->
<div class="mt-8">
  <?php if ($total_pages > 1): ?>
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
          <span class="sr-only">Previous</span>
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M12.707 14.707a1 1 0 01-1.414 0L7 10.414a1 1 0 010-1.414l4.293-4.293a1 1 0 011.414 1.414L9.414 10l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
          </svg>
        </a>
      <?php endif; ?>
      
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border <?php echo $i == $page ? 'bg-indigo-50 border-indigo-500 text-indigo-600' : 'border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50'; ?>">
          <?php echo $i; ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
          <span class="sr-only">Next</span>
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 001.414 0L13 10.414a1 1 0 000-1.414l-4.293-4.293a1 1 0 10-1.414 1.414L10.586 10 7.293 13.293a1 1 0 000 1.414z" clip-rule="evenodd"/>
          </svg>
        </a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</div>

<!-- Edit Course Form Popup -->
<div id="courseFormPopup" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
  <form id="courseForm" method="POST" action="" class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
    <input type="hidden" name="course_id" id="course_id">
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Edit Course</h2>
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
    <input type="text" name="content_type" id="content_type" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
  </div>
  <div class="mb-4">
    <label for="is_approved" class="block text-sm font-medium text-gray-700">Approved</label>
    <input type="checkbox" name="is_approved" id="is_approved" class="mt-1 p-2 block">
  </div>
  <div class="flex justify-end">
    <button type="button" onclick="closeCourseForm()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-all mr-2">Cancel</button>
    <button type="submit" name="save_course" class="bg-violet-600 text-white px-4 py-2 rounded-md hover:bg-violet-700 transition-all">Save</button>
  </div>
  </form>
</div>
<script>
function editCourse(courseId) {
  // Fetch course data and populate form
  fetch(`getJson.php?id=${courseId}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        Swal.fire('Error!', data.error, 'error');
      } else {
        document.getElementById('course_id').value = data.id_course;
        document.getElementById('title').value = data.title;
        document.getElementById('description').value = data.description;
        document.getElementById('category').value = data.category;
        document.getElementById('price').value = data.price;
        document.getElementById('status').value = data.status;
        document.getElementById('media_path').value = data.media_path;
        document.getElementById('content_type').value = data.content_type;
        document.getElementById('is_approved').checked = data.is_approved;

        // Show the form popup
        document.getElementById('courseFormPopup').classList.remove('hidden');
      }
    }).catch(error => {
      Swal.fire('Error!', 'Failed to fetch course data.', 'error');
      console.error('Error fetching course data:', error);
    });
}

function closeCourseForm() {
  document.getElementById('courseFormPopup').classList.add('hidden');
}

function saveCourse() {
  // Create a FormData object
  const formData = new FormData(document.getElementById('courseForm'));

  fetch('', {
    method: 'POST',
    body: formData
  }).then(response => {
    if (response.ok) {
      // Show success alert
      Swal.fire('Saved!', 'Your course has been saved.', 'success');
      // Close the form popup and reload the page to reflect changes
      closeCourseForm();
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      // Show error alert
      Swal.fire('Error!', 'There was an error saving your course.', 'error');
    }
  }).catch(error => {
    Swal.fire('Error!', 'There was an error saving your course.', 'error');
    console.error('Error saving course:', error);
  });
}

function confirmDeleteCourse(courseId) {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      // Create a FormData object for delete
      const formData = new FormData();
      formData.append('delete_course_id', courseId);

      fetch('', {
        method: 'POST',
        body: formData
      }).then(response => {
        if (response.ok) {
          // Show success alert
          Swal.fire('Deleted!', 'Your course has been deleted.', 'success');
          // Reload the page to reflect changes
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          // Show error alert
          Swal.fire('Error!', 'There was an error deleting your course.', 'error');
        }
      }).catch(error => {
        Swal.fire('Error!', 'There was an error deleting your course.', 'error');
        console.error('Error deleting course:', error);
      });
    }
  });
}
</script>

</div>

</body>
</html>