<?php

require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\User;

$showAlert = false;
$alertMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user = User::signin($email, $password);
        // Start session and set user session variables
        session_start();
        $_SESSION['id_user'] = $user->getId();
        $_SESSION['name'] = $user->getName();
        $_SESSION['role'] = $user->getRole();
        $showAlert = true;
        $alertMessage = "Welcome back, " . $user->getName() . "!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showAlert(message) {
            Swal.fire({
                title: 'Login Successful!',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'home.php';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showAlert): ?>
                showAlert('<?php echo $alertMessage; ?>');
            <?php endif; ?>
        });
    </script>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8 transform transition-all duration-500 hover:shadow-2xl">
      <!-- Header Section -->
      <div class="text-center space-y-2">
        <div class="flex justify-center">
          <div class="relative">
            <div class="absolute -inset-1 bg-violet-100 rounded-lg blur opacity-25"></div>
            <h1 class="relative text-3xl font-bold text-violet-600">Youdemy</h1>
          </div>
        </div>
        <p class="text-slate-500">Welcome back to your learning journey</p>
      </div>

    
    <!-- Login Form -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php" class="space-y-6">
      <!-- Email Field -->
      <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
        <div class="relative group">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2 group-hover:text-violet-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m12 4H4m0-4h12"/></svg>
              <input
              type="email"
              name="email"
              class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
              placeholder="Enter your email"
            />
            </div>
        </div>

        <!-- Password Field -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-slate-700">Password</label>
          <div class="relative group">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 transform -translate-y-1/2 group-hover:text-violet-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m0-6v6m-4-6v6m0-6v6m0 0a2 2 0 100-4 2 2 0 100 4m4 0a2 2 0 100-4 2 2 0 100 4m4 0a2 2 0 100-4 2 2 0 100 4"/></svg>
            <input
              type="password"
              name="password"
              class="w-full pl-10 pr-4 py-3 rounded-lg border border-slate-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all duration-200"
              placeholder="Enter your password"
            />
          </div>
        </div>
        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
          <label class="flex items-center space-x-2 group cursor-pointer">
            <input
              type="checkbox"
              class="rounded border-slate-300 text-violet-600 focus:ring-violet-500 transition-colors duration-200"
            />
            <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors duration-200">Remember me</span>
          </label>
          <a href="#" class="text-sm text-violet-600 hover:text-violet-700 transition-colors duration-200 hover:underline">Forgot password?</a>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          class="w-full bg-violet-600 text-white py-3 px-4 rounded-lg hover:bg-violet-700 transform transition-all duration-200 hover:scale-[1.02] flex items-center justify-center space-x-2 relative overflow-hidden group"
        >
          <span class="relative">Login In</span>
        </button>
    </form>


    <!-- Divider -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-slate-500">Or continue with</span>
        </div>
      </div>

      <!-- Social Buttons -->
      <div class="grid grid-cols-2 gap-4">
        <button class="flex items-center justify-center px-4 py-2 border border-slate-200 rounded-lg hover:border-violet-200 hover:bg-violet-50 transition-all duration-200">
          <img src="https://via.placeholder.com/20" alt="Google" class="w-5 h-5 mr-2" />
          <span class="text-sm text-slate-600">Google</span>
        </button>
        <button class="flex items-center justify-center px-4 py-2 border border-slate-200 rounded-lg hover:border-violet-200 hover:bg-violet-50 transition-all duration-200">
          <img src="https://via.placeholder.com/20" alt="GitHub" class="w-5 h-5 mr-2" />
          <span class="text-sm text-slate-600">GitHub</span>
        </button>
      </div>

      <!-- Sign Up Link -->
      <div class="text-center">
        <p class="text-slate-600">Don't have an account? <a href="#" class="text-violet-600 hover:text-violet-700 transition-colors duration-200 font-medium hover:underline">Sign up</a></p>
      </div>
    </div>
  </div>

</body>
</html>
