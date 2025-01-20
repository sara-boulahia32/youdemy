<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Course;
use Models\User;
use Models\Tag;
use Models\Category;

session_start();



// Ensure the user is an admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    if (isset($_POST['new_category'])) {
      
      try {
          $name = trim($_POST['new_category']);
          if (!empty($name)) {
            // Insert the new category and get the ID of the new record
            $lastInsertId = Category::create(['name' => $name]);
    
            // Debugging: Show the inserted name and ID
            echo json_encode([
                'success' => true,
                'id' => $lastInsertId, // ID of the new category
                'name' => $name        // The name of the inserted category
            ]);
        } else {
            // If the name is empty
            echo json_encode(['success' => false, 'error' => 'Category name cannot be empty']);
        }
      } catch (Exception $e) {
          echo json_encode(['success' => false, 'error' => $e->getMessage()]);
      }
      exit();
  }
    // Handle teacher validation
    if (isset($_POST['validate_teacher_id'])) {
        try {
            $teacher_id = intval($_POST['validate_teacher_id']);
            $validate = $_POST['validate'] === '1';
            User::validateTeacher($teacher_id, $validate);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }
  //  handle user manangement
    if (isset($_POST['manage_user_id'], $_POST['action'])) {
      try {
          $user_id = intval($_POST['manage_user_id']);
          $action = $_POST['action'];
  
          // Validate action
          if (!in_array($action, ['activate', 'deactivate', 'delete'])) {
              throw new Exception('Invalid action');
          }
  
          // Call the manageUser function
          $result = User::manageUser($user_id, $action);
  
          if ($result) {
              echo json_encode(['success' => true]);
          } else {
              throw new Exception('Database operation failed');
          }
      } catch (Exception $e) {
          echo json_encode(['success' => false, 'error' => $e->getMessage()]);
      }
      exit();
  }
  
  
  

    // Handle course management
    if (isset($_POST['manage_course_id'])) {
      try {
          $course_id = intval($_POST['manage_course_id']);
          $action = $_POST['action'];
  
          if ($action === 'delete') {
              Course::deleteById($course_id);
          } elseif ($action === 'publish') {
              // Update the course status to 'Published' if it is 'Draft'
              $result = Course::updateStatus($course_id);
  
              if (!$result) {
                  throw new Exception("Failed to update course status. The course may not be in 'Draft' status.");
              }
          }
  
          echo json_encode(['success' => true]);
      } catch (Exception $e) {
          echo json_encode(['success' => false, 'error' => $e->getMessage()]);
      }
      exit();
  }
  
  

// Handle bulk tag addition
if (isset($_POST['bulk_tags'])) {
  try {
      $tags = array_map('trim', explode(',', $_POST['bulk_tags']));
      $addedCount = 0;
      
      foreach ($tags as $tagName) {
          if (!empty($tagName)) {
              if (Tag::create(['name' => $tagName])) {
                  $addedCount++;
              }
          }
      }
      
      echo json_encode([
          'success' => true,
          'message' => "$addedCount tags added successfully"
      ]);
  } catch (Exception $e) {
      echo json_encode([
          'success' => false, 
          'error' => $e->getMessage()
      ]);
  }
  exit();
}
// Handle tag deletion
if (isset($_POST['manage_tag_id'])) {
  try {
      $tag_id = intval($_POST['manage_tag_id']);
      $action = $_POST['action'];

      if ($action === 'delete') {
          if (Tag::deleteById($tag_id)) {
              echo json_encode([
                  'success' => true,
                  'message' => 'Tag deleted successfully'
              ]);
          } else {
              throw new Exception('Failed to delete tag');
          }
      } else {
          throw new Exception('Invalid action');
      }
  } catch (Exception $e) {
      echo json_encode([
          'success' => false,
          'error' => $e->getMessage()
      ]);
  }
  exit();
}

  
}

// Fetch dashboard data
$stats = [
    'total_courses' => Course::countAll(),
    'courses_by_category' => Category::getCourseDistribution(),
    'top_course' => Course::getTopCourse(),
    'top_teachers' => User::getTopTeachers(),
    'categories' => Category::getAll()
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
     <!-- Alpine.js -->
     <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
</head>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('addCategory').addEventListener('click', addNewCategory);
});

  function addNewCategory() {
    Swal.fire({
        title: 'Add New Category',
        input: 'text',
        inputPlaceholder: 'Enter category name',
        showCancelButton: true,
        confirmButtonText: 'Add',
        inputValidator: (value) => {
            if (!value) {
                return 'Category name cannot be empty!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('AdminDashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `new_category=${encodeURIComponent(result.value)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Category added successfully',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error || 'An error occurred', 'error');
                }
            });
        }
    });
}
</script>
<body class="min-h-screen bg-gray-100" x-data="{ activeTab: 'stats' }">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-sm p-4 min-h-screen">
            <div class="space-y-4">
                <button @click="activeTab = 'stats'" 
                        :class="{'bg-blue-50 text-blue-600': activeTab === 'stats'}"
                        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
                    <i data-feather="bar-chart-2"></i>
                    <span>Statistics</span>
                </button>
                <button @click="activeTab = 'teachers'"
                        :class="{'bg-blue-50 text-blue-600': activeTab === 'teachers'}"
                        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
                    <i data-feather="users"></i>
                    <span>Teacher Validation</span>
                </button>
                <button @click="activeTab = 'users'"
                        :class="{'bg-blue-50 text-blue-600': activeTab === 'users'}"
                        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
                    <i data-feather="user"></i>
                    <span>User Management</span>
                </button>
                <button @click="activeTab = 'courses'"
                        :class="{'bg-blue-50 text-blue-600': activeTab === 'courses'}"
                        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
                    <i data-feather="book"></i>
                    <span>Course Management</span>
                </button>
                <button @click="activeTab = 'tags'"
                        :class="{'bg-blue-50 text-blue-600': activeTab === 'tags'}"
                        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
                    <i data-feather="tag"></i>
                    <span>Tag Management</span>
                </button>
                <button @click="activeTab = 'categories'" 
        :class="{'bg-blue-50 text-blue-600': activeTab === 'categories'}"
        class="flex items-center space-x-2 w-full p-2 rounded-lg hover:bg-gray-50">
    <i data-feather="folder"></i>
    <span>Categories</span>
</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Statistics -->
            <div x-show="activeTab === 'stats'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Total Courses</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['total_courses']; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Top Course</h3>
                    <p class="text-xl font-medium"><?php echo htmlspecialchars($stats['top_course']['title']); ?></p>
                    <p class="text-gray-600"><?php echo $stats['top_course']['students']; ?> students</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Top Teachers</h3>
                    <?php foreach ($stats['top_teachers'] as $teacher): ?>
                        <div class="flex justify-between items-center mb-2">
                            <span><?php echo htmlspecialchars($teacher['name']); ?></span>
                            <span class="text-blue-600"><?php echo $teacher['subscriptions']; ?> students</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="bg-white p-6 rounded-lg shadow col-span-2">
        <h3 class="text-lg font-semibold mb-4">Course Distribution by Category</h3>
        <div class="space-y-4">
            <?php foreach ($stats['courses_by_category'] as $categoryId => $count): 
                $category = Category::getById($categoryId);
                if ($category):
            ?>
                <div class="flex items-center justify-between">
                    <span><?php echo htmlspecialchars($category->getName()); ?></span>
                    <div class="flex items-center gap-4">
                        <div class="w-48 bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" 
                                 style="width: <?php echo ($count / $stats['total_courses'] * 100); ?>%">
                            </div>
                        </div>
                        <span class="text-gray-600"><?php echo $count; ?></span>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
            </div>

            <!-- Teacher Validation -->
            <div x-show="activeTab === 'teachers'" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Pending Teacher Validations</h2>
                <div class="space-y-4">
                    <?php foreach (User::getPendingTeachers() as $teacher): ?>
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <h3 class="font-medium"><?php echo htmlspecialchars($teacher['name']); ?></h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($teacher['email']); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="validateTeacher(<?php echo $teacher['id_user']; ?>, true)" 
                                        class="p-2 text-green-600 hover:bg-green-50 rounded">
                                    <i data-feather="check"></i>
                                </button>
                                <button onclick="validateTeacher(<?php echo $teacher['id_user']; ?>, false)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- User Management -->
            <div x-show="activeTab === 'users'" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">User Management</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-4">Name</th>
                                <th class="text-left p-4">Email</th>
                                <th class="text-left p-4">Role</th>
                                <th class="text-left p-4">Status</th>
                                <th class="text-left p-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (User::getAll() as $user): ?>
                                <tr class="border-b">
                                    <td class="p-4"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-sm <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <button onclick="manageUser(<?php echo $user['id_user']; ?>, '<?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?>')"
                                                    class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                <i data-feather="<?php echo $user['is_active'] ? 'eye-off' : 'eye'; ?>"></i>
                                            </button>
                                            <button onclick="manageUser(<?php echo $user['id_user']; ?>, 'delete')"
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Course Management -->
            <div x-show="activeTab === 'courses'" class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Course Management</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach (Course::getAll() as $course): ?>
                        <div class="border rounded-lg p-4">
                            <h3 class="font-medium"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($course['category']); ?></p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="px-2 py-1 rounded-full text-sm <?php echo $course['status'] === 'Published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo htmlspecialchars($course['status']); ?>
                                </span>
                                <div class="flex gap-2">
                                    <button onclick="manageCourse(<?php echo $course['id_course']; ?>, 'publish')"
                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                        <i data-feather="edit-2"></i>
                                    </button>
                                    <button onclick="manageCourse(<?php echo $course['id_course']; ?>, 'delete')"
                                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
<!-- Category Management -->
<div x-show="activeTab === 'categories'" class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Category Management</h2>
        <button id="addCategory" class="add-category bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
    <i data-feather="plus"></i>
    Add Category
</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($stats['categories'] as $category): ?>
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center">
                <!-- Use the getter method -->
                <h3 class="font-medium"><?php echo htmlspecialchars($category->getName()); ?></h3>
                <div class="flex gap-2">
                    <button onclick="manageCategory(<?php echo $category->getId(); ?>, 'edit')"
                            class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                        <i data-feather="edit-2"></i>
                    </button>
                    <button onclick="manageCategory(<?php echo $category->getId(); ?>, 'delete')"
                            class="p-1 text-red-600 hover:bg-red-50 rounded">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
            <p class="text-gray-600 mt-2">
                <?php 
                $courseCount = isset($stats['courses_by_category'][$category->getId()]) 
                    ? $stats['courses_by_category'][$category->getId()] 
                    : 0;
                echo $courseCount . ' courses';
                ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

</div>

            <!-- Tag Management -->
            <div x-show="activeTab === 'tags'" class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Tag Management</h2>
                    <button onclick="showBulkTagModal()"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i data-feather="plus"></i>
                        Bulk Add Tags
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach (Tag::getAll() as $tag): ?>
    <div class="flex items-center justify-between p-3 border rounded-lg">
        <span><?php echo htmlspecialchars($tag->getname()); ?></span>
        <div class="flex gap-2">
            <button onclick="manageTag(<?php echo $tag->getId(); ?>, 'delete')"
                    class="p-1 text-red-600 hover:bg-red-50 rounded">
                <i data-feather="trash-2"></i>
            </button>
        </div>
    </div>
<?php endforeach; ?>

                </div>
            </div>
        </main>
    </div>

    <script>
      function showBulkTagModal() {
    Swal.fire({
        title: 'Add Bulk Tags',
        input: 'textarea',
        inputPlaceholder: 'Enter tags separated by commas...',
        showCancelButton: true,
        confirmButtonText: 'Add Tags',
        showLoaderOnConfirm: true,
        preConfirm: (tags) => {
            return fetch('AdminDashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `bulk_tags=${encodeURIComponent(tags)}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error adding tags');
                }
                return data;
            });
        }
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: 'Success',
                text: 'Tags have been added successfully',
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function manageCategory(categoryId, action) {
    if (action === 'delete') {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                submitCategoryAction(categoryId, action);
            }
        });
    } else {
        submitCategoryAction(categoryId, action);
    }
}

function submitCategoryAction(categoryId, action) {
    fetch('AdminDashboard.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `manage_category_id=${categoryId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', 'Category action completed successfully', 'success')
            .then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.error || 'An error occurred', 'error');
        }
    });
}



// Update the existing validateTeacher function
function validateTeacher(teacherId, validate) {
    Swal.fire({
        title: 'Are you sure?',
        text: validate ? 'This will approve the teacher account' : 'This will reject the teacher account',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: validate ? 'Yes, approve' : 'Yes, reject',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('AdminDashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `validate_teacher_id=${teacherId}&validate=${validate ? '1' : '0'}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success',
                        text: validate ? 'Teacher has been approved' : 'Teacher has been rejected',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error || 'An error occurred', 'error');
                }
            });
        }
    });
}
       

function manageUser(userId, action) {
    const actions = {
        'activate': 'activate this user',
        'deactivate': 'deactivate this user',
        'delete': 'delete this user'
    };

    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to ${actions[action]}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('AdminDashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `manage_user_id=${userId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success',
                        text: `User has been ${action}d`,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error || 'An error occurred', 'error');
                }
            });
        }
    });
}
function manageCourse(courseId, action) {
    if (action === 'delete') {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the course and all associated data",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitCourseAction(courseId, action);
            }
        });
    } else if (action === 'publish') {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will publish the course",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, publish it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitCourseAction(courseId, action);
            }
        });
    } else {
        submitCourseAction(courseId, action);
    }
}


function submitCourseAction(courseId, action) {
    fetch('AdminDashboard.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `manage_course_id=${courseId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let actionText;
            switch (action) {
                case 'publish':
                    actionText = 'published';
                    break;
                case 'delete':
                    actionText = 'deleted';
                    break;
                default:
                    actionText = action + 'ed'; // This handles other actions like 'edit'
            }

            Swal.fire({
                title: 'Success',
                text: `Course has been ${actionText}`,
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.error || 'An error occurred', 'error');
        }
    });
}

function manageTag(tagId, action) {
    if (action === 'delete') {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this tag",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitTagAction(tagId, action);
            }
        });
    }
}

function submitTagAction(tagId, action) {
    fetch('AdminDashboard.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `manage_tag_id=${tagId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success',
                text: data.message || 'Tag has been deleted',
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.error || 'Failed to delete tag', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while deleting the tag', 'error');
    });
}


// Updated bulk tag addition function
function showBulkTagModal() {
    Swal.fire({
        title: 'Add Bulk Tags',
        input: 'textarea',
        inputPlaceholder: 'Enter tags separated by commas...',
        inputAttributes: {
            'aria-label': 'Enter tags separated by commas'
        },
        showCancelButton: true,
        confirmButtonText: 'Add Tags',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: (tags) => {
            return fetch('AdminDashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `bulk_tags=${encodeURIComponent(tags)}`
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new TypeError("Expected JSON response but got " + contentType);
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Error adding tags');
                }
                return data;
            })
            .catch(error => {
                console.error('Error:', error);
                throw new Error('Failed to add tags. Please try again.');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: 'Success',
                text: result.value.message || 'Tags have been added successfully',
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        }
    }).catch(error => {
        Swal.fire('Error', error.message, 'error');
    });
}
        document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized');
    Alpine.data('dashboard', () => ({
        activeTab: 'stats'
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');
    feather.replace();
});

    </script>
</body>
</html>
