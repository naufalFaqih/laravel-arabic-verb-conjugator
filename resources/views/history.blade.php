<!-- filepath: c:\laragon\www\latihanLaravel11\resources\views\history.blade.php -->
@vite('resources/css/app.css')
<x-layout>
  <x-slot:title>Riwayat Pencarian - Tashrif Arab</x-slot:title>

  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Riwayat Pencarian
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
          Daftar kata-kata yang telah Anda cari sebelumnya.
        </p>
      </div>
      
      @if(count($histories) > 0)
        <form action="{{ route('history.destroy.all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua riwayat pencarian?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1 text-xs text-red-600 hover:text-red-800 border border-red-300 rounded hover:bg-red-50">
            Hapus Semua
          </button>
        </form>
      @endif
    </div>

    <div class="border-t border-gray-200">
      <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
        <dt class="text-sm font-medium text-gray-500">
          Login terakhir
        </dt>
        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
          {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d M Y, H:i') : 'Tidak tersedia' }}
        </dd>
      </div>
    </div>
    
    <div class="p-4">
      @if(count($histories) > 0)
        <div class="divide-y divide-gray-200">
          @foreach($histories as $history)
            <div class="py-3 flex justify-between items-center">
              <div class="flex-1">
                <div class="flex items-center space-x-3">
                  <span class="text-gray-800 font-medium">{{ $loop->iteration }}.</span>
                  <span class="text-right text-gray-800 font-medium">{!! $history->query !!}</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ $history->created_at->format('d M Y, H:i') }}
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <a href="/?query={{ urlencode($history->query) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                  Cari lagi
                </a>
                <form action="{{ route('history.destroy', $history->id) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                    Hapus
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-8 text-gray-500">
          <p>Belum ada riwayat pencarian.</p>
        </div>
      @endif
    </div>
  </div>
</x-layout>