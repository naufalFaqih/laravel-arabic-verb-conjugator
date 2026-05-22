<?php

namespace App\Services;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aggregates statistics & system health metrics displayed on the admin
 * dashboard. Helpers are pulled from {@see \App\Http\Controllers\AdminController}
 * with behaviour preserved.
 *
 * NOTE: {@see databaseSize()} still queries MySQL `information_schema`.
 * If the project is later switched fully to SQLite this method should be
 * adapted to PRAGMA / file size instead. Documented for follow-up.
 */
class AdminStatsService
{
    /**
     * @return array{total_users: int, admin_users: int, users_today: int, users_this_week: int, total_searches: int, searches_today: int, searches_this_week: int, active_users_today: int}
     */
    public function dashboardStats(): array
    {
        try {
            return [
                'total_users' => User::count(),
                'admin_users' => User::where('is_admin', true)->count(),
                'users_today' => User::whereDate('created_at', today())->count(),
                'users_this_week' => User::whereBetween('created_at', [
                    now()->startOfWeek(), now()->endOfWeek(),
                ])->count(),
                'total_searches' => 0,
                'searches_today' => 0,
                'searches_this_week' => 0,
                'active_users_today' => 0,
            ];
        } catch (\Throwable $e) {
            return $this->defaultStats();
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function recentUsers(int $limit = 10): Collection
    {
        try {
            return User::latest()->take($limit)->get();
        } catch (\Throwable $e) {
            return new Collection();
        }
    }

    /**
     * @return Collection<int, SearchHistory>
     */
    public function recentSearches(int $limit = 15): Collection
    {
        try {
            if (Schema::hasTable('search_histories')) {
                return SearchHistory::with('user')->latest()->take($limit)->get();
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return new Collection();
    }

    /**
     * @return array{database_size: string, cache_status: string, queue_status: string, storage_usage: string}
     */
    public function systemHealth(): array
    {
        return [
            'database_size' => $this->databaseSize(),
            'cache_status' => $this->cacheStatus(),
            'queue_status' => $this->queueStatus(),
            'storage_usage' => $this->storageUsage(),
        ];
    }

    /**
     * @return array{total_users: int, admin_users: int, users_today: int, users_this_week: int, total_searches: int, searches_today: int, searches_this_week: int, active_users_today: int}
     */
    public function defaultStats(): array
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

    /**
     * @return array{database_size: string, cache_status: string, queue_status: string, storage_usage: string}
     */
    public function defaultSystemHealth(): array
    {
        return [
            'database_size' => 'N/A',
            'cache_status' => 'Error',
            'queue_status' => 'N/A',
            'storage_usage' => 'N/A',
        ];
    }

    // ----------------------------------------------------------------------
    // Health-check helpers (preserved from AdminController).
    // ----------------------------------------------------------------------

    private function databaseSize(): string
    {
        try {
            $dbName = config('database.connections.mysql.database');
            if (! $dbName) {
                return 'N/A';
            }

            $size = DB::select(
                "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS size FROM information_schema.tables WHERE table_schema = ?",
                [$dbName]
            );

            return ($size[0]->size ?? 0) . ' MB';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }

    private function cacheStatus(): string
    {
        try {
            Cache::put('health_check', 'ok', 60);

            return Cache::get('health_check') === 'ok' ? 'OK' : 'Error';
        } catch (\Throwable $e) {
            return 'Error';
        }
    }

    private function queueStatus(): string
    {
        try {
            if (! Schema::hasTable('failed_jobs')) {
                return 'N/A';
            }

            $failed = DB::table('failed_jobs')->count();

            return $failed > 0 ? "Failed: {$failed}" : 'OK';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }

    private function storageUsage(): string
    {
        try {
            $bytes = disk_free_space(storage_path());
            if ($bytes === false) {
                return 'N/A';
            }

            return round($bytes / 1024 / 1024 / 1024, 2) . ' GB free';
        } catch (\Throwable $e) {
            return 'N/A';
        }
    }
}
