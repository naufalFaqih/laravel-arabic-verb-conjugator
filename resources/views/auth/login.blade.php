<!-- filepath: c:\laragon\www\latihanLaravel11\resources\views\auth\login.blade.php -->
@vite('resources/css/app.css')
<x-layout>
  <x-slot:title>Login - Tashrif Arab</x-slot:title>

  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
      <div>
        <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
          Masuk ke Akun Anda
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Atau
          <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            daftar akun baru
          </a>
        </p>
      </div>
      
      @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4" role="alert">
          <p>{{ session('error') }}</p>
        </div>
      @endif
      
      @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4" role="alert">
          <p>{{ session('success') }}</p>
        </div>
      @endif

      <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
        @csrf <!-- CSRF Protection -->
        <div class="rounded-md -space-y-px">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
            <input 
              id="email" 
              name="email" 
              type="email" 
              autocomplete="email" 
              required 
              value="{{ old('email') }}" 
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="nama@domain.com"
            >
            @error('email')
              <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input 
              id="password" 
              name="password" 
              type="password" 
              autocomplete="current-password" 
              required 
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="••••••••"
            >
            @error('password')
              <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input 
              id="remember" 
              name="remember" 
              type="checkbox" 
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-900">
              Ingat Saya
            </label>
          </div>

          <!-- Opsional: Link Lupa Password -->
          <!-- <div class="text-sm">
            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
              Lupa password?
            </a>
          </div> -->
        </div>

        <div>
          <button 
            type="submit" 
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <!-- Lock Icon -->
              <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </span>
            Masuk
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>