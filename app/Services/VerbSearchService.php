<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the external Qutrub Arabic verb conjugation API.
 *
 * Centralises endpoint, error handling and logging so that controllers
 * (and Livewire components) can stay focused on presentation.
 */
class VerbSearchService
{
    public function __construct(
        private readonly string $endpoint = 'http://qutrub.arabeyes.org/api',
        private readonly int $timeoutSeconds = 15,
    ) {
    }

    /**
     * Fetch conjugation data for the given verb.
     *
     * @return array{success: bool, data?: array, error?: string, status?: int}
     */
    public function search(string $verb): array
    {
        $verb = trim($verb);

        if ($verb === '') {
            return [
                'success' => false,
                'error' => 'Parameter verb is required',
                'status' => 400,
            ];
        }

        try {
            $response = Http::timeout($this->timeoutSeconds)
                ->get($this->endpoint, ['verb' => $verb]);

            if ($response->failed()) {
                Log::warning('Qutrub API request failed', [
                    'verb' => $verb,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to fetch data from external API',
                    'status' => 502,
                ];
            }

            return [
                'success' => true,
                'data' => (array) $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('Qutrub API exception', [
                'verb' => $verb,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
