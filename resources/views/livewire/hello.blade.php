<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Livewire 3 Smoke Test</h1>

    <p class="text-sm text-gray-600 mb-6">
        Halaman ini hanya untuk verifikasi instalasi Livewire 3.
        Akan dihapus di Task 9.
    </p>

    <div class="flex items-center justify-center gap-4 mb-6">
        <button type="button"
                wire:click="decrement"
                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
            -
        </button>

        <span class="text-3xl font-bold text-indigo-600 w-16 text-center" wire:loading.remove>
            {{ $count }}
        </span>
        <span class="text-3xl font-bold text-gray-300 w-16 text-center" wire:loading>
            …
        </span>

        <button type="button"
                wire:click="increment"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
            +
        </button>
    </div>

    <div class="flex justify-center">
        <button type="button"
                wire:click="resetCount"
                class="px-3 py-1 text-xs text-gray-500 border border-gray-300 rounded hover:bg-gray-50">
            Reset
        </button>
    </div>

    {{-- Alpine smoke test: verifikasi bundled Alpine berfungsi --}}
    <div class="mt-6 pt-6 border-t border-gray-200" x-data="{ open: false }">
        <button type="button"
                @click="open = !open"
                class="text-sm text-indigo-600 hover:text-indigo-800">
            <span x-text="open ? 'Sembunyikan' : 'Tampilkan'"></span> Alpine info
        </button>
        <div x-show="open" x-transition class="mt-2 p-3 bg-gray-50 rounded text-xs text-gray-600">
            Alpine.js juga ter-bundle bersama Livewire 3 dan berjalan baik.
        </div>
    </div>
</div>
