@vite('resources/css/app.css')
<x-layout>
    <x-slot:title>Welcome to ArabicMorph - Arabic Conjugation Tool</x-slot:title>
  <!-- Hero Section -->
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50">

    <!-- Navigation Bar -->
    <nav class="shadow-sm">
      <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <img src="{{ asset('img/logo_nav.png') }}" alt="ArabicMorph Logo" class="h-40 w-90 mr-3 mt-8">
          </div>
          <div class="flex items-center space-x-4">
            <!-- Language Switcher -->
            <div class="flex items-center space-x-1 bg-gray-100 p-1 rounded-lg border border-gray-200 text-xs font-semibold">
                <a href="{{ route('lang.switch', 'id') }}" 
                   class="px-2.5 py-1 rounded-md transition-all duration-150 {{ app()->getLocale() == 'id' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                   ID
                </a>
                <a href="{{ route('lang.switch', 'en') }}" 
                   class="px-2.5 py-1 rounded-md transition-all duration-150 {{ app()->getLocale() == 'en' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                   EN
                </a>
            </div>
            @guest
              <a href="{{ route('home') }}" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium">
                {{ __('messages.go_to_app') }}
              </a>
            @endguest
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

      <!-- Main Hero -->
      <div class="text-center mb-16">
        <h2 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-6">
          {{ __('messages.hero_title') }}
        </h2>
        <p class="text-xl md:text-2xl text-gray-600 mb-4">
          {{ __('messages.hero_subtitle') }}
        </p>
        <p class="text-lg text-gray-500 mb-8 max-w-3xl mx-auto">
          {{ __('messages.hero_description') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="{{ route('home') }}"
             class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            {{ __('messages.start_searching') }}
          </a>
          <a href="#features"
             class="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-lg hover:bg-gray-50 transition duration-200 shadow-md border-2 border-indigo-600">
            {{ __('messages.learn_more') }}
          </a>
        </div>
      </div>

      <!-- Features Grid -->
      <div id="features" class="grid md:grid-cols-3 gap-8 mb-16">
        <!-- Feature 1 -->
        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-indigo-600">
          <div class="text-5xl mb-4 text-center">📚</div>
          <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">{{ __('messages.complete_tables') }}</h3>
          <p class="text-gray-600 text-center">
            {{ __('messages.complete_tables_desc') }}
          </p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-blue-600">
          <div class="text-5xl mb-4 text-center">🔍</div>
          <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">{{ __('messages.smart_search') }}</h3>
          <p class="text-gray-600 text-center">
            {{ __('messages.smart_search_desc') }}
          </p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-purple-600">
          <div class="text-5xl mb-4 text-center">💾</div>
          <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">{{ __('messages.search_history_feature') }}</h3>
          <p class="text-gray-600 text-center">
            {{ __('messages.search_history_feature_desc') }}
          </p>
        </div>
      </div>

      <!-- What is ArabicMorph Section -->
      <div class="bg-white rounded-xl shadow-xl p-8 md:p-12 mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">What is ArabicMorph?</h2>

        <div class="prose prose-lg max-w-none text-gray-700 space-y-4">
          <p>
            <strong>ArabicMorph</strong> is a comprehensive web-based tool designed specifically for students
            and learners of the Arabic language who want to master verb conjugation (Tashrif Lughowi / تصريف لغوي).
          </p>

          <p>
            Our platform provides instant access to complete conjugation tables for any Arabic verb,
            covering all chapter and pronouns according to classical Arabic morphology pattern.
          </p>

          <div class="bg-indigo-50 border-l-4 border-indigo-600 p-6 my-6 rounded-r-lg">
            <h3 class="text-xl font-bold text-indigo-900 mb-3">Perfect For:</h3>
            <ul class="space-y-2 text-indigo-800">
              <li class="flex items-start">
                <svg class="w-6 h-6 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Students studying Arabic morphology (Sharaf)
              </li>
              <li class="flex items-start">
                <svg class="w-6 h-6 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Islamic school (Madrasah/Pesantren) students
              </li>
              <li class="flex items-start">
                <svg class="w-6 h-6 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Arabic language learners and enthusiasts
              </li>
              <li class="flex items-start">
                <svg class="w-6 h-6 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Teachers preparing Arabic grammar materials
              </li>
            </ul>
          </div>

          <p>
            With ArabicMorph, you can quickly verify verb conjugations, practice your knowledge,
            and build a deeper understanding of Arabic morphology without flipping through textbooks.
          </p>
        </div>
      </div>

      <!-- How It Works Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl shadow-xl p-8 md:p-12 text-white mb-16">
        <h2 class="text-3xl font-bold mb-8 text-center">How It Works</h2>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="bg-white text-indigo-600 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              1
            </div>
            <h3 class="text-xl font-semibold mb-2">Enter Arabic Verb</h3>
            <p class="text-indigo-100">
              Type any Arabic verb in its root form (e.g., كَتَبَ, دَرَسَ, فَهِمَ)
            </p>
          </div>

          <div class="text-center">
            <div class="bg-white text-indigo-600 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              2
            </div>
            <h3 class="text-xl font-semibold mb-2">Get Instant Results</h3>
            <p class="text-indigo-100">
              View complete conjugation tables for all tenses and pronouns
            </p>
          </div>

          <div class="text-center">
            <div class="bg-white text-indigo-600 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
              3
            </div>
            <h3 class="text-xl font-semibold mb-2">Learn & Practice</h3>
            <p class="text-indigo-100">
              Study the patterns and save your searches for future reference
            </p>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="text-center bg-white rounded-xl shadow-xl p-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Master Arabic Verbs?</h2>
        <p class="text-xl text-gray-600 mb-8">
          Join thousands of students learning Arabic grammar with ArabicMorph
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="{{ route('home') }}"
             class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 shadow-lg">
            Start Learning Now - It's Free!
          </a>
          @guest
          <a href="{{ route('register') }}"
             class="inline-flex items-center justify-center px-8 py-4 bg-gray-200 text-gray-800 text-lg font-semibold rounded-lg hover:bg-gray-300 transition duration-200">
            Create Account
          </a>
          @endguest
        </div>
      </div>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gray-400">
          &copy; {{ date('Y') }} ArabicMorph. Made with ❤️ for Arabic learners.
        </p>
      </div>
    </footer>

  </div>

</x-layout>
