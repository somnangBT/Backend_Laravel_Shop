<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LuckShop — Admin Login</title>
    <!-- Favicon -->
    <link rel="icon" href="/images/luckshop-logo.png" type="image/png">
   
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
  
    <!-- Tailwind Config -->
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Poppins', 'sans-serif'],
            },
            colors: {
              'accent-blue': '#3B82F6',
              'accent-hover': '#2563EB'
            }
          }
        }
      }
    </script>

    <style>
      .input-focus:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
      }

      .btn-gradient {
        background: linear-gradient(90deg, #3B82F6 0%, #2563EB 100%);
      }
    </style>
</head>
  
<body class="min-h-screen flex items-center justify-center bg-white text-gray-900 p-4">
  <div class="w-full max-w-3xl rounded-2xl shadow-xl bg-white border border-gray-200 relative overflow-hidden grid grid-cols-1 md:grid-cols-2">
    <!-- Left Column: Login Form -->
    <div class="relative z-10 p-8 sm:p-10 bg-white">
      <!-- Compact Shop Logo on Mobile -->
      <div class="flex justify-center mb-5 md:hidden">
        <img
          src="/images/luckshop-logo.png"
          alt="LuckShop Logo"
          class="w-16 h-16 rounded-full object-cover border-2 border-amber-400 shadow bg-white"
        >
      </div>

      <h2 class="text-3xl font-bold mb-2 text-gray-900 text-center md:text-left">Login to Admin</h2>
      <p class="text-gray-500 mb-8 text-center md:text-left">Sign in to continue your Account</p>

      @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 border border-green-300">
          <p class="text-green-700 text-center text-sm">{{ session('success') }}</p>
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-300">
          <p class="text-red-700 text-center text-sm">{{ $errors->first() }}</p>
        </div>
      @endif

      <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf

        <div class="relative">
          <label class="block text-sm font-medium mb-1 text-gray-700">Email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </div>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 text-gray-900 transition-all input-focus focus:outline-none focus:ring-2 focus:ring-accent-blue focus:bg-white"
              placeholder="you@example.com"
              required
            />
          </div>
        </div>

        <div class="relative">
          <label class="block text-sm font-medium mb-1 text-gray-700">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <input
              type="password"
              name="password"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-300 placeholder-gray-400 text-gray-900 transition-all input-focus focus:outline-none focus:ring-2 focus:ring-accent-blue focus:bg-white"
              placeholder="••••••••"
              required
            />
            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" id="password-toggle">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-accent-blue focus:ring-accent-blue border-gray-300 rounded bg-white">
            <label for="remember-me" class="ml-2 block text-sm text-gray-600">Remember me</label>
          </div>
          <div class="text-sm">
            <a href="#" class="font-medium text-accent-blue hover:text-accent-hover">Forgot password?</a>
          </div>
        </div>

        <div>
          <button
            type="submit"
            class="w-full py-3 px-4 btn-gradient hover:opacity-95 rounded-lg font-semibold text-white transition-all shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center"
          >
            <span>Sign In</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </form>
    </div>

    <!-- Right Column: Shop Logo -->
    <div class="hidden md:flex flex-col items-center justify-center p-8 bg-gray-50 border-l border-gray-200 text-center">
      <img
        src="/images/luckshop-logo.png"
        alt="LuckShop Logo"
        class="w-48 h-48 rounded-full object-cover border-2 border-amber-400 shadow-lg bg-white mb-5"
      >
      <h3 class="text-2xl font-bold text-gray-900 tracking-wide">Luck<span class="text-orange-500">Shop</span></h3>
      <p class="text-sm text-gray-500 mt-1">Admin Management Portal</p>
    </div>
  </div>

  <script>
    // Password visibility toggle
    document.getElementById('password-toggle').addEventListener('click', function() {
      const passwordInput = document.querySelector('input[name="password"]');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      this.innerHTML = type === 'password' 
        ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';
    });
  </script>
</body>
</html>