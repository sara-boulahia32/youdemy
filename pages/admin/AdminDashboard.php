<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/2.3.0/alpine.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="{ activeTab: 'teachers', showTagModal: false }">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm px-6 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="search" placeholder="Rechercher..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-200">
                    <i data-feather="search" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"></i>
                </div>
                <button class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-feather="bell" class="h-5 w-5 text-gray-600"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white h-screen shadow-sm p-4 hidden md:block">
            <nav class="space-y-2">
                <template x-for="tab in ['teachers', 'users', 'content', 'tags', 'stats']">
                    <button 
                        @click="activeTab = tab"
                        :class="`w-full flex items-center gap-3 px-4 py-3 rounded-lg ${
                            activeTab === tab ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'
                        }`"
                    >
                        <i :data-feather="
                            tab === 'teachers' ? 'users' :
                            tab === 'users' ? 'users' :
                            tab === 'content' ? 'book-open' :
                            tab === 'tags' ? 'tags' :
                            'bar-chart-2'
                        " class="h-5 w-5"></i>
                        <span x-text="
                            tab === 'teachers' ? 'Validation Enseignants' :
                            tab === 'users' ? 'Gestion Utilisateurs' :
                            tab === 'content' ? 'Gestion Contenus' :
                            tab === 'tags' ? 'Gestion Tags' :
                            'Statistiques'
                        "></span>
                    </button>
                </template>
            </nav>
        </aside>

        <!-- Mobile Menu -->
        <div class="md:hidden w-full bg-white shadow-sm p-4">
            <select @change="activeTab = $event.target.value" class="w-full p-2 border rounded-lg">
                <option value="teachers">Validation Enseignants</option>
                <option value="users">Gestion Utilisateurs</option>
                <option value="content">Gestion Contenus</option>
                <option value="tags">Gestion Tags</option>
                <option value="stats">Statistiques</option>
            </select>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Stats Section -->
            <div x-show="activeTab === 'stats'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Total Courses</h3>
                    <p class="text-3xl font-bold">156</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Top Course</h3>
                    <p class="text-xl font-medium">Web Development</p>
                    <p class="text-gray-600">325 students</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Top Teachers</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span>Marie Dubois</span>
                            <span class="text-blue-600 font-medium">4.8 ⭐️</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Jean Martin</span>
                            <span class="text-blue-600 font-medium">4.7 ⭐️</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Sophie Laurent</span>
                            <span class="text-blue-600 font-medium">4.6 ⭐️</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Section -->
            <div x-show="activeTab === 'teachers'" class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Teacher Validation</h2>
                    <div class="space-y-4">
                        <template x-for="i in 3">
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h3 class="font-medium" x-text="`Teacher Application #${i}`"></h3>
                                    <p class="text-gray-600" x-text="`Submitted on Jan ${i}, 2024`"></p>
                                </div>
                                <div class="flex gap-2">
                                    <button class="p-2 text-green-600 hover:bg-green-50 rounded-lg">
                                        <i data-feather="check" class="h-5 w-5"></i>
                                    </button>
                                    <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                        <i data-feather="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div x-show="activeTab === 'users'" class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">User Management</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4">User</th>
                                    <th class="text-left p-4">Role</th>
                                    <th class="text-left p-4">Status</th>
                                    <th class="text-left p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="p-4">John Doe</td>
                                    <td class="p-4">Student</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <button class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                                <i data-feather="edit" class="h-4 w-4"></i>
                                            </button>
                                            <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                                <i data-feather="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Add more user rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div x-show="activeTab === 'content'" class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Content Management</h2>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i data-feather="plus" class="h-5 w-5"></i>
                            Add Course
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="course in [
                            { title: 'Web Development', category: 'Programming', status: 'Published' },
                            { title: 'Digital Marketing', category: 'Marketing', status: 'Draft' },
                            { title: 'UI/UX Design', category: 'Design', status: 'Published' }
                        ]">
                            <div class="border rounded-lg p-4">
                                <h3 class="font-medium" x-text="course.title"></h3>
                                <p class="text-gray-600" x-text="course.category"></p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span :class="`px-2 py-1 rounded-full text-sm ${
                                        course.status === 'Published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    }`" x-text="course.status"></span>
                                    <div class="flex gap-2">
                                        <button class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                            <i data-feather="edit" class="h-4 w-4"></i>
                                        </button>
                                        <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                            <i data-feather="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tags Section -->
            <div x-show="activeTab === 'tags'" class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Tag Management</h2>
                        <button 
                            @click="showTagModal = true"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                        >
                            <i data-feather="plus" class="h-5 w-5"></i>
                            Bulk Add Tags
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="tag in ['JavaScript', 'Python', 'React', 'Vue.js', 'Node.js', 'HTML', 'CSS', 'Database', 'AWS', 'DevOps']">
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <span x-text="tag"></span>
                                <div class="flex gap-2">
                                    <button class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                        <i data-feather="edit" class="h-4 w-4"></i>
                                    </button>
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                        <i data-feather="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bulk Tag Modal -->
    <div x-show="showTagModal" class="fixed hidden inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Add Multiple Tags</h3>
            <textarea class="w-full h-32 p-2 border rounded-lg mb-4" placeholder="Enter tags separated by commas..."></textarea>
            <div class="flex justify-end gap-2">
                <button @click="showTagModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button @click="showTagModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add Tags
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>