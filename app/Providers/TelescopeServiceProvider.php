<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Telescope if enabled
        if (!$this->app->environment('production') || env('TELESCOPE_ENABLED', false)) {
            parent::register();
        }

        // Configure Telescope
        $this->hideSensitiveRequestDetails();
        $this->configureFilter();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Register the gate
        $this->gate();
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
        ]);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'authorization',
        ]);
    }

    /**
     * Register the Telescope gate.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user = null) {
            // Allow access in local environment
            if ($this->app->environment('local')) {
                return true;
            }

            // In production, only allow admin users
            return $user && isset($user->is_admin) && $user->is_admin === true;
        });
    }

    /**
     * Configure the Telescope filter.
     */
    protected function configureFilter(): void
    {
        Telescope::filter(function (IncomingEntry $entry) {
            // Always record in local environment
            if ($this->app->environment('local')) {
                return true;
            }

            // In production, only record important entries
            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag() ||
                   $this->shouldRecordEntry($entry);
        });
    }

    /**
     * Determine if the entry should be recorded.
     */
    protected function shouldRecordEntry(IncomingEntry $entry): bool
    {
        // Record specific entry types in production
        $recordTypes = [
            'exception',
            'failed_request',
            'slow_query',
            'failed_job',
        ];

        return in_array($entry->type, $recordTypes);
    }
}