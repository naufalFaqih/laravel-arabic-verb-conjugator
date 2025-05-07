@vite('resources/css/app.css')
<x-layout>
  <x-slot:title>{{ $title }}</x-slot:title>

  <form id="searchForm" class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
    <label for="verb" class="block text-sm font-bold text-gray-700 text-center mb-2">Masukkan Kata Kerja (Fiil):</label>
    <input
      type="text"
      id="verb"
      name="verb"
      class="block w-3/4 md:w-1/2 lg:w-1/3 mx-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-md p-2 text-right font-bold"
      placeholder="Contoh: اشتغل, سَلّمَ, لعب"
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
  <h3 class="text-md  text-gray-700 text-right">Informasi Kata Kerja:</h3>
  <p id="verbInfoContent" class="mt-2 text-lg text-gray-600 text-right font-bold"></p>
  <h3 class="text-md text-gray-700 text-right mt-6">Ditemukan Juga Pada Bab:</h3>
  <ul id="suggestList" class="mt-4 mb-2 text-lg text-gray-600 text-right font-bold"></ul>
</div>

{{-- Hasil Pencarian --}}
<div id="result" class="mt-6 p-4 bg-white rounded-lg shadow-md hidden">
  <h3 class="text-lg text-gray-700 text-right font-bold mb-3">Hasil Pencarian:</h3>
  <div id="resultColumns" class="grid grid-cols-1 md:grid-cols-8 gap-4 text-center font-bold"></div>

  <div id="dataWrapper" class="flex flex-nowrap gap-4 mt-6 text-center font-bold">  
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

  <script src="@vite('resources/js/search.js')"></script>
</x-layout>