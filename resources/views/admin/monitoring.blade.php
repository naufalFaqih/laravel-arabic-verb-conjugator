<x-layout>
    <x-slot:title>System Monitoring - Admin Dashboard</x-slot:title>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">System Monitoring</h1>
            <p class="text-gray-600">Monitor kesehatan dan performa sistem</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Kelola User
                </a>
                <a href="{{ route('admin.monitoring') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    Monitoring
                </a>
                <a href="/telescope" target="_blank" class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600">
                    Telescope
                </a>
            </nav>
        </div>

        <!-- System Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Server Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Server Information</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">PHP Version:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $systemMetrics['php_version'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Laravel Version:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $systemMetrics['laravel_version'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Server Time:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $systemMetrics['server_time'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Timezone:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $systemMetrics['timezone'] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Memory Usage -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Memory Usage</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Current Usage:</dt>
                        <dd class="text-sm font-medium text-blue-600">{{ $systemMetrics['memory_usage'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Peak Usage:</dt>
                        <dd class="text-sm font-medium text-red-600">{{ $systemMetrics['memory_peak'] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <button onclick="clearCache()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                        Clear Cache
                    </button>
                    <button onclick="optimizeApp()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                        Optimize App
                    </button>
                    <a href="/telescope" target="_blank" class="block w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm text-center">
                        Open Telescope
                    </a>
                </div>
            </div>
        </div>

        <!-- System Health Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Database -->
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900">Database</h4>
                <p class="text-sm text-gray-600">{{ $systemHealth['database_size'] ?? 'N/A' }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                    Connected
                </span>
            </div>

            <!-- Cache -->
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900">Cache</h4>
                <p class="text-sm text-gray-600">Redis/File</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemHealth['cache_status'] === 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mt-2">
                    {{ $systemHealth['cache_status'] }}
                </span>
            </div>

            <!-- Queue -->
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900">Queue</h4>
                <p class="text-sm text-gray-600">Job Processing</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ str_contains($systemHealth['queue_status'], 'OK') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} mt-2">
                    {{ $systemHealth['queue_status'] }}
                </span>
            </div>

            <!-- Storage -->
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="w-12 h-12 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900">Storage</h4>
                <p class="text-sm text-gray-600">{{ $systemHealth['storage_usage'] }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                    Available
                </span>
            </div>
        </div>

        <!-- Recent Errors -->
        @if($errorLogs->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Errors</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($errorLogs as $error)
                        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ $error->content['class'] ?? 'Unknown Error' }}
                                    </p>
                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $error->content['message'] ?? 'No message available' }}
                                    </p>
                                    <p class="text-xs text-red-500 mt-2">
                                        {{ $error->created_at }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- API Usage (if available) -->
        @if($apiUsage->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">API Usage (Last 7 Days)</h3>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($apiUsage as $usage)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $usage->date }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $usage->count }} requests</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        function clearCache() {
            if (confirm('Are you sure you want to clear all cache?')) {
                fetch('/admin/clear-cache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Cache cleared successfully!');
                    location.reload();
                })
                .catch(error => {
                    alert('Error clearing cache');
                });
            }
        }

        function optimizeApp() {
            if (confirm('Are you sure you want to optimize the application?')) {
                fetch('/admin/optimize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Application optimized successfully!');
                    location.reload();
                })
                .catch(error => {
                    alert('Error optimizing application');
                });
            }
        }
    </script>
</x-layout>