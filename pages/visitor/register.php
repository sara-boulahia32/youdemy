<?php

require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\User;

$showAlert = false;
$alertMessage = '';
$alertRole = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $is_active = ($role === 'student') ? 1 : 0;

    try {
        User::signup($name, $email, $password, $role, $is_active);
        $showAlert = true;
        $alertMessage = 'You have successfully registered as a ' . $role . '.';
        $alertRole = $role;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
    <script>
        function setRole(role) {
            document.getElementById('role').value = role;
        }

        function showAlert(message, role) {
            Swal.fire({
                title: 'Registration Successful!',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }
    </script>

</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
<div class="w-full max-w-xl">
    <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8 transform transition-all duration-500 hover:shadow-2xl">
      <!-- Header -->
      <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-violet-600">Join Youdemy</h1>
        <p class="text-slate-500">Start your learning journey today</p>
      </div>

      <!-- Role Selection -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <button
        type="button"
          class="p-6 rounded-xl border-2 transition-all duration-300 flex flex-col items-center space-y-3 hover:shadow-md border-slate-200 hover:border-violet-200" onclick="setRole('student')">
          <div class="p-3 rounded-full bg-slate-100">
            <svg class="w-6 h-6 text-slate-600" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7l-9-5 9-5 9 5-9 5z" />
            </svg>
          </div>
          <h3 class="font-semibold text-slate-800">Student</h3>
          <p class="text-sm text-slate-500 text-center">Join courses and learn from experts</p>
        </button>

        <button
        type="button"
          class="p-6 rounded-xl border-2 transition-all duration-300 flex flex-col items-center space-y-3 hover:shadow-md border-slate-200 hover:border-violet-200" onclick="setRole('teacher')">
          <div class="p-3 rounded-full bg-slate-100">
            <svg class="w-6 h-6 text-slate-600" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2-2m0 0l2-2m-2 2l-2 2m2-2v6m6-2l2-2m0 0l2-2m-2 2l-2 2m2-2v6" />
            </svg>
          </div>
          <h3 class="font-semibold text-slate-800">Teacher</h3>
          <p class="text-sm text-slate-500 text-center">Create courses and share knowledge</p>
        </button>
      </div>

      <!-- Registration Form -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="register.php" class="space-y-6">
      <!-- Full Name Input -->
      <div class="space-y-2">
          <label class="block text-sm font-medium text-slate-700">Full Name</label>
          <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3" />
            </svg>
        <input type="text" id="name" name="name" class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
        placeholder="Enter your full name" required>
        </div>
        </div>


        <!-- Email Input -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-slate-700">Email</label>
          <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2v-2" />
            </svg>
            <input
              type="email"
              name="email"
              class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
              placeholder="Enter your email"
            />
          </div>
        </div>
<!-- Password Input -->
<div class="space-y-2">
          <label class="block text-sm font-medium text-slate-700">Password</label>
          <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-2.2 1.8-4 4-4s4 1.8 4 4-1.8 4-4 4-4-1.8-4-4zm-2-7v6" />
            </svg>
            <input
              type="password"
              class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
              placeholder="Create a password"
            />
          </div>
        </div>

        <!-- Confirm Password Input -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
          <div class="relative">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11h14M5 19h14M5 5h14" />
            </svg>
            <input
              type="password"
              name="password"
              class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
              placeholder="Confirm your password"
            />
          </div>
        </div>

        <!-- Hidden Role Input --> <input type="hidden" id="role" name="role" value="student">

        <!-- Register Button -->
        <button
          type="submit"
          class="w-full py-3 px-4 rounded-lg bg-violet-600 hover:bg-violet-700 text-white transform transition-all duration-200 hover:scale-105 flex items-center justify-center">
          <span>Create Account</span>
        </button>
      </form>
</body>
</html>

