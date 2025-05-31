@vite('resources/css/app.css')
@vite('resources/js/translation.js')
<x-layout>
  <x-slot:title>Test Translation</x-slot:title>

  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Test Google Translate Integration</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Manual translation test -->
      <div class="p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Manual Translation Test</h2>
        
        <div class="mb-4">
          <label for="arabicText" class="block text-sm font-medium text-gray-700 mb-2">Enter Arabic Text:</label>
          <textarea 
            id="arabicText" 
            rows="3" 
            class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:shadow-outline text-right"
            placeholder="أدخل النص العربي هنا"
          ></textarea>
        </div>
        
        <button 
          id="translateBtn" 
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
        >
          Translate
        </button>
        
        <div class="mt-4">
          <h3 class="text-md font-medium text-gray-700 mb-2">Translation Result:</h3>
          <div id="translationResult" class="p-3 bg-gray-100 rounded-lg min-h-16"></div>
        </div>
      </div>
      
      <!-- Automatic translation test with attributes -->
      <div class="p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Attribute-Based Translation Test</h2>
        
        <div class="space-y-4">
          <!-- Test 1 -->
          <div class="p-4 border rounded-lg">
            <div class="arabic-text mb-2" data-translate-arabic="كَتَبَ">كَتَبَ</div>
            <div class="translation-text"></div>
          </div>
          
          <!-- Test 2 -->
          <div class="p-4 border rounded-lg">
            <div class="arabic-text mb-2" data-translate-arabic="قَرَأَ">قَرَأَ</div>
            <div class="translation-text"></div>
          </div>
          
          <!-- Test 3 -->
          <div class="p-4 border rounded-lg">
            <div class="arabic-text mb-2" data-translate-arabic="جَلَسَ">جَلَسَ</div>
            <div class="translation-text"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Manual translation button
      document.getElementById('translateBtn').addEventListener('click', function() {
        const arabicText = document.getElementById('arabicText').value;
        const resultElement = document.getElementById('translationResult');
        
        if (arabicText.trim() === '') {
          resultElement.textContent = 'Please enter text to translate';
          return;
        }
        
        // Call translation function from translation.js
        translateArabicToIndonesian(arabicText, resultElement);
      });
    });
  </script>
</x-layout>
