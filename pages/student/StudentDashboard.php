<?php
require_once __DIR__ . '/../../src/config/autoloader.php';
use Models\Course;
use Models\Category;

session_start();
// Ensure the user is a student
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
 var_dump($_SESSION['role']);
$user_id = $_SESSION['user_id']; // Assume user is logged in
$keyword = isset($_GET['search']) ? $_GET['search'] : '';
$coursesPerPage = isset($_GET['coursesPerPage']) ? intval($_GET['coursesPerPage']) : 3;
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $coursesPerPage;

if ($categoryFilter) {
    $courses = Course::getByCategory($categoryFilter, $coursesPerPage, $offset);
    $totalCourses = count($courses); // Assume the category filter result is already paginated
    $totalPages = ceil($totalCourses / $coursesPerPage);
} else if ($keyword) {
    $courses = Course::search($keyword, '', $coursesPerPage, $offset);
    $courses = Course::getByStudent($user_id);

    $totalCourses = count($courses); // Assume the search result is already paginated
    $totalPages = ceil($totalCourses / $coursesPerPage);
} else {
    // $courses = Course::getPaginated($coursesPerPage, $offset);
    $courses = Course::getByStudent($user_id);
    
    $totalCourses = Course::getTotalCourses();
    $totalPages = ceil($totalCourses / $coursesPerPage);
    echo var_dump($courses);
}

$categories = Category::getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - My Courses</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-8">
        
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
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Student Dashboard</h1>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Add New Course
            </button>
        </div>

        <!-- Search Bar -->
        <div class="max-w-7xl mx-auto px-4 mb-12">
            <div class="relative max-w-xl mx-auto">
                <form method="GET" action="StudentDashboard.php" class="w-full">
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

        <!-- Filters -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-4">
                <label for="coursesPerPage" class="block text-sm font-medium text-gray-700">Courses per page:</label>
                <select id="coursesPerPage" class="mt-1 block w-1/4 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm rounded-md" onchange="location = this.value;">
                    <option value="?coursesPerPage=3&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 3 ? 'selected' : ''; ?>>3</option>
                    <option value="?coursesPerPage=6&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 6 ? 'selected' : ''; ?>>6</option>
                    <option value="?coursesPerPage=9&search=<?php echo htmlspecialchars($keyword); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" <?php echo $coursesPerPage == 9 ? 'selected' : ''; ?>>9</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="mb-4">
                <label for="categoryFilter" class="block text-sm font-medium text-gray-700">Filter by category:</label>
                <div class="flex flex-wrap gap-2">
                    <a href="StudentDashboard.php?category=&search=<?php echo htmlspecialchars($keyword); ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm"> All </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="StudentDashboard.php?category=<?php echo $category->getId(); ?>&search=<?php echo htmlspecialchars($keyword); ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm <?php echo $categoryFilter == $category->getId() ? 'bg-violet-600 text-white' : ''; ?>">
                            <?php echo htmlspecialchars($category->getName()); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Courses Grid -->
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
                                    4.8
                                </div>
                                <div class="flex items-center">
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

            <!-- Pagination -->
            <div class="mt-8">
                <?php if ($totalPages > 1): ?>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&coursesPerPage=<?php echo $coursesPerPage; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
