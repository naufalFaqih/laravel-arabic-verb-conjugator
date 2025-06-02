@vite('resources/css/app.css')
@vite('resources/js/translation.js')
@vite('resources/js/search.js')
@vite('resources/js/translation-debug.js')
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

{{-- Ringkasan Pencarian --}}
<div class="mt-6 p-4 bg-white rounded-lg shadow-md hidden" id="summary">
  <h3 class="text-lg text-gray-700 font-bold mb-3 text-right">:ملخص البحث / Ringkasan Pencarian</h3>
  
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200">
      <thead>
        <tr>
          <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
            الماضي
            <br/><span class="text-xs font-normal">Madhi (Lampau)</span>
          </th>
          <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
            المضارع
            <br/><span class="text-xs font-normal">Mudhori (Saat ini)</span>
          </th>
          <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
            الأمر
            <br/><span class="text-xs font-normal">Amar (Perintah)</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="px-4 py-2 text-center border border-gray-200">
            <div id="summary-madhi" class="text-lg font-bold arabic-text" data-translate-arabic="">-</div>
            <div class="translation-text text-xs mt-2 text-gray-600"></div>
          </td>
          <td class="px-4 py-2 text-center border border-gray-200">
            <div id="summary-mudhori" class="text-lg font-bold arabic-text" data-translate-arabic="">-</div>
            <div class="translation-text text-xs mt-2 text-gray-600"></div>
          </td>
          <td class="px-4 py-2 text-center border border-gray-200">
            <div id="summary-amar" class="text-lg font-bold arabic-text" data-translate-arabic="">-</div>
            <div class="translation-text text-xs mt-2 text-gray-600"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

{{-- Informasi Kata Kerja --}}
<div id="verbInfo" class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md hidden">
  <h3 class="text-md  text-gray-700 text-right">
    <span class="arabic-text" data-translate-arabic="">:Informasi Kata Kerja</span>
    <div class="translation-text text-xs mt-1"></div>
  </h3>
  <div class="mt-2">
    <div id="verbInfoContent" class="text-lg text-gray-600 text-right font-bold arabic-text" data-translate-arabic="">-</div>
    <div class="translation-text text-xs mt-2 text-gray-600"></div>
  </div>
  <h3 class="text-md text-gray-700 text-right mt-6">  
    <span class="arabic-text" data-translate-arabic="">:Ditemukan Juga Pada Bab</span>
    <div class="translation-text text-xs mt-1"></div>
  </h3>
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

<script>
// Translation Enhanced untuk DeepSeek API dengan debugging
window.TranslationEnhanced = {
    cache: new Map(),
    debugMode: true,

    log: function(message, data = null) {
        if (this.debugMode) {
            console.log(`[Translation] ${message}`, data || '');
        }
    },

    error: function(message, error = null) {
        console.error(`[Translation Error] ${message}`, error || '');
    },

    translateAll: function () {
        this.log("🔄 Starting DeepSeek translation for all elements...");

        const elements = document.querySelectorAll("[data-translate-arabic]");
        this.log(`📝 Found ${elements.length} elements to translate`);

        if (elements.length === 0) {
            this.log("⚠️ No elements with data-translate-arabic found");
            return;
        }

        elements.forEach((element, index) => {
            const arabicText = element.getAttribute("data-translate-arabic");

            this.log(`Element ${index + 1}:`, {
                id: element.id,
                text: arabicText,
                element: element
            });

            if (!arabicText || arabicText === "-" || arabicText.trim() === "") {
                this.log(`⏭️ Skipping element ${index + 1}: empty text`);
                return;
            }

            let translationElement = this.findOrCreateTranslationElement(element);
            this.translateElement(arabicText, translationElement, element.id || `element-${index}`);
        });
    },

    findOrCreateTranslationElement: function (element) {
        let translationElement = element.nextElementSibling;

        if (translationElement && translationElement.classList.contains("translation-text")) {
            this.log("Found existing translation element");
            return translationElement;
        }

        let parentTranslation = element.parentElement.querySelector(".translation-text");
        if (parentTranslation) {
            this.log("Found parent translation element");
            return parentTranslation;
        }

        this.log("Creating new translation element");
        translationElement = document.createElement("div");
        translationElement.className = "translation-text text-xs mt-2 text-gray-600";

        if (element.nextSibling) {
            element.parentNode.insertBefore(translationElement, element.nextSibling);
        } else {
            element.parentNode.appendChild(translationElement);
        }

        return translationElement;
    },

    translateElement: function (arabicText, targetElement, sourceId = "") {
        this.log(`🔤 Translating "${arabicText}" for element ${sourceId} using DeepSeek`);

        if (this.cache.has(arabicText)) {
            const cachedTranslation = this.cache.get(arabicText);
            this.log(`💾 Using memory cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            return Promise.resolve(cachedTranslation);
        }

        const cacheKey = `deepseek_translate_${btoa(encodeURIComponent(arabicText))}`;
        const cachedTranslation = localStorage.getItem(cacheKey);

        if (cachedTranslation && !/[\u0600-\u06FF]/.test(cachedTranslation)) {
            this.log(`💽 Using localStorage cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            this.cache.set(arabicText, cachedTranslation);
            return Promise.resolve(cachedTranslation);
        }

        targetElement.innerHTML = '<div class="translation-loading inline-flex gap-1"><span>•</span><span>•</span><span>•</span></div>';

        return this.callTranslationAPI(arabicText)
            .then((translation) => {
                if (translation) {
                    targetElement.textContent = translation;
                    this.cache.set(arabicText, translation);
                    localStorage.setItem(cacheKey, translation);
                    this.log(`✅ DeepSeek translation success for "${arabicText}": ${translation}`);
                    return translation;
                } else {
                    throw new Error("No translation returned");
                }
            })
            .catch((error) => {
                this.error(`Translation failed for "${arabicText}":`, error);
                const localTranslation = this.getLocalTranslation(arabicText);
                if (localTranslation) {
                    targetElement.textContent = localTranslation + " (lokal)";
                    return localTranslation;
                } else {
                    targetElement.innerHTML = '<span class="translation-error text-red-500 text-xs">Gagal menerjemahkan</span>';
                    return null;
                }
            });
    },

    callTranslationAPI: function (text) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            this.error("CSRF token not found");
            return Promise.reject(new Error("CSRF token not found"));
        }

        this.log(`🌐 Calling API for: "${text}"`);

        const requestData = {
            text: text,
            source: "ar",
            target: "id",
            force: false,
        };

        this.log("Request data:", requestData);

        return fetch("/api/translate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(requestData),
        })
        .then((response) => {
            this.log(`API Response status: ${response.status}`);
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then((data) => {
            this.log("API Response data:", data);
            
            if (data.success && data.translation) {
                return data.translation;
            }
            throw new Error(data.message || "Translation failed");
        })
        .catch((error) => {
            this.error("API call failed:", error);
            throw error;
        });
    },

    getLocalTranslation: function (text) {
        const localDict = {
            'الماضي': "masa lampau",
            'المضارع': "masa sekarang", 
            'الأمر': "perintah",
            'كَتَبَ': "menulis (dia lk)",
            'يَكْتُبُ': "sedang menulis (dia lk)",
            'اُكْتُبْ': "tulislah!",
            'Informasi Kata Kerja': "Informasi Kata Kerja",
            'Ditemukan Juga Pada Bab': "Ditemukan Juga Pada Bab"
        };

        if (localDict[text]) {
            return localDict[text];
        }

        for (const [key, value] of Object.entries(localDict)) {
            if (text.includes(key)) {
                return value + " (sebagian)";
            }
        }

        return null;
    },

    clearCache: function () {
        this.cache.clear();
        const keys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith("deepseek_translate_")) {
                keys.push(key);
            }
        }
        keys.forEach((key) => localStorage.removeItem(key));
        this.log(`🗑️ Cleared ${keys.length} DeepSeek translation cache entries`);
    },

    forceRetranslate: function () {
        this.log("🔄 Force retranslating all elements with DeepSeek...");
        this.clearCache();
        document.querySelectorAll(".translation-text").forEach((el) => {
            el.innerHTML = "";
        });
        setTimeout(() => {
            this.translateAll();
        }, 500);
    },

    testAPI: function() {
        this.log("🧪 Testing DeepSeek API...");
        
        return fetch('/api/translate/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            this.log("API Test Result:", data);
            return data;
        })
        .catch(error => {
            this.error("API Test Failed:", error);
            throw error;
        });
    }
};

// Auto-initialize
document.addEventListener("DOMContentLoaded", function () {
    console.log("🌐 DeepSeek Translation Enhanced loading...");

    // Tunggu sebentar untuk memastikan semua elemen dimuat
    setTimeout(() => {
        // Hapus cache terjemahan yang rusak
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith('deepseek_translate_')) {
                const value = localStorage.getItem(key);
                // Jika nilai cache adalah teks Arab (mengandung karakter Arab)
                if (/[\u0600-\u06FF]/.test(value)) {
                    console.log(`Removing invalid DeepSeek cache: ${key}`);
                    localStorage.removeItem(key);
                }
            }
        }
        
        // Tunggu sedikit kemudian terjemahkan semua
        setTimeout(() => {
            if (window.TranslationEnhanced) {
                window.TranslationEnhanced.translateAll();
            }
        }, 500);
    }, 1000);
    
    // Observer untuk menerjemahkan elemen yang baru ditambahkan
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const elementsToTranslate = node.querySelectorAll ? 
                            node.querySelectorAll('[data-translate-arabic]') : [];
                        
                        if (elementsToTranslate.length > 0 && window.TranslationEnhanced) {
                            setTimeout(() => {
                                window.TranslationEnhanced.translateAll();
                            }, 100);
                        }
                    }
                });
            }
        });
    });
    
    // Observasi perubahan DOM
    observer.observe(document.body, { childList: true, subtree: true });
});

// Export untuk penggunaan global
window.TranslationAPI = window.TranslationEnhanced;

// Debug functions untuk testing
window.debugDeepSeek = {
    testAPI: () => {
        console.log("🧪 Testing DeepSeek API directly...");
        if (window.TranslationEnhanced && window.TranslationEnhanced.testAPI) {
            return window.TranslationEnhanced.testAPI();
        } else {
            console.error("TranslationEnhanced not found or testAPI method missing");
            return Promise.reject("TranslationEnhanced not available");
        }
    },
    
    testTranslation: (text = "السلام عليكم") => {
        console.log(`🔤 Testing translation for: ${text}`);
        if (window.TranslationEnhanced && window.TranslationEnhanced.callTranslationAPI) {
            return window.TranslationEnhanced.callTranslationAPI(text);
        } else {
            console.error("TranslationEnhanced not found or callTranslationAPI method missing");
            return Promise.reject("TranslationEnhanced not available");
        }
    },
    
    checkElements: () => {
        const elements = document.querySelectorAll('[data-translate-arabic]');
        console.log(`Found ${elements.length} elements with data-translate-arabic:`);
        elements.forEach((el, i) => {
            console.log(`${i + 1}.`, {
                id: el.id,
                text: el.getAttribute('data-translate-arabic'),
                element: el
            });
        });
        return elements;
    },
    
    forceTranslate: () => {
        if (window.TranslationEnhanced && window.TranslationEnhanced.forceRetranslate) {
            window.TranslationEnhanced.forceRetranslate();
        } else {
            console.error("TranslationEnhanced not found or forceRetranslate method missing");
        }
    },
    
    clearCache: () => {
        if (window.TranslationEnhanced && window.TranslationEnhanced.clearCache) {
            window.TranslationEnhanced.clearCache();
        } else {
            console.error("TranslationEnhanced not found or clearCache method missing");
        }
    },
    
    checkStatus: () => {
        console.log("🔍 Checking TranslationEnhanced status:");
        console.log("window.TranslationEnhanced:", window.TranslationEnhanced);
        console.log("window.TranslationAPI:", window.TranslationAPI);
        
        if (window.TranslationEnhanced) {
            console.log("Available methods:", Object.keys(window.TranslationEnhanced));
        }
        
        return {
            TranslationEnhanced: !!window.TranslationEnhanced,
            TranslationAPI: !!window.TranslationAPI,
            methods: window.TranslationEnhanced ? Object.keys(window.TranslationEnhanced) : []
        };
    }
};

console.log('🔧 Debug tools loaded. Use window.debugDeepSeek for testing.');
console.log('Available commands:');
console.log('- testAPI()');
console.log('- testTranslation(text)');
console.log('- checkElements()');
console.log('- forceTranslate()');
console.log('- clearCache()');
console.log('- checkStatus()');
</script>

</x-layout>