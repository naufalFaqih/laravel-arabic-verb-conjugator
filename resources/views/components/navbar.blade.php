<nav class="bg-gray-800" x-data="{ isOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="shrink-0">
            <img class="size-8" src="{{ asset('img/logo.jpg') }}" alt="Logo Baru">
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <x-nav-link href='/' :active="request()->is('/')">{{ __('messages.home') }}</x-nav-link>
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center space-x-3 md:ml-6">

            <!-- Language Switcher -->
            <div class="flex items-center space-x-1 bg-gray-900/60 p-1 rounded-lg border border-gray-700/60 text-xs font-semibold">
                <a href="{{ route('lang.switch', 'id') }}" 
                   class="px-2.5 py-1 rounded-md transition-all duration-150 {{ app()->getLocale() == 'id' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-400 hover:text-white' }}">
                   ID
                </a>
                <a href="{{ route('lang.switch', 'en') }}" 
                   class="px-2.5 py-1 rounded-md transition-all duration-150 {{ app()->getLocale() == 'en' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-400 hover:text-white' }}">
                   EN
                </a>
            </div>

            <!-- Auth Links -->
            @guest
                <a href="{{ route('login') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">{{ __('messages.login') }}</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">{{ __('messages.register') }}</a>
                @endif
            @else
                <a href="{{ route('history') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('messages.history') }}
                </a>

            <!-- Profile dropdown -->
            <div class="relative ml-3">
              <div>
                <button type="button" @click="isOpen = !isOpen" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                  <span class="absolute -inset-1.5"></span>
                  <span class="sr-only">Open user menu</span>
                        @if (Auth::user()->profile_photo_path)
                            <img class="size-8 rounded-full" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}">
                        @else
                            <img class="size-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                        @endif
                </button>
              </div>

              <div x-show="isOpen"
              x-transition:enter="transition ease-out duration-100 transform"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-75 transform"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95"
              class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                     <div class="px-4 py-2 text-xs text-gray-500">
                         {{ Auth::user()->name }}
                     </div>

                     <a href="{{ route('history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ __('messages.history') }}</a>

                     <form method="POST" action="{{ route('logout') }}">
                         @csrf
                         <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                             {{ __('messages.logout') }}
                         </button>
                     </form>
            </div>
          </div>
           @endguest
          </div>
        </div>

        <div class="-mr-2 flex items-center md:hidden space-x-2">
          <!-- Language Switcher Mobile -->
          <div class="flex items-center space-x-1 bg-gray-900/60 p-1 rounded-lg border border-gray-700/60 text-xs font-semibold">
              <a href="{{ route('lang.switch', 'id') }}" 
                 class="px-2 py-0.5 rounded transition-all duration-150 {{ app()->getLocale() == 'id' ? 'bg-indigo-600 text-white' : 'text-gray-400' }}">
                 ID
              </a>
              <a href="{{ route('lang.switch', 'en') }}" 
                 class="px-2 py-0.5 rounded transition-all duration-150 {{ app()->getLocale() == 'en' ? 'bg-indigo-600 text-white' : 'text-gray-400' }}">
                 EN
              </a>
          </div>

          <!-- Mobile menu button -->
          <button type="button" @click="isOpen = !isOpen" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open main menu</span>
            <svg :class="{'hidden': isOpen, 'block': !isOpen }" class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg :class="{'block': isOpen, 'hidden': !isOpen }" class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="isOpen" class="md:hidden" id="mobile-menu">
      <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
        <a href="/" class="block rounded-md px-3 py-2 text-base font-medium {{ request()->is('/') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">{{ __('messages.home') }}</a>
      </div>

      <div class="border-t border-gray-700 pb-3 pt-4">
        @guest
            <div class="mt-3 space-y-1 px-2">
              <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ __('messages.login') }}</a>
              @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ __('messages.register') }}</a>
              @endif
            </div>
        @else
            <div class="flex items-center px-5">
              <div class="shrink-0">
                @if (Auth::user()->profile_photo_path)
                    <img class="size-10 rounded-full" src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}">
                @else
                    <img class="size-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                @endif
              </div>
              <div class="ml-3">
                <div class="text-base/5 font-medium text-white">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
              </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
              <a href="{{ route('history') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ __('messages.history') }}</a>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
                  {{ __('messages.logout') }}
                </button>
              </form>
            </div>
        @endguest
      </div>
    </div>
  </nav>
