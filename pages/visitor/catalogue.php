<?php
require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Tag;
use Models\Course;
$tags = Tag::getAll(); // Adjust if necessary
$courses = Course::getAll();
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
      <input
        type="text"
        placeholder="Search for courses..."
        class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-violet-600 focus:border-transparent outline-none"
      />
    </div>
  </div>

<!-- Courses Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($courses as $course): ?>
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
        <!-- Course Image -->
        <?php if ($course->getMediaPath()): ?>
          <div class="relative">
            <img src="<?php echo htmlspecialchars($course->getMediaPath()); ?>" alt="<?php echo htmlspecialchars($course->getTitle()); ?>" class="w-full h-48 object-cover rounded-t-2xl">
            <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-violet-600">
              $<?php echo htmlspecialchars($course->getPrice()); ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Course Content -->
        <div class="p-6">
          <!-- Tags -->
          <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($course->getTags() as $tag): ?>
              <span class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm">
                <?php echo htmlspecialchars($tag->getName()); ?>
              </span>
            <?php endforeach; ?>
          </div>

          <!-- Title & Description -->
          <h3 class="text-xl font-semibold text-slate-800 mb-2"><?php echo htmlspecialchars($course->getTitle()); ?></h3>
          <p class="text-slate-600 text-sm mb-4"><?php echo htmlspecialchars(substr($course->getDescription(), 0, 100)); ?>...</p>

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
            <button class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors">Enroll Now</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
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
        <label for="content" class="block text-slate-800">Contenu (Vidéo ou Document)</label>
        <input type="file" id="content" name="content" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600">
    </div>
    <div class="mb-4">
        <label for="category" class="block text-slate-800">Catégorie</label>
        <input type="text" id="category" name="category" class="w-full px-4 py-2 border border-slate-200 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-violet-600" required>
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
</body>
</html>
