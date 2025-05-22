@vite('resources/css/app.css')
@vite('resources/js/search.js')
<x-layout>
  <x-slot:title>{{ $title }}</x-slot:title>

<!-- Header dengan Auth Status -->
  <div class="mb-8 bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
      @auth
        <div class="flex flex-col sm:flex-row items-center justify-between">
          <div class="mb-4 sm:mb-0">
            <h2 class="text-xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h2>
          </div>
          
          <div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Logout
              </button>
            </form>
          </div>
        </div>
      @else
        <div class="flex flex-col sm:flex-row items-center justify-between">
          <div class="mb-4 sm:mb-0">
            <h2 class="text-xl font-bold text-gray-800">Selamat datang di Tashrif Arab</h2>
            <p class="text-sm text-gray-600">Silahkan login untuk mengakses fitur lengkap.</p>
          </div>
          
          <div class="space-x-2">
            <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 inline-block">
              Login
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 inline-block">
              Register
            </a>
          </div>
        </div>
      @endauth
    </div>
  </div>

  <!-- Flash Messages dari Session -->
  @if(session('success'))
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
      <p>{{ session('success') }}</p>
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
      <p>{{ session('error') }}</p>
    </div>
  @endif


  <form id="searchForm" class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md" data-purpose="search-verb">
    <label for="verb" class="block text-sm font-bold text-gray-700 text-center mb-2">Masukkan Kata Kerja (Fiil):</label>
    <input
      type="text"
      id="verb"
      name="verb"
      class="block w-3/4 md:w-1/2 lg:w-1/3 mx-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-md p-2 text-right font-bold"
      placeholder="اشتغل, سَلّمَ, لعب :Contoh" 
      pattern="^[\u0600-\u06FF\s]+$" 
      title="Hanya diperbolehkan karakter dalam bahasa Arab"
      required
    />
    <button
      type="submit"
      id ="searchButton"
      class="mt-4 block mx-auto px-6 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Tashrif
    </button>
  </form>

{{-- Loading Screen --}}
<div id="loading" class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md hidden text-center">
  <div class="spinner mx-auto"></div>
  <p class="text-lg font-medium text-gray-700">Memproses permintaan...</p>
</div>

{{-- Informasi Kata Kerja --}}
<div id="verbInfo" class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md hidden">
  <h3 class="text-md  text-gray-700 text-right">:Informasi Kata Kerja</h3>
  <p id="verbInfoContent" class="mt-2 text-lg text-gray-600 text-right font-bold"></p>
  <h3 class="text-md text-gray-700 text-right mt-6">:Ditemukan Juga Pada Bab:</h3>
  <ul id="suggestList" class="mt-4 mb-2 text-lg text-gray-600 text-right font-bold"></ul>
</div>

{{-- Hasil Pencarian --}}
<div id="result" class="mt-6 p-4 bg-white rounded-lg shadow-md hidden">
  <h3 class="text-lg text-gray-700 text-right font-bold mb-3">Tashrif Lughowi / تصرف لغوي:</h3>
    <div id="resultColumns" class="grid grid-cols-1 md:grid-cols-8 gap-4 text-center font-bold break-words overflow-hidden"></div>
    <div id="dataWrapper" class="flex flex-row gap-4 mt-6 text-center font-bold ">  
      <div id="amarMuakkadData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="amarData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="mudhoriMuakkadData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="mudhoriMansubData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="mudhoriMajzumData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="mudhoriMalumData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="madhiMalumData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
      <div id="domirData" class="p-4 bg-gray-100 rounded-lg shadow-md w-full md:w-1/2">
      </div>
    </div>
</div>



@auth
    <!-- Section riwayat pencarian (hanya untuk user yang login) -->
    <div class="mt-8 p-6 bg-gray-50 rounded-lg shadow-md border border-gray-100">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Riwayat Pencarian Terakhir</h3>
        <a href="{{ route('history') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
          Lihat Semua
        </a>
      </div>
      
      @if(Schema::hasTable('search_histories') && Auth::user()->searchHistories()->count() > 0)
        <div class="divide-y divide-gray-100">
          @foreach(Auth::user()->searchHistories()->latest()->take(5)->get() as $history)
            <div class="py-2 flex justify-between items-center">
              <div class="flex-1">
                <span class="text-right text-gray-800 font-medium">{!! $history->query !!}</span>
                <div class="text-xs text-gray-500">{{ $history->created_at->diffForHumans() }}</div>
              </div>
              <a href="/?query={{ urlencode($history->query) }}" class="ml-4 text-indigo-600 hover:text-indigo-900 text-sm">
                Cari lagi
              </a>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-gray-500 text-sm">Belum ada riwayat pencarian.</p>
      @endif
    </div>
@endauth

</x-layout>