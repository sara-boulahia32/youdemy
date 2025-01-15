<?php

require_once __DIR__ . '/../src/config/autoloader.php';

// Set session cookie parameters to expire after 12 hours
session_set_cookie_params(43200);
ini_set('session.gc_maxlifetime', 43200);

// Start the session
session_start();

// Your existing code...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Youdemy</title>
  <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/lib/index.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50">
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

  <!-- Hero Section -->
  <div class="relative overflow-hidden bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-8">
          <h1 class="text-4xl lg:text-5xl font-bold text-slate-800">
            Welcome to the Online
            <span class="block text-violet-600">Learning Center</span>
          </h1>
          <p class="text-lg text-slate-600">
            Découvrez des milliers de cours en ligne et développez vos compétences avec les meilleurs experts.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <button class="px-8 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-all">
              Get Started
            </button>
            <button class="px-8 py-3 border border-slate-200 text-slate-700 rounded-lg hover:border-violet-600 hover:text-violet-600 transition-all">
              Explore Courses
            </button>
          </div>
        </div>
        <div class="relative">
          <div class="w-full h-96 relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-violet-100 rounded-full opacity-20"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-100 rounded-full opacity-20"></div>
            <img src="/api/placeholder/500/400" alt="Students learning" class="relative z-10 rounded-2xl">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Popular Subjects -->
  <div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold text-slate-800 mb-4">Popular Subjects</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-violet-200 transition-all duration-300">
          <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
            <i class="w-6 h-6">Code Icon</i>
          </div>
          <h3 class="text-slate-800 font-medium mb-1">Programming</h3>
          <p class="text-sm text-slate-500">120+ Courses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-violet-200 transition-all duration-300">
          <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
            <i class="w-6 h-6">Globe Icon</i>
          </div>
          <h3 class="text-slate-800 font-medium mb-1">Language</h3>
          <p class="text-sm text-slate-500">85+ Courses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-violet-200 transition-all duration-300">
          <div class="w-12 h-12 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center mb-4">
            <i class="w-6 h-6">Monitor Icon</i>
          </div>
          <h3 class="text-slate-800 font-medium mb-1">Marketing</h3>
          <p class="text-sm text-slate-500">90+ Courses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 hover:border-violet-200 transition-all duration-300">
          <div class="w-12 h-12 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center mb-4">
            <i class="w-6 h-6">Bookmark Icon</i>
          </div>
          <h3 class="text-slate-800 font-medium mb-1">Networking</h3>
          <p class="text-sm text-slate-500">70+ Courses</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Featured Courses -->
  <div class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-slate-800">Perfect Online Course For Your Career</h2>
        <button class="text-violet-600 hover:text-violet-700 transition-colors">
          View All Courses
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl overflow-hidden border border-slate-100 hover:border-violet-200 transition-all duration-300">
          <div class="aspect-video bg-slate-100">
            <img src="/api/placeholder/400/225" alt="Course thumbnail" class="w-full h-full object-cover">
          </div>
          <div class="p-6">
            <div class="flex items-center space-x-2 mb-4">
              <span class="px-3 py-1 bg-violet-50 text-violet-600 rounded-full text-sm">Development</span>
              <span class="text-slate-500 text-sm">4.5 ★</span>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">Learn Web Development from Scratch</h3>
            <p class="text-slate-600 text-sm mb-4">Master the fundamentals of web development with practical projects.</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-slate-200"></div>
                <span class="text-sm text-slate-600">John Doe</span>
              </div>
              <span class="text-violet-600 font-semibold">$49.99</span>
            </div>
          </div>
        </div>
        <!-- Repeat similar course cards as needed -->
      </div>
    </div>
  </div>
</body>
</html>
