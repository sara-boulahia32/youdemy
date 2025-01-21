<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;
use Models\Category;
use Database\Database;

session_start(); // Start session to access logged-in user
$showSuccessAlert = false; 
$showErrorAlert = false;
// Handle the enrollment logic
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

        // Show success alert using SweetAlert
        $showSuccessAlert = true; 

    } catch (Exception $e) {
        // Show error alert using SweetAlert
        $showErrorAlert = true; 

    }
}

$coursesPerPage = isset($_GET['coursesPerPage']) ? (int) $_GET['coursesPerPage'] : 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $coursesPerPage;
$keyword = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

if ($categoryFilter) {
    $courses = Course::getByCategory($categoryFilter, $coursesPerPage, $offset);
    $totalCourses = count($courses); // Assume the category filter result is already paginated
    $totalPages = ceil($totalCourses / $coursesPerPage);
} else if ($keyword) {
    $courses = Course::search($keyword, '', $coursesPerPage, $offset);
    $totalCourses = count($courses); // Assume the search result is already paginated
    $totalPages = ceil($totalCourses / $coursesPerPage);
} else {
    $courses = Course::getPaginated($coursesPerPage, $offset);
    $totalCourses = Course::getTotalCourses();
    $totalPages = ceil($totalCourses / $coursesPerPage);
}

$categories = Category::getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Courses Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="font-inter bg-slate-50">
  
  <!-- Navigation -->
  <nav class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="text-2xl font-bold text-violet-600">Youdemy</div>
        <div class="hidden md:flex space-x-8">
          <a href="#" class="text-slate-600 hover:text-violet-600 transition-colors">Cours</a>
          <a href="#" class="text-slate-600 hover:text-violet-600 transition-colors">Enseignants</a>
          <a href="#" class="text-slate-600 hover:text-violet-600 transition-colors">Blog</a>
        </div>
        <div class="flex items-center space-x-4">
          <button class="px-4 py-2 text-slate-600 hover:text-violet-600 transition-colors">
            Connexion
          </button>
          <button class="px-6 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-all">
            S'inscrire
          </button>
        </div>
      </div>
    </div>
  </nav>

   
  <!-- Search Bar -->
  <div class="max-w-7xl mx-auto px-4 mb-12">
    <div class="relative max-w-xl mx-auto">
      <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-3.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4a6 6 0 016 6v3a6 6 0 11-12 0V10a6 6 0 016-6z" />
      </svg>
      <form method="GET" action="catalogue.php" class="w-full">
        <input
          type="text"
          name="search"
          value="<?php echo htmlspecialchars($keyword); ?>"
          placeholder="Search for courses..."
          class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-violet-600 focus:border-transparent outline-none"
        />
      </form>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
      <label for="coursesPerPage" class="block text-sm font-medium text-gray-700">Courses per page:</label>
      <select id="coursesPerPage" class="mt-1 block w-1/4 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm rounded-md" onchange="location = this.value;">
        <option value="?coursesPerPage=3&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 3 ? 'selected' : ''; ?>>3</option>
        <option value="?coursesPerPage=6&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 6 ? 'selected' : ''; ?>>6</option>
        <option value="?coursesPerPage=9&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 9 ? 'selected' : ''; ?>>9</option>
      </select>
    </div>

   <!-- Category Filter --> <div class="mb-4"> <label for="categoryFilter" class="block text-sm font-medium text-gray-700">Filter by category:</label> <div class="flex flex-wrap gap-2"> <a href="catalogue.php?category=&search=<?php echo htmlspecialchars($keyword); ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm"> All </a> <?php foreach ($categories as $category): ?> <a href="catalogue.php?category=<?php echo $category->getId(); ?>&search=<?php echo htmlspecialchars($keyword); ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm <?php echo $categoryFilter == $category->getId() ? 'bg-violet-600 text-white' : ''; ?>"> <?php echo htmlspecialchars($category->getName()); ?> </a> <?php endforeach; ?> </div> </div>
    <div id="coursesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($courses as $course): ?>
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
              12,453 students
            </div>
          </div>

          <!-- Instructor -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <img src="../../public/assets/img/Free Vector _ Hand drawn bookstore landing page template.jpeg" alt="Instructor" class="w-8 h-8 rounded-full">
              <span class="text-sm text-slate-600">Sarah Johnson</span>
            </div>
              <!-- Buttons -->
  <div class="flex justify-between">
    <a href="course_details.php?id=<?php echo $course->getId(); ?>" class="bg-transparent border-2 border-violet-600 text-violet-600 px-4 py-2 rounded-md hover:bg-violet-600 hover:text-white transition-all">
      See Details
    </a>
    <form method="POST" action="">
      <input type="hidden" name="course_id" value="<?php echo $course->getId(); ?>">
      <button type="submit" class="bg-violet-600 text-white px-4 py-2 rounded-md hover:bg-violet-700 transition-all">
        Enroll Now
      </button>
    </form>
  </div>

</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="mt-8"> 
    <?php if ($totalPages > 1): ?> <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination"> <?php for ($i = 1; $i <= $totalPages; $i++): ?> <a href="?page=<?php echo $i; ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"> <?php echo $i; ?> </a> <?php endfor; ?> </nav> <?php endif; ?> </div>
</div>

  <!-- Teacher's Add Course Button -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-end mb-4">
      <button
        id="add-course-btn"
        onclick="togglePopup()"
        class="hidden md:block px-6 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-all">
        Ajouter un Cours
      </button>
    </div>
  </div>

 

  <!-- Add Course Popup -->
  <div id="add-course-popup" class="hidden fixed inset-0 bg-slate-800 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-8 w-full sm:w-96">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Ajout de nouveaux cours</h2>
        <button onclick="togglePopup()" class="text-slate-600 hover:text-violet-600">&times;</button>
      </div>
      <form id="addCourseForm" method="POST" enctype="multipart/form-data">
    <div class="mb-4">
        <label for="title" class="block text-slate-800">Titre</label>
        <input type="text" id="title" name="title" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required>
    </div>
    <div class="mb-4">
        <label for="description" class="block text-slate-800">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required></textarea>
    </div>
    <div class="mb-4">
        <label for="category" class="block text-slate-800">Catégorie</label>
        <select id="category" name="category" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required> <?php if ($categories) { foreach ($categories as $category) { echo '<option value="' . htmlspecialchars($category->getId()) . '">' . htmlspecialchars($category->getName()) . '</option>'; } } ?> </select>
        
    </div>
    <div class="mb-4">
        <label for="content_type" class="block text-slate-800">Type de Contenu</label>
        <select id="content_type" name="content_type" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required>
            <option value="text">Texte</option>
            <option value="video">Vidéo</option>
            <option value="file">Fichier</option>
            <option value="image">Image</option>
        </select>
    </div>
    <div class="mb-4" id="content-upload">
        <!-- Content upload field will be dynamically updated based on content type -->
    </div>
    <div class="mb-4">
        <label for="price" class="block text-slate-800">Prix</label>
        <input type="number" id="price" name="price" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required>
    </div>
    <div class="mb-4">
        <label for="tags" class="block text-slate-800">Tags</label>
        <input type="text" id="tags" name="tags" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600">
        <div id="tagsList" class="hidden mt-2 bg-white shadow-lg rounded-lg p-2 border"></div>
        <div id="selectedTags" class="mt-2"></div>
    </div>
    <div class="mb-4">
        <button type="submit" class="w-full bg-violet-600 text-white py-2 rounded-lg hover:bg-violet-700 transition-all">Ajouter</button>
    </div>
</form>

    </div>
  </div>
  <script>
    function togglePopup() {
      const popup = document.getElementById('add-course-popup');
      popup.classList.toggle('hidden');
    }
  document.addEventListener('DOMContentLoaded', function() {
    const isTeacher = <?php echo isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher' ? 'true' : 'false'; ?>;
    if (isTeacher) {
        document.getElementById('add-course-btn').classList.remove('hidden');
    }

    const courseTagsInput = document.getElementById('tags');
    const tagsList = document.getElementById('tagsList');
    const selectedTagsContainer = document.getElementById('selectedTags');
    let selectedTags = [];

    // Function to toggle the visibility of the popup
    

    function fetchTags(query) {
      fetch(`getAllJson.php?query=${query}`)
          .then(response => response.json())
          .then(tags => {
              tagsList.innerHTML = '';
              tags.forEach(tag => {
                  const tagItem = document.createElement('div');
                  tagItem.classList.add('px-4', 'py-2', 'cursor-pointer', 'hover:bg-gray-100');
                  tagItem.textContent = tag.name;
                  tagsList.appendChild(tagItem);
                  tagItem.addEventListener('click', function () {
                      addTag(tag);
                  });
              });
              tagsList.classList.remove('hidden');
          });
    }

    function addTag(tag) {
      if (!selectedTags.some(t => t.id === tag.id || t.name.toLowerCase() === tag.name.toLowerCase())) {
          selectedTags.push(tag);
          updateSelectedTags();
      }
      courseTagsInput.value = ''; 
      tagsList.classList.add('hidden'); 
    }

    function removeTag(tag) {
      selectedTags = selectedTags.filter(t => t.id !== tag.id);
      updateSelectedTags();
    }

    function updateSelectedTags() {
      selectedTagsContainer.innerHTML = '';
      selectedTags.forEach(tag => {
          const tagItem = document.createElement('div');
          tagItem.classList.add('px-4', 'py-2', 'bg-gray-200', 'rounded', 'mr-2', 'mb-2', 'inline-block');
          tagItem.textContent = tag.name;
          const removeButton = document.createElement('button');
          removeButton.classList.add('ml-2', 'text-red-500');
          removeButton.textContent = 'x';
          removeButton.addEventListener('click', function () {
              removeTag(tag);
          });
          tagItem.appendChild(removeButton);
          selectedTagsContainer.appendChild(tagItem);
      });
    }

    courseTagsInput.addEventListener('input', function () {
      const query = courseTagsInput.value;
      if (query.length >= 2) {
          fetchTags(query);
      } else {
          tagsList.classList.add('hidden');
      }
    });

    courseTagsInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && courseTagsInput.value.trim() !== '') {
          e.preventDefault(); 
          const newTag = { id: 'new' + selectedTags.length, name: courseTagsInput.value.trim() };
          addTag(newTag);
      }
    });

    document.getElementById('addCourseForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('selectedTags', JSON.stringify(selectedTags));
    fetch('../../src/Models/add.php', {
        method: 'POST',
        body: formData
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
        Swal.fire('Success', 'Course added successfully!', 'success').then(() => {
            window.location.href = 'catalogue.php';
        });
    })
    .catch(error => {
        Swal.fire('Error', error.message, 'error');
    });
});

  });
</script>
<script>
document.getElementById('content_type').addEventListener('change', function() {
    const contentType = this.value;
    const contentUpload = document.getElementById('content-upload');
    contentUpload.innerHTML = '';

    if (contentType === 'video') {
        contentUpload.innerHTML = '<label for="content" class="block text-slate-800">Vidéo</label><input type="file" id="content" name="content" accept="video/*" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600">';
    } else if (contentType === 'file') {
        contentUpload.innerHTML = '<label for="content" class="block text-slate-800">Fichier</label><input type="file" id="content" name="content" accept=".pdf,.doc,.docx" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600">';
    } else if (contentType === 'image') {
        contentUpload.innerHTML = '<label for="content" class="block text-slate-800">Image</label><input type="file" id="content" name="content" accept="image/*" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600">';
    } else if (contentType === 'text') {
        contentUpload.innerHTML = '<label for="content" class="block text-slate-800">Texte</label><textarea id="content" name="content" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600"></textarea>';
    }
});
</script>
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
