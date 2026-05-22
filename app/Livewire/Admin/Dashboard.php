<?php

namespace App\Livewire\Admin;

use App\Services\AdminStatsService;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Admin dashboard summary panel.
 *
 * Replaces the controller-driven {@see \App\Http\Controllers\AdminController::dashboard}.
 * Stats are loaded lazily via computed properties so a `wire:click="refresh"`
 * round-trip simply busts the local cache and re-renders.
 */
class Dashboard extends Component
{
    public ?string $statusMessage = null;

    public ?string $errorMessage = null;

    /**
     * @return array{total_users: int, admin_users: int, users_today: int, users_this_week: int, total_searches: int, searches_today: int, searches_this_week: int, active_users_today: int}
     */
    #[Computed(persist: false)]
    public function stats(): array
    {
        try {
            return app(AdminStatsService::class)->dashboardStats();
        } catch (\Throwable) {
            return app(AdminStatsService::class)->defaultStats();
        }
    }

    #[Computed(persist: false)]
    public function recentUsers()
    {
        return app(AdminStatsService::class)->recentUsers();
    }

    #[Computed(persist: false)]
    public function recentSearches()
    {
        return app(AdminStatsService::class)->recentSearches();
    }

    /**
     * @return array{database_size: string, cache_status: string, queue_status: string, storage_usage: string}
     */
    #[Computed(persist: false)]
    public function systemHealth(): array
    {
        try {
            return app(AdminStatsService::class)->systemHealth();
        } catch (\Throwable) {
            return app(AdminStatsService::class)->defaultSystemHealth();
        }
    }

    public function refresh(): void
    {
        unset($this->stats, $this->recentUsers, $this->recentSearches, $this->systemHealth);
        $this->statusMessage = 'Statistik diperbarui.';
        $this->errorMessage = null;
    }

    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            $this->statusMessage = 'Cache cleared successfully!';
            $this->errorMessage = null;
            $this->refresh();
        } catch (\Throwable $e) {
            $this->errorMessage = 'Error clearing cache: ' . $e->getMessage();
            $this->statusMessage = null;
        }
    }

    public function optimize(): void
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            $this->statusMessage = 'Application optimized successfully!';
            $this->errorMessage = null;
        } catch (\Throwable $e) {
            $this->errorMessage = 'Error optimizing application: ' . $e->getMessage();
            $this->statusMessage = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
