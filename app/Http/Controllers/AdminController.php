<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use App\Models\User;
use App\Services\AdminStatsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Admin panel controller.
 *
 * The dashboard action delegates statistics gathering to
 * {@see AdminStatsService}. The remaining actions (users, userDetail,
 * monitoring, toggleAdmin, clearCache, optimize) are out of scope for
 * the Livewire refactor and keep their original behaviour.
 */
class AdminController extends Controller
{
    public function __construct(private readonly AdminStatsService $stats)
    {
    }

    public function dashboard(): View
    {
        try {
            return view('admin.dashboard', [
                'stats' => $this->stats->dashboardStats(),
                'recentUsers' => $this->stats->recentUsers(),
                'recentSearches' => $this->stats->recentSearches(),
                'systemHealth' => $this->stats->systemHealth(),
            ]);
        } catch (\Throwable $e) {
            return view('admin.dashboard', [
                'stats' => $this->stats->defaultStats(),
                'recentUsers' => new Collection(),
                'recentSearches' => new Collection(),
                'systemHealth' => $this->stats->defaultSystemHealth(),
            ]);
        }
    }

    public function users(Request $request): View
    {
        try {
            $query = User::query();

            if ($request->filled('search')) {
                $search = (string) $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('admin_filter')) {
                $query->where('is_admin', $request->input('admin_filter') === 'admin');
            }

            $users = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->appends($request->query());

            return view('admin.users', compact('users'));
        } catch (\Throwable $e) {
            return view('admin.users', ['users' => User::paginate(15)]);
        }
    }

    public function userDetail(int $id): View|RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            return view('admin.user-detail', [
                'user' => $user,
                'userStats' => $this->getUserStats($user),
                'telescopeEntries' => new Collection(),
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'User tidak ditemukan.');
        }
    }

    public function monitoring(): View
    {
        try {
            return view('admin.monitoring', [
                'systemMetrics' => $this->getSystemMetrics(),
                'systemHealth' => $this->stats->systemHealth(),
                'apiUsage' => $this->getApiUsageStats(),
                'errorLogs' => $this->getRecentErrors(),
            ]);
        } catch (\Throwable $e) {
            return view('admin.monitoring', [
                'systemMetrics' => $this->getDefaultSystemMetrics(),
                'systemHealth' => $this->stats->defaultSystemHealth(),
                'apiUsage' => new Collection(),
                'errorLogs' => new Collection(),
            ]);
        }
    }

    public function toggleAdmin(Request $request, int $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            if (Auth::id() === $user->id && $user->is_admin) {
                return back()->with('error', 'Tidak dapat menghapus status admin dari akun sendiri.');
            }

            $user->update(['is_admin' => ! $user->is_admin]);

            $status = $user->is_admin ? 'ditambahkan ke' : 'dihapus dari';

            return back()->with('success', "User {$user->name} berhasil {$status} admin.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengupdate status admin.');
        }
    }

    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json(['message' => 'Cache cleared successfully!']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error clearing cache: ' . $e->getMessage()], 500);
        }
    }

    public function optimize(): JsonResponse
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return response()->json(['message' => 'Application optimized successfully!']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error optimizing application: ' . $e->getMessage()], 500);
        }
    }

    // ----------------------------------------------------------------------
    // Helpers retained for monitoring / userDetail (out of refactor scope).
    // ----------------------------------------------------------------------

    private function getUserStats(User $user): array
    {
        try {
            if (method_exists($user, 'searchHistories') && Schema::hasTable('search_histories')) {
                return [
                    'total_searches' => $user->searchHistories()->count(),
                    'searches_today' => $user->searchHistories()->whereDate('created_at', today())->count(),
                    'searches_this_week' => $user->searchHistories()->whereBetween('created_at', [
                        now()->startOfWeek(), now()->endOfWeek(),
                    ])->count(),
                    'last_activity' => $user->searchHistories()->latest()->first()?->created_at,
                ];
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return [
            'total_searches' => 0,
            'searches_today' => 0,
            'searches_this_week' => 0,
            'last_activity' => null,
        ];
    }

    private function getSystemMetrics(): array
    {
        try {
            return [
                'memory_usage' => $this->formatBytes(memory_get_usage(true)),
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_time' => now()->format('Y-m-d H:i:s'),
                'timezone' => config('app.timezone'),
            ];
        } catch (\Throwable $e) {
            return $this->getDefaultSystemMetrics();
        }
    }

    private function getApiUsageStats()
    {
        try {
            if (! Schema::hasTable('telescope_entries')) {
                return new Collection();
            }

            return DB::table('telescope_entries')
                ->where('type', 'request')
                ->where('content', 'like', '%/api/%')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        } catch (\Throwable $e) {
            return new Collection();
        }
    }

    private function getRecentErrors()
    {
        try {
            if (! Schema::hasTable('telescope_entries')) {
                return new Collection();
            }

            return DB::table('telescope_entries')
                ->where('type', 'exception')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($entry) {
                    $entry->content = json_decode($entry->content, true);

                    return $entry;
                });
        } catch (\Throwable $e) {
            return new Collection();
        }
    }

    private function getDefaultSystemMetrics(): array
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

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if (! is_numeric($bytes) || $bytes < 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $i = 0;
        for (; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
