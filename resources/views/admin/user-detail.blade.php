<x-layout>
    <x-slot:title>Detail User: {{ $user->name }} - Admin Dashboard</x-slot:title>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail User: {{ $user->name }}</h1>
                    <p class="text-gray-600">Informasi lengkap dan aktivitas user</p>
                </div>
                <a href="{{ route('admin.users') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    ← Kembali ke Kelola User
                </a>
            </div>
        </div>

        <!-- User Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-xl font-medium text-gray-700">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                            <p class="text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Status:</dt>
                                <dd>
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            User
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Bergabung:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Terakhir update:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->updated_at->diffForHumans() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Activity Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Aktivitas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Pencarian:</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $userStats['total_searches'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pencarian Hari Ini:</span>
                        <span class="text-lg font-semibold text-green-600">{{ $userStats['searches_today'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pencarian Minggu Ini:</span>
                        <span class="text-lg font-semibold text-purple-600">{{ $userStats['searches_this_week'] }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Aktivitas Terakhir:</span>
                            <span class="text-sm text-gray-900">
                                @if($userStats['last_activity'])
                                    {{ $userStats['last_activity']->diffForHumans() }}
                                @else
                                    Belum ada aktivitas
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('admin.user.toggle-admin', $user->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin mengubah status admin user ini?')"
                                    class="w-full bg-{{ $user->is_admin ? 'red' : 'green' }}-600 text-white px-4 py-2 rounded-md hover:bg-{{ $user->is_admin ? 'red' : 'green' }}-700">
                                {{ $user->is_admin ? 'Hapus Status Admin' : 'Jadikan Admin' }}
                            </button>
                        </form>
                    @else
                        <div class="w-full bg-gray-100 text-gray-500 px-4 py-2 rounded-md text-center">
                            Ini adalah akun Anda sendiri
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Search History -->
        @if(isset($user->searchHistories) && $user->searchHistories->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Riwayat Pencarian Terbaru</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($user->searchHistories as $search)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $search->query }}</p>
                                <p class="text-xs text-gray-500">{{ $search->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $search->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</x-layout>