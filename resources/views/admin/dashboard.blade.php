<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600">Monitor aplikasi dan aktivitas pengguna</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kelola User
                </a>
                <a href="{{ route('admin.monitoring') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Monitoring
                </a>
                <a href="/telescope" target="_blank" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600">
                    Telescope
                </a>
            </nav>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['users_today'] }} hari ini</p>
                    </div>
                </div>
            </div>

            <!-- Admin Users -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Admin Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['admin_users']) }}</p>
                        <p class="text-xs text-gray-500">dari {{ $stats['total_users'] }} users</p>
                    </div>
                </div>
            </div>

            <!-- Total Searches -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pencarian</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_searches']) }}</p>
                        <p class="text-xs text-gray-500">{{ $stats['searches_today'] }} hari ini</p>
                    </div>
                </div>
            </div>

            <!-- Active Users Today -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">User Aktif Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users_today']) }}</p>
                        <p class="text-xs text-gray-500">melakukan pencarian</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">User Terbaru</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentUsers as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Admin
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $user->search_histories_count }} pencarian</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Belum ada user terdaftar</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Lihat semua user →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Searches -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pencarian Terbaru</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentSearches as $search)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 text-right">{{ $search->query }}</p>
                                    <p class="text-xs text-gray-500">
                                        oleh {{ $search->user->name ?? 'Guest' }} • {{ $search->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Belum ada pencarian</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">System Health</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Database</p>
                        <p class="text-lg font-medium">{{ $systemHealth['database_size'] ?? 'N/A' }} MB</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Cache</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemHealth['cache_status'] === 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $systemHealth['cache_status'] }}
                        </span>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Queue</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ str_contains($systemHealth['queue_status'], 'OK') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $systemHealth['queue_status'] }}
                        </span>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Storage</p>
                        <p class="text-lg font-medium">{{ $systemHealth['storage_usage'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>