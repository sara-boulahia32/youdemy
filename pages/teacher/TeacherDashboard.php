<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;
use Database\database;


session_start(); // Ensure session is started to check user role

// Ensure the user is a teacher
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
//     header("Location: login.php");
//     exit();
// }

$teacher_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course_id'])) {
    $course_id = $_POST['delete_course_id'];
    $result = Course::deleteById($course_id);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete course']);
    }
    exit;
}
    // Handle delete course request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_course'])) {
    try {
        // Debug logging
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        $course_id = $_POST['course_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';  // Make sure this matches your form field
        $price = $_POST['price'] ?? 0;
        $status = $_POST['status'] ?? '';
        $content_type = $_POST['content_type'] ?? '';
        $is_approved = isset($_POST['is_approved']) ? 1 : 0;
        
        // Handle file upload
        $media_path = null;
        if (isset($_FILES['media_path']) && $_FILES['media_path']['error'] == UPLOAD_ERR_OK) {
            // Create upload directory if it doesn't exist
            $upload_dir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name =  $_FILES['media_path']['name'];
            $uploaded_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['media_path']['tmp_name'], $uploaded_file)) {
                $media_path = '../../public/uploads/' . $file_name; // Store relative path in database
            } else {
                throw new Exception("Failed to upload file: " . error_get_last()['message']);
            }
        }
        
        // If no new file was uploaded and updating existing course, keep old media path
        if ($media_path === null && $course_id) {
            $existing_course = Course::getById($course_id);
            if ($existing_course) {
                $media_path = $existing_course->getMediaPath();
            }
        }

        // Validate required fields
        if (empty($category)) {
            throw new Exception("Category is required");
        }
        $valid_content_types = ['video', 'file', 'image', 'text'];
        $content_type = strtolower($_POST['content_type'] ?? '');
        if (!in_array($content_type, $valid_content_types)) {
            throw new Exception("Invalid content type");
        }
        
        $result = Course::update(
            $course_id,
            $title,
            $description,
            $category,
            $price,
            $status,
            $media_path,
            $is_approved,
            $content_type
        );
        
        if (!$result) {
            error_log("Course update failed for course_id: " . $course_id);
            throw new Exception("Failed to update course");
        }
        
        error_log("Course updated successfully with media_path: " . $media_path);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Course update error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
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
  <form id="courseForm" method="POST" action="" class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full" enctype="multipart/form-data">
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
    <select name="category" id="category" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
        <?php 
        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT id_category, name FROM Categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['id_category']); ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
    <div class="mb-4">
      <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
      <input type="text" name="price" id="price" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
    <div class="mb-4">
      <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
      <select name="status" id="status" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
    </div>
    <div class="mb-4">
      <label for="media_path" class="block text-sm font-medium text-gray-700">Media Path</label>
      <input type="file" name="media_path" id="media_path" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
    <div class="mb-4">
      <label for="content_type" class="block text-sm font-medium text-gray-700">Content Type</label>
      <select name="content_type" id="content_type" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
    </div>
    <div class="mb-4">
      <label for="is_approved" class="block text-sm font-medium text-gray-700">Approved</label>
      <input type="checkbox" name="is_approved" id="is_approved" class="mt-1 p-2 block">
    </div>
    <div class="flex justify-end">
      <button type="button" onclick="closeCourseForm()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-all mr-2">Cancel</button>
      <button type="button" onclick="saveCourse()" class="bg-violet-600 text-white px-4 py-2 rounded-md hover:bg-violet-700 transition-all">Save</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Fetch and populate dropdown options
  fetch('getJson.php')
    .then(response => response.json())
    .then(data => {
      populateDropdown('category', data.categories);
      populateDropdown('status', data.statuses);
      populateDropdown('content_type', data.content_types);
    }).catch(error => {
      console.error('Error fetching dropdown options:', error);
    });
});

function populateDropdown(dropdownId, options) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.innerHTML = '';
    
    if (dropdownId === 'content_type') {
        // Use exact enum values from database
        const contentTypeLabels = {
            'video': 'Video',
            'file': 'File',
            'image': 'Image',
            'text': 'Text'
        };
        options.forEach(type => {
            const opt = document.createElement('option');
            opt.value = type;  // lowercase enum value
            opt.textContent = contentTypeLabels[type];  // Capitalized label
            dropdown.appendChild(opt);
        });
    } else if (dropdownId === 'category') {
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.id_category;
            opt.textContent = option.name;
            dropdown.appendChild(opt);
        });
    } else {
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            dropdown.appendChild(opt);
        });
    }
}
// Update the existing saveCourse function
function saveCourse() {
    const formData = new FormData(document.getElementById('courseForm'));
    formData.append('save_course', '1');
    
    // Handle file upload
    const fileInput = document.getElementById('media_path');
    const existingMediaLabel = document.getElementById('existing_media_label');
    if (fileInput.files.length === 0 && existingMediaLabel) {
        // If no new file is selected but there's an existing media path, preserve it
        const existingPath = existingMediaLabel.textContent.replace('Current file: ', '');
        formData.append('existing_media_path', existingPath);
    }
    
    // Debug log
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
    }

    fetch('', {
        method: 'POST',
        body: formData,
        // Don't set Content-Type header - let the browser set it with the correct boundary
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        Swal.fire({
            title: 'Success!',
            text: 'Your course has been saved.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        closeCourseForm();
        // Reload the page after the alert is shown
        setTimeout(() => {
            location.reload();
        }, 1600);
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: error.message || 'There was a problem saving the course.',
            icon: 'error'
        });
    });
}

// Update the editCourse function to show the current media path
function editCourse(courseId) {
    fetch(`getJson.php?id=${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire('Error!', data.error, 'error');
                return;
            }
            
            // Populate form fields
            document.getElementById('course_id').value = data.id_course;
            document.getElementById('title').value = data.title;
            document.getElementById('description').value = data.description;
            document.getElementById('category').value = data.category;
            document.getElementById('price').value = data.price;
            document.getElementById('status').value = data.status;
            document.getElementById('content_type').value = data.content_type;
            document.getElementById('is_approved').checked = data.is_approved == 1;
            
            // Clear the file input but show the current file name if it exists
            const mediaInput = document.getElementById('media_path');
            mediaInput.value = ''; // Clear the file input
            
            // Add a label showing the current media file if it exists
            const existingMediaLabel = document.getElementById('existing_media_label') || document.createElement('div');
            existingMediaLabel.id = 'existing_media_label';
            if (data.media_path) {
                existingMediaLabel.textContent = `Current file: ${data.media_path.split('/').pop()}`;
                existingMediaLabel.className = 'text-sm text-gray-600 mt-1';
                mediaInput.parentNode.insertBefore(existingMediaLabel, mediaInput.nextSibling);
            } else {
                existingMediaLabel.remove();
            }

            // Show the form popup
            document.getElementById('courseFormPopup').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to fetch course data.', 'error');
        });
}
function closeCourseForm() {
  document.getElementById('courseFormPopup').classList.add('hidden');
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
      
      fetch('', {  // Empty string means current page URL
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          throw new Error(data.error);
        }
        // Show success alert
        Swal.fire('Deleted!', 'Your course has been deleted.', 'success');
        // Reload the page to reflect changes
        setTimeout(() => {
          location.reload();
        }, 1500);
      })
      .catch(error => {
        // Show error alert
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