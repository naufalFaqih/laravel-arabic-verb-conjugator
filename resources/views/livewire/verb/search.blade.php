<div>
    {{-- Header dengan Auth Status --}}
    <div class="mb-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            @auth
                <div class="flex flex-col sm:flex-row items-center justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}!</h2>
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
                        <h2 class="text-xl font-bold text-gray-800">Welcome To Tashrif Arabic Verbs</h2>
                        <p class="text-sm text-gray-600">Please login to access full feature.</p>
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 inline-block">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 inline-block">Register</a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    {{-- Flash session messages --}}
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

    {{-- Inline error from component --}}
    @if($errorMessage)
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p>{{ $errorMessage }}</p>
        </div>
    @endif

    {{-- Search form --}}
    <form wire:submit.prevent="search" class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md" data-purpose="search-verb">
        <label for="verb" class="block text-sm font-bold text-gray-700 text-center mb-2">Input Verb (Fiil):</label>
        <input
            type="text"
            id="verb"
            name="verb"
            wire:model="verb"
            class="block w-3/4 md:w-1/2 lg:w-1/3 mx-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-md p-2 text-right font-bold arabic-input"
            placeholder="اشتغل, سَلّمَ, لعب :Contoh"
            pattern="^[\u0600-\u06FF\s]+$"
            title="Hanya diperbolehkan karakter dalam bahasa Arab"
            required
        />
        <button
            type="submit"
            id="searchButton"
            class="mt-4 block mx-auto px-6 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Tashrif
        </button>
    </form>

    {{-- Loading --}}
    <div wire:loading wire:target="search" class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md text-center">
        <div class="spinner mx-auto"></div>
        <p class="text-lg font-medium text-gray-700">Process Requests...</p>
    </div>

    @if($hasResult)
        {{-- Summary --}}
        <div class="mt-6 p-4 bg-white rounded-lg shadow-md" wire:loading.remove wire:target="search">
            <h3 class="text-lg text-gray-700 font-bold mb-3 text-right">:ملخص البحث / Search Summary</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
                                الماضي<br/><span class="text-xs font-normal">Madhi (Past)</span>
                            </th>
                            <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
                                المضارع<br/><span class="text-xs font-normal">Mudhori (Present/Future)</span>
                            </th>
                            <th class="px-4 py-2 bg-gray-50 text-gray-700 text-center border border-gray-200">
                                الأمر<br/><span class="text-xs font-normal">Amar (Command)</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 text-center border border-gray-200">
                                <div class="text-lg font-bold arabic-text" data-translate-arabic="{{ $summary['madhi'] }}">{{ $summary['madhi'] }}</div>
                                <div class="translation-text text-xs mt-2 text-gray-600"></div>
                            </td>
                            <td class="px-4 py-2 text-center border border-gray-200">
                                <div class="text-lg font-bold arabic-text" data-translate-arabic="{{ $summary['mudhori'] }}">{{ $summary['mudhori'] }}</div>
                                <div class="translation-text text-xs mt-2 text-gray-600"></div>
                            </td>
                            <td class="px-4 py-2 text-center border border-gray-200">
                                <div class="text-lg font-bold arabic-text" data-translate-arabic="{{ $summary['amar'] }}">{{ $summary['amar'] }}</div>
                                <div class="translation-text text-xs mt-2 text-gray-600"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Verb info & suggestions --}}
        <div class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md" wire:loading.remove wire:target="search">
            <h3 class="text-md text-gray-700 text-right">
                <span class="arabic-text" data-translate-arabic="Verb Information">:Verb Information</span>
                <div class="translation-text text-xs mt-1"></div>
            </h3>
            <div class="mt-2">
                <div class="text-lg text-gray-600 text-right font-bold arabic-text" data-translate-arabic="{{ $verbInfo }}">{{ $verbInfo }}</div>
                <div class="translation-text text-xs mt-2 text-gray-600"></div>
            </div>
            @if(! empty($suggest))
                <h3 class="text-md text-gray-700 text-right mt-6">
                    <span class="arabic-text" data-translate-arabic="Also Found in Chapter">:Also Found in Chapter</span>
                    <div class="translation-text text-xs mt-1"></div>
                </h3>
                <ul class="mt-4 mb-2 text-lg text-gray-600 text-right font-bold">
                    @foreach($suggest as $item)
                        <li>
                            <span style="margin-right:20px">{{ $item['verb'] }}</span>
                            <span>- {{ $item['future'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Result columns --}}
        <div class="mt-6 p-4 bg-white rounded-lg shadow-md" wire:loading.remove wire:target="search">
            <h3 class="text-lg text-gray-700 text-right font-bold mb-3">Tashrif Lughowi / تصرف لغوي:</h3>

            <div class="overflow-x-auto pb-2" id="mainScrollContainer">
                <div class="min-w-max">
                    {{-- Column headers --}}
                    <div class="pb-3 mb-6">
                        <div class="grid grid-cols-8 gap-4 text-center font-bold break-words min-w-max">
                            @foreach($columns as $heading)
                                <div class="p-2 bg-blue-50 rounded-lg shadow-sm break-words font-bold text-center text-md text-blue-800" data-translate-arabic="{{ $heading }}">{{ $heading }}</div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Conjugation cells (8 columns each w-64) --}}
                    <div class="flex flex-row gap-4 mt-6 text-center font-bold min-w-max">
                        @foreach(['amarMuakkad','amar','mudhoriMuakkad','mudhoriMansub','mudhoriMajzum','mudhoriMalum','madhiMalum','domir'] as $cellKey)
                            <div class="p-4 bg-gray-100 rounded-lg shadow-md w-64 flex-shrink-0">
                                @foreach($cells[$cellKey] ?? [] as $value)
                                    <div class="mb-2 text-right" data-translate-arabic="{{ $value }}">{{ $value }}</div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Mobile scroll hint --}}
            <div class="flex justify-center mt-4 md:hidden">
                <div class="flex items-center text-xs text-gray-500 scroll-indicator">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    <span>Swipe to see more</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </div>
        </div>
    @endif

    @auth
        <livewire:verb.recent-history />
    @endauth

    {{-- Translation enhancement: trigger TranslationEnhanced after Livewire renders new Arabic content --}}
    @push('scripts')
    @verbatim
    <script>
        (function () {
            // Run the translation pass once after the initial render and again
            // every time Livewire dispatches `verb-result-ready`.
            const triggerTranslate = () => {
                if (window.TranslationEnhanced && typeof window.TranslationEnhanced.translateAll === 'function') {
                    window.TranslationEnhanced.translateAll();
                }
            };

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('verb-result-ready', () => setTimeout(triggerTranslate, 100));
            });

            // Initial pass for any Arabic strings already on the page (suggest items, etc.)
            document.addEventListener('DOMContentLoaded', () => setTimeout(triggerTranslate, 600));
        })();
    </script>
    @endverbatim
    @endpush
</div>
