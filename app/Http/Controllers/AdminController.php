<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{

    /**
 * Clear application cache
 */
public function clearCache()
{
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        return response()->json(['message' => 'Cache cleared successfully!']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error clearing cache: ' . $e->getMessage()], 500);
    }
}

/**
 * Optimize application
 */
public function optimize()
{
    try {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        return response()->json(['message' => 'Application optimized successfully!']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error optimizing application: ' . $e->getMessage()], 500);
    }
}
    public function __construct()
    {
        //$this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    /**
     * Admin dashboard overview
     */
     public function dashboard()
    {
        try {
            $stats = $this->getDashboardStats();
            $recentUsers = $this->getRecentUsers();
            $recentSearches = $this->getRecentSearches();
            $systemHealth = $this->getSystemHealth();

            return view('admin.dashboard', compact(
                'stats',
                'recentUsers', 
                'recentSearches',
                'systemHealth'
            ));
        } catch (\Exception $e) {
            return view('admin.dashboard', [
                'stats' => $this->getDefaultStats(),
                'recentUsers' => collect(),
                'recentSearches' => collect(),
                'systemHealth' => $this->getDefaultSystemHealth()
            ]);
        }
    }

    /**
     * User management
     */
    public function users(Request $request)
    {
        try {
            $query = User::query();

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Admin filter
            if ($request->filled('admin_filter')) {
                $query->where('is_admin', $request->admin_filter === 'admin');
            }

            $users = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->appends($request->query());

            return view('admin.users', compact('users'));
        } catch (\Exception $e) {
            return view('admin.users', ['users' => User::paginate(15)]);
        }
    }

    /**
     * User detail with activity
     */
  public function userDetail($id)
    {
        try {
            $user = User::findOrFail($id);
            $userStats = $this->getUserStats($user);
            $telescopeEntries = collect(); // Simplified for now

            return view('admin.user-detail', compact('user', 'userStats', 'telescopeEntries'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'User tidak ditemukan.');
        }
    }

    /**
     * System monitoring
     */
 public function monitoring()
{
    try {
        $systemMetrics = $this->getSystemMetrics();
        $systemHealth = $this->getSystemHealth();
        $apiUsage = $this->getApiUsageStats();
        $errorLogs = $this->getRecentErrors();
        
        return view('admin.monitoring', compact('systemMetrics', 'systemHealth', 'apiUsage', 'errorLogs'));
    } catch (\Exception $e) {
        return view('admin.monitoring', [
            'systemMetrics' => $this->getDefaultSystemMetrics(),
            'systemHealth' => $this->getDefaultSystemHealth(),
            'apiUsage' => collect(),
            'errorLogs' => collect()
        ]);
    }
}

    /**
     * Toggle user admin status
     */
 public function toggleAdmin(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent removing admin from self - menggunakan Auth facade
            if (Auth::id() === $user->id && $user->is_admin) {
                return back()->with('error', 'Tidak dapat menghapus status admin dari akun sendiri.');
            }

            $user->update(['is_admin' => !$user->is_admin]);

            $status = $user->is_admin ? 'ditambahkan ke' : 'dihapus dari';
            
            return back()->with('success', "User {$user->name} berhasil {$status} admin.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengupdate status admin.');
        }
    }

    /**
     * Get dashboard statistics
     */private function getDashboardStats()
    {
        try {
            return [
                'total_users' => User::count(),
                'admin_users' => User::where('is_admin', true)->count(),
                'users_today' => User::whereDate('created_at', today())->count(),
                'users_this_week' => User::whereBetween('created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ])->count(),
                'total_searches' => 0,
                'searches_today' => 0,
                'searches_this_week' => 0,
                'active_users_today' => 0,
            ];
        } catch (\Exception $e) {
            return $this->getDefaultStats();
        }
    }

    /**
     * Get recent users
     */
 private function getRecentUsers()
    {
        try {
            return User::latest()->take(10)->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recent searches
     */
      private function getRecentSearches()
    {
        try {
            if (class_exists(SearchHistory::class) && Schema::hasTable('search_histories')) {
                return SearchHistory::with('user')->latest()->take(15)->get();
            }
            return collect();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth()
    {
        return [
            'database_size' => $this->getDatabaseSize(),
            'cache_status' => $this->getCacheStatus(),
            'queue_status' => $this->getQueueStatus(),
            'storage_usage' => $this->getStorageUsage(),
        ];
    }

    /**
     * Get user activity from Telescope
     */
    private function getUserStats($user)
    {
        try {
            if (method_exists($user, 'searchHistories') && Schema::hasTable('search_histories')) {
                return [
                    'total_searches' => $user->searchHistories()->count(),
                    'searches_today' => $user->searchHistories()->whereDate('created_at', today())->count(),
                    'searches_this_week' => $user->searchHistories()->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek()
                    ])->count(),
                    'last_activity' => $user->searchHistories()->latest()->first()?->created_at,
                ];
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        return [
            'total_searches' => 0,
            'searches_today' => 0,
            'searches_this_week' => 0,
            'last_activity' => null,
        ];
    }

    /**
     * Get system metrics
     */
    private function getSystemMetrics()
    {
        try{
        return [
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
        ];
        } catch (\Exception $e) {
        return $this->getDefaultSystemMetrics();
    }
    }

    /**
     * Get API usage statistics
     */
    private function getApiUsageStats()
    {
        try {
            if (!Schema::hasTable('telescope_entries')) {
                return collect();
            }

            return DB::table('telescope_entries')
                ->where('type', 'request')
                ->where('content', 'like', '%/api/%')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get recent errors
     */
    private function getRecentErrors()
    {
        try {
            if (!Schema::hasTable('telescope_entries')) {
                return collect();
            }

            return DB::table('telescope_entries')
                ->where('type', 'exception')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function($entry) {
                    $entry->content = json_decode($entry->content, true);
                    return $entry;
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    // Helper methods for system health
    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            if (!$dbName) {
                return 'N/A';
            }
            
            $size = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'size' FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            return ($size[0]->size ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getCacheStatus()
    {
        try {
            cache()->put('health_check', 'ok', 60);
            return cache()->get('health_check') === 'ok' ? 'OK' : 'Error';
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    private function getQueueStatus()
    {
        try {
            if (!Schema::hasTable('failed_jobs')) {
                return 'N/A';
            }
            $failed = DB::table('failed_jobs')->count();
            return $failed > 0 ? "Failed: {$failed}" : 'OK';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getStorageUsage()
    {
        try {
            $bytes = disk_free_space(storage_path());
            if ($bytes === false) {
                return 'N/A';
            }
            return round($bytes / 1024 / 1024 / 1024, 2) . ' GB free';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Format bytes to human readable format
     */
   private function formatBytes($bytes, $precision = 2)
{
    if (!is_numeric($bytes) || $bytes < 0) {
        return '0 B';
    }

    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Default system health when error occurs
 */
private function getDefaultSystemHealth()
{
    return [
        'database_size' => 'N/A',
        'cache_status' => 'Error',
        'queue_status' => 'N/A',
        'storage_usage' => 'N/A',
    ];
}

/**
 * Default system metrics when error occurs
 */
private function getDefaultSystemMetrics()
{
    return [
        'memory_usage' => 'N/A',
        'memory_peak' => 'N/A',
        'php_version' => PHP_VERSION,
        'laravel_version' => 'N/A',
        'server_time' => now()->format('Y-m-d H:i:s'),
        'timezone' => 'UTC',
    ];
}
 private function getDefaultStats()
    {
        return [
            'total_users' => 0,
            'admin_users' => 0,
            'users_today' => 0,
            'users_this_week' => 0,
            'total_searches' => 0,
            'searches_today' => 0,
            'searches_this_week' => 0,
            'active_users_today' => 0,
        ];
    }

}