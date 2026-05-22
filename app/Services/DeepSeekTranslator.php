<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Encapsulates DeepSeek-backed Arabic ↔ Indonesian translation logic.
 *
 * Behaviour preserved from the original TranslationController:
 *   - Cache via {@see Cache} keyed per source/target/text.
 *   - DeepSeek API as primary source; local dictionary as fallback.
 *   - Permissive validation that only rejects clearly invalid output.
 *
 * No HTTP-layer concerns live here — the calling controller (or Livewire
 * component) shapes the response.
 */
class DeepSeekTranslator
{
    private string $apiKey;

    private string $apiUrl;

    private string $model;

    public function __construct(?string $apiKey = null, ?string $apiUrl = null, ?string $model = null)
    {
        $this->apiKey = $apiKey ?? (string) config('services.deepseek.api_key', '');
        $this->apiUrl = $apiUrl ?? (string) config('services.deepseek.api_url', 'https://api.deepseek.com/v1/chat/completions');
        $this->model = $model ?? (string) config('services.deepseek.model', 'deepseek-chat');
    }

    public function hasApiKey(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Translate text from {@see $source} to {@see $target}, with caching.
     *
     * @return array{success: bool, translation?: string, method?: string, cached?: bool, fresh?: bool, message?: string, source: string, target: string}
     */
    public function translate(string $text, string $source = 'ar', string $target = 'id', bool $force = false): array
    {
        $text = trim($text);

        if ($text === '' || $text === '-') {
            return [
                'success' => false,
                'message' => 'Empty or invalid text',
                'source' => $source,
                'target' => $target,
            ];
        }

        $cacheKey = $this->cacheKey($text, $source, $target);

        if ($force) {
            Cache::forget($cacheKey);
        }

        if (! $force && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if ($this->isValidCachedTranslation($cached)) {
                return [
                    'success' => true,
                    'translation' => $cached,
                    'cached' => true,
                    'source' => $source,
                    'target' => $target,
                ];
            }

            Cache::forget($cacheKey);
        }

        if ($this->hasApiKey()) {
            try {
                $translation = $this->callDeepSeek($text, $source, $target);

                if ($translation !== null && $translation !== '') {
                    Cache::put($cacheKey, $translation, now()->addDays(7));

                    return [
                        'success' => true,
                        'translation' => $translation,
                        'method' => 'deepseek_api',
                        'fresh' => true,
                        'source' => $source,
                        'target' => $target,
                    ];
                }
            } catch (\Throwable $e) {
                Log::error('DeepSeek translation error', [
                    'text' => $text,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->fallback($text, $source, $target, $cacheKey);
    }

    /**
     * Batch translate convenience wrapper.
     *
     * @param  string[]  $texts
     * @return array<int, array{text: string, translation: ?string, success: bool, error?: string}>
     */
    public function batch(array $texts, string $source = 'ar', string $target = 'id'): array
    {
        $results = [];

        foreach ($texts as $text) {
            try {
                if ($this->hasApiKey()) {
                    $translation = $this->callDeepSeek($text, $source, $target);
                    if (! $translation || ! $this->isValidTranslation($translation, $text)) {
                        $translation = $this->localTranslation($text)
                            ?? $this->smartFallback($text, $source, $target);
                    }
                } else {
                    $translation = $this->localTranslation($text)
                        ?? $this->smartFallback($text, $source, $target);
                }

                $results[] = [
                    'text' => $text,
                    'translation' => $translation,
                    'success' => true,
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'text' => $text,
                    'translation' => $this->localTranslation($text)
                        ?? $this->smartFallback($text, $source, $target),
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Probe the DeepSeek API.
     *
     * @return array{success: bool, api_status: string, message?: string, sample_result?: string, test_text?: string, service?: string}
     */
    public function check(): array
    {
        if (! $this->hasApiKey()) {
            return [
                'success' => false,
                'api_status' => 'error',
                'message' => 'DeepSeek API key not configured',
            ];
        }

        try {
            $testText = 'السلام عليكم';
            $result = $this->callDeepSeek($testText, 'ar', 'id');

            if ($result && $this->isValidTranslation($result, $testText)) {
                return [
                    'success' => true,
                    'api_status' => 'working',
                    'service' => 'DeepSeek API',
                    'sample_result' => $result,
                    'test_text' => $testText,
                ];
            }

            return [
                'success' => false,
                'api_status' => 'error',
                'message' => 'DeepSeek API test failed',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'api_status' => 'error',
                'message' => 'Error checking DeepSeek API: ' . $e->getMessage(),
            ];
        }
    }

    // ----------------------------------------------------------------------
    // Internal helpers
    // ----------------------------------------------------------------------

    private function cacheKey(string $text, string $source, string $target): string
    {
        return "translate_v4:{$source}:{$target}:" . md5($text);
    }

    private function callDeepSeek(string $text, string $source, string $target): ?string
    {
        $cleanText = $this->cleanInputText($text);
        $systemPrompt = $this->buildSystemPrompt($source, $target);
        $userPrompt = $this->buildUserPrompt($cleanText, $source, $target);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-DeepSeek/1.0',
        ])->timeout(30)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'max_tokens' => 150,
            'temperature' => 0.1,
            'top_p' => 0.9,
            'stream' => false,
        ]);

        if ($response->failed()) {
            Log::error('DeepSeek API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException("DeepSeek API error: {$response->status()}");
        }

        $payload = $response->json();
        $rawTranslation = $payload['choices'][0]['message']['content'] ?? null;

        if ($rawTranslation === null) {
            return null;
        }

        $cleaned = $this->cleanTranslationResult($rawTranslation);

        return $cleaned !== '' ? $cleaned : trim((string) $rawTranslation);
    }

    private function cleanInputText(string $text): string
    {
        $cleaned = trim($text, '"\'');
        $cleaned = preg_replace('/\s+/', ' ', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }

    private function cleanTranslationResult(string $translation): string
    {
        $cleaned = trim($translation);
        $cleaned = trim($cleaned, '"\'`');

        $prefixes = [
            'Translation: ', 'Terjemahan: ', 'Indonesian: ', 'Arabic: ',
            'The translation is: ', 'Artinya: ', 'Answer: ',
        ];

        foreach ($prefixes as $prefix) {
            if (stripos($cleaned, $prefix) === 0) {
                $cleaned = substr($cleaned, strlen($prefix));
                break;
            }
        }

        return trim($cleaned);
    }

    private function buildSystemPrompt(string $source, string $target): string
    {
        if ($source === 'ar' && $target === 'id') {
            return "Kamu adalah penerjemah profesional Arab-Indonesia yang sangat ahli dalam tata bahasa Arab dan kata kerja.\n\n"
                . "ATURAN KETAT:\n"
                . "1. Berikan HANYA terjemahan Indonesia, tanpa penjelasan\n"
                . "2. Untuk kata kerja past tense (ماضي): gunakan 'dia [verb] (laki-laki)' atau 'dia [verb] (perempuan)'\n"
                . "3. Untuk kata kerja present tense (مضارع): gunakan 'dia [verb] (laki-laki)' atau 'dia [verb] (perempuan)'\n"
                . "4. Untuk kata kerja imperative (أمر): gunakan '[verb]lah!'\n"
                . "5. JANGAN gunakan awalan seperti 'Translation:' atau 'Terjemahan:'\n"
                . "6. فعل artinya 'melakukan' atau 'berbuat'\n\n"
                . "Contoh spesifik:\n"
                . "- فَعَلَ → dia melakukan (laki-laki)\n"
                . "- يَفْعَلُ → dia melakukan (laki-laki)\n"
                . "- اِفْعَلْ → lakukanlah!\n"
                . "- فعل ثلاثي → kata kerja trilateral\n"
                . "- متعدي → transitif\n"
                . "- صحيح سالم → sehat tanpa cacat";
        }

        return 'You are a professional Arabic-Indonesian translator. Provide only the translation without explanations.';
    }

    private function buildUserPrompt(string $text, string $source, string $target): string
    {
        if ($source === 'ar' && $target === 'id') {
            if (
                str_contains($text, 'فَعَلَ')
                || str_contains($text, 'يَفْعَلُ')
                || str_contains($text, 'اِفْعَلْ')
            ) {
                return "Terjemahkan kata kerja Arab ini ke Indonesia dengan format yang tepat: {$text}";
            }

            return "Terjemahkan teks Arab ini ke Indonesia: {$text}";
        }

        $sourceLanguage = $this->languageName($source);
        $targetLanguage = $this->languageName($target);

        return "Translate from {$sourceLanguage} to {$targetLanguage}: {$text}";
    }

    private function languageName(string $code): string
    {
        return match ($code) {
            'ar' => 'Arabic',
            'id' => 'Indonesian',
            'en' => 'English',
            default => 'Unknown',
        };
    }

    private function fallback(string $text, string $source, string $target, string $cacheKey): array
    {
        $translation = $this->localTranslation($text)
            ?? $this->contextualTranslation($text, $source, $target)
            ?? $this->smartFallback($text, $source, $target);

        if ($translation && ! str_contains($translation, '[Belum tersedia')) {
            Cache::put($cacheKey, $translation, now()->addHours(6));
        }

        return [
            'success' => true,
            'translation' => $translation,
            'method' => 'local_fallback',
            'source' => $source,
            'target' => $target,
        ];
    }

    private function isValidCachedTranslation(?string $cached): bool
    {
        if (! is_string($cached) || $cached === '') {
            return false;
        }

        $invalidPatterns = [
            '[Belum tersedia terjemahan]',
            '[Terjemahan:',
            'Terjemahan untuk:',
            'gagal menerjemahkan',
            'tidak dapat menerjemahkan',
            'error translating',
            'Unknown translation',
        ];

        foreach ($invalidPatterns as $pattern) {
            if (str_contains($cached, $pattern)) {
                return false;
            }
        }

        return true;
    }

    private function isValidTranslation(string $translation, string $original): bool
    {
        if ($translation === '' || $translation === $original) {
            return false;
        }

        $criticalPatterns = [
            'I cannot translate',
            "I'm sorry, I cannot",
            'Unable to translate',
            'Error translating',
            'As an AI, I cannot',
            'I apologize, but I cannot',
        ];

        foreach ($criticalPatterns as $pattern) {
            if (stripos($translation, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }

    private function localTranslation(string $text): ?string
    {
        $translations = $this->localDictionary();

        if (isset($translations[$text])) {
            return $translations[$text];
        }

        $partialMatches = [];
        foreach ($translations as $key => $value) {
            if (str_contains($text, $key)) {
                $partialMatches[$key] = $value;
            }
        }

        if (! empty($partialMatches)) {
            uksort($partialMatches, fn ($a, $b) => strlen($b) - strlen($a));

            return $partialMatches[array_key_first($partialMatches)];
        }

        return null;
    }

    private function contextualTranslation(string $text, string $source, string $target): ?string
    {
        if ($source === 'ar' && $target === 'id') {
            if (str_contains($text, 'نَظَرَ') || str_contains($text, 'يَنْظُرُ')) {
                return 'dia melihat (laki-laki)';
            }
            if (str_contains($text, 'اُنْظُرْ')) {
                return 'lihatlah!';
            }
        }

        return null;
    }

    private function smartFallback(string $text, string $source, string $target): string
    {
        if ($source === 'ar' && $target === 'id') {
            return strlen($text) <= 10
                ? "[perlu terjemahan: {$text}]"
                : '[teks panjang perlu terjemahan manual]';
        }

        return '[translation needed]';
    }

    /**
     * Local Arabic ↔ Indonesian dictionary used as last-line fallback.
     *
     * @return array<string, string>
     */
    private function localDictionary(): array
    {
        return [
            // Kata kerja فعل (melakukan/berbuat)
            'فَعَلَ' => 'dia melakukan (laki-laki)',
            'يَفْعَلُ' => 'dia melakukan (laki-laki)',
            'اِفْعَلْ' => 'lakukanlah!',
            'فَعَلَتْ' => 'dia melakukan (perempuan)',
            'تَفْعَلُ' => 'dia melakukan (perempuan)',
            'فَعَلْتُ' => 'saya melakukan',
            'أَفْعَلُ' => 'saya melakukan',
            'فَعَلْنَا' => 'kami melakukan',
            'نَفْعَلُ' => 'kami melakukan',

            // Kata kerja نظر (melihat/memandang)
            'نَظَرَ' => 'dia melihat (laki-laki)',
            'يَنْظُرُ' => 'dia melihat (laki-laki)',
            'اُنْظُرْ' => 'lihatlah!',
            'نَظَرَتْ' => 'dia melihat (perempuan)',
            'تَنْظُرُ' => 'dia melihat (perempuan)',
            'نَظَرْتُ' => 'saya melihat',
            'أَنْظُرُ' => 'saya melihat',
            'نَظَرْنَا' => 'kami melihat',
            'نَنْظُرُ' => 'kami melihat',

            // Kata kerja شرب (minum)
            'شَرَبَ' => 'dia minum (laki-laki)',
            'يَشْرُبُ' => 'dia minum (laki-laki)',
            'اُشْرُبْ' => 'minumlah!',
            'شَرَبَتْ' => 'dia minum (perempuan)',
            'تَشْرُبُ' => 'dia minum (perempuan)',

            // Kata kerja كتب (menulis)
            'كَتَبَ' => 'dia menulis (laki-laki)',
            'يَكْتُبُ' => 'dia menulis (laki-laki)',
            'اُكْتُبْ' => 'tulislah!',
            'كَتَبَتْ' => 'dia menulis (perempuan)',
            'تَكْتُبُ' => 'dia menulis (perempuan)',

            // Kata kerja كفر (mengingkari)
            'كَفَرَ' => 'dia mengingkari (laki-laki)',
            'يَكْفُرُ' => 'dia mengingkari (laki-laki)',
            'اُكْفُرْ' => 'ingkarilah!',
            'كَفَرَتْ' => 'dia mengingkari (perempuan)',
            'تَكْفُرُ' => 'dia mengingkari (perempuan)',

            // Kata kerja قرأ (membaca)
            'قَرَأَ' => 'dia membaca (laki-laki)',
            'يَقْرَأُ' => 'dia membaca (laki-laki)',
            'اِقْرَأْ' => 'bacalah!',
            'قَرَأَتْ' => 'dia membaca (perempuan)',
            'تَقْرَأُ' => 'dia membaca (perempuan)',

            // Istilah tata bahasa
            'الماضي' => 'masa lampau',
            'المضارع' => 'masa sekarang',
            'الأمر' => 'perintah',
            'الفعل' => 'kata kerja',
            'فعل' => 'kata kerja',
            'فعل ثلاثي' => 'kata kerja trilateral',
            'فعل ثلاثي متعدي' => 'kata kerja trilateral transitif',
            'فعل ثلاثي متعدي صحيح سالم' => 'kata kerja trilateral transitif sehat tanpa cacat',
            'متعدي' => 'mutaaddi (transitif)',
            'لازم' => 'lazim (intransitif)',
            'صحيح' => 'sehat (tanpa huruf illat)',
            'سالم' => 'tanpa cacat',

            // Salam dan doa
            'السلام عليكم' => 'semoga keselamatan atas kalian',
            'الحمد لله' => 'segala puji bagi Allah',
            'بسم الله' => 'dengan nama Allah',

            // UI Elements
            'معلومات الفعل' => 'Informasi Kata Kerja',
            'Informasi Kata Kerja' => 'Informasi Kata Kerja',
            'في قاعدة البيانات' => 'dalam database',
        ];
    }
}
