<?php

namespace App\Livewire\Verb;

use App\Services\SearchHistoryService;
use App\Services\VerbSearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Verb conjugation search experience (replaces home.blade.php's inline form
 * & search.js DOM rendering).
 *
 * The Qutrub API returns a numerically-keyed structure:
 *   - result[0]      → 8 column headers (we reverse for display)
 *   - result[1..14]  → rows; index meaning:
 *       [0] = pronoun, [1] = madhiMalum, [2] = mudhoriMalum,
 *       [3] = mudhoriMajzum, [4] = mudhoriMansub, [5] = mudhoriMuakkad,
 *       [6] = amar, [7] = amarMuakkad
 *   - result[9]      → summary row (madhi, mudhori)
 *   - result[3][6]   → amar summary
 *   - verb_info      → string; suggest → array of {verb, future}
 *
 * Translation of the Arabic strings still happens client-side (legacy
 * TranslationEnhanced JS) so that browser-level localStorage caching
 * continues to work.
 */
class Search extends Component
{
    /**
     * Pre-filled from ?query=… so users can deep-link from the search history.
     */
    #[Url(as: 'query', except: '')]
    public string $verb = '';

    /** @var array<int, string> Column headers, already reversed for display. */
    public array $columns = [];

    /** @var array<string, array<int, string>> Conjugation cells per category. */
    public array $cells = [];

    /** @var array{madhi: string, mudhori: string, amar: string} */
    public array $summary = ['madhi' => '-', 'mudhori' => '-', 'amar' => '-'];

    public string $verbInfo = '';

    /** @var array<int, array{verb: string, future: string}> */
    public array $suggest = [];

    public bool $hasResult = false;

    public ?string $errorMessage = null;

    /**
     * Allow auto-loading from ?query=verb param.
     */
    public function mount(?string $query = null): void
    {
        $candidate = $query ?? request()->query('query', request()->query('q', ''));
        if (is_string($candidate) && trim($candidate) !== '') {
            $this->verb = trim($candidate);
            $this->search();
        }
    }

    public function search(): void
    {
        $verb = trim($this->verb);
        $this->errorMessage = null;
        $this->resetResult();

        if ($verb === '') {
            $this->errorMessage = 'Masukkan kata kerja Arab terlebih dahulu.';

            return;
        }

        if (! preg_match('/^[\x{0600}-\x{06FF}\s]+$/u', $verb)) {
            $this->errorMessage = 'Hanya diperbolehkan karakter dalam bahasa Arab.';

            return;
        }

        $result = app(VerbSearchService::class)->search($verb);

        if (! ($result['success'] ?? false)) {
            $this->errorMessage = $result['error'] ?? 'Gagal mengambil data.';

            return;
        }

        $this->parseResponse($result['data'] ?? [], $verb);
        $this->hasResult = true;

        if (Auth::check()) {
            $this->saveHistory($verb);
        }

        $this->dispatch('verb-result-ready');
    }

    private function resetResult(): void
    {
        $this->columns = [];
        $this->cells = [
            'domir' => [],
            'madhiMalum' => [],
            'mudhoriMalum' => [],
            'mudhoriMajzum' => [],
            'mudhoriMansub' => [],
            'mudhoriMuakkad' => [],
            'amar' => [],
            'amarMuakkad' => [],
        ];
        $this->summary = ['madhi' => '-', 'mudhori' => '-', 'amar' => '-'];
        $this->verbInfo = '';
        $this->suggest = [];
        $this->hasResult = false;
    }

    private function parseResponse(array $data, string $verb): void
    {
        $result = $data['result'] ?? [];

        // 8 column headers — reverse for display order.
        $headers = $this->normalizeRow($result[0] ?? []);
        $this->columns = array_values(array_reverse(array_slice($headers, 0, 8)));

        // 14 rows of conjugation cells.
        for ($i = 1; $i <= 14; $i++) {
            $row = $this->normalizeRow($result[$i] ?? []);
            if ($row === []) {
                continue;
            }

            $this->cells['domir'][] = (string) ($row[0] ?? '-');
            $this->cells['madhiMalum'][] = (string) ($row[1] ?? '-');
            $this->cells['mudhoriMalum'][] = (string) ($row[2] ?? '-');
            $this->cells['mudhoriMajzum'][] = (string) ($row[3] ?? '-');
            $this->cells['mudhoriMansub'][] = (string) ($row[4] ?? '-');
            $this->cells['mudhoriMuakkad'][] = (string) ($row[5] ?? '-');
            $this->cells['amar'][] = (string) ($row[6] ?? '-');
            $this->cells['amarMuakkad'][] = (string) ($row[7] ?? '-');
        }

        // Summary row (result[9]) — madhi, mudhori. Amar summary at result[3][6].
        $summaryRow = $this->normalizeRow($result[9] ?? []);
        $this->summary['madhi'] = (string) ($summaryRow[1] ?? '-');
        $this->summary['mudhori'] = (string) ($summaryRow[2] ?? '-');

        $amarRow = $this->normalizeRow($result[3] ?? []);
        $this->summary['amar'] = (string) ($amarRow[6] ?? '-');

        // Tashrif fallbacks.
        $tashrif = $data['tashrif'] ?? null;
        if ($this->summary['madhi'] === '-' && is_array($tashrif)) {
            $this->summary['madhi'] = (string) ($tashrif['madhi'] ?? '-');
            $this->summary['mudhori'] = (string) ($tashrif['mudhori'] ?? '-');
            $this->summary['amar'] = (string) ($tashrif['amr'] ?? '-');
        }

        // Verb info.
        $verbInfoText = (string) ($data['verb_info'] ?? '');
        if ($verbInfoText === '' && isset($result[0][0])) {
            $verbInfoText = (string) $result[0][0];
        }
        $this->verbInfo = $verbInfoText !== '' ? $verbInfoText : "الفعل {$verb}";

        // Suggestions.
        $rawSuggest = $data['suggest'] ?? [];
        $this->suggest = array_values(array_map(
            fn ($item) => [
                'verb' => (string) ($item['verb'] ?? ''),
                'future' => (string) ($item['future'] ?? ''),
            ],
            is_array($rawSuggest) ? $rawSuggest : []
        ));
    }

    /**
     * Qutrub responses sometimes return numerically-indexed arrays as objects
     * (string keys "0", "1", …). Normalise so we can use array indexing.
     *
     * @param  mixed  $value
     * @return array<int, mixed>
     */
    private function normalizeRow(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $keys = array_keys($value);
        if ($keys && is_string($keys[0])) {
            usort($keys, fn ($a, $b) => (int) $a - (int) $b);
            return array_values(array_map(fn ($k) => $value[$k], $keys));
        }

        return array_values($value);
    }

    private function saveHistory(string $verb): void
    {
        try {
            app(SearchHistoryService::class)->store(
                (int) Auth::id(),
                ['query' => $verb, 'result' => null]
            );

            $this->dispatch('verb-saved');
        } catch (\Throwable $e) {
            Log::warning('Failed to save search history from Livewire: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.verb.search');
    }
}
