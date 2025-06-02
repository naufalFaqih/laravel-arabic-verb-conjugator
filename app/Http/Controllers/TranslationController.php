<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    private $deepseekApiKey;
    private $deepseekApiUrl;
    
    public function __construct()
    {
        // PERBAIKAN: Inisialisasi di constructor dengan hardcode untuk testing
        $this->deepseekApiKey = env('DEEPSEEK_API_KEY') ?: 'sk-86cd11de25dc45a3920647b89c398c75';
        $this->deepseekApiUrl = env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions');
        
        Log::info('TranslationController initialized', [
            'api_key_present' => !empty($this->deepseekApiKey),
            'api_key_length' => strlen($this->deepseekApiKey ?? ''),
            'api_url' => $this->deepseekApiUrl
        ]);
    }
    
    /**
     * Terjemahkan teks menggunakan DeepSeek API
     */
 public function translate(Request $request)
{
    Log::info('=== TRANSLATION REQUEST START ===');
    Log::info('Request data', $request->all());
    
    $request->validate([
        'text' => 'required|string',
        'source' => 'nullable|string|in:ar,id,en',
        'target' => 'nullable|string|in:ar,id,en',
        'force' => 'nullable|boolean'
    ]);

    $text = trim($request->input('text'));
    $source = $request->input('source', 'ar');
    $target = $request->input('target', 'id');
    $force = $request->input('force', false);
    
    Log::info('Translation parameters', [
        'text' => $text,
        'source' => $source,
        'target' => $target,
        'force' => $force,
        'api_key_present' => !empty($this->deepseekApiKey),
        'api_key_prefix' => $this->deepseekApiKey ? substr($this->deepseekApiKey, 0, 10) . '...' : 'NONE'
    ]);
    
    // Skip empty or invalid text
    if (empty($text) || $text === '-') {
        Log::warning('Empty or invalid text provided', ['text' => $text]);
        return response()->json([
            'success' => false,
            'message' => 'Empty or invalid text',
            'text' => $text
        ]);
    }
    
    // PERBAIKAN: Cache key yang lebih baik
    $cacheKey = "translate_v4:{$source}:{$target}:" . md5($text);
    
    // Jika force=true, hapus cache
    if ($force) {
        Log::info('Force translation requested, clearing cache');
        Cache::forget($cacheKey);
    }
    
    // Cek cache valid
    if (!$force && Cache::has($cacheKey)) {
        $cachedTranslation = Cache::get($cacheKey);
        
        if ($this->isValidCachedTranslation($cachedTranslation, $text)) {
            Log::info('Using valid cached translation', [
                'text' => $text,
                'translation' => $cachedTranslation
            ]);
            
            return response()->json([
                'success' => true,
                'translation' => $cachedTranslation,
                'source' => $source,
                'target' => $target,
                'cached' => true
            ]);
        } else {
            Log::warning('Removing invalid cache', ['cached' => $cachedTranslation]);
            Cache::forget($cacheKey);
        }
    }

    try {
        Log::info('Attempting DeepSeek API translation');
        
        // PERBAIKAN UTAMA: PRIORITASKAN DeepSeek API dan TERIMA hasilnya
        if (!empty($this->deepseekApiKey)) {
            $translation = $this->translateWithDeepSeek($text, $source, $target);
            
            // PERBAIKAN: Jika ada hasil dari API, GUNAKAN ITU!
            if (!empty($translation)) {
                // Simpan ke cache
                Cache::put($cacheKey, $translation, now()->addDays(7));
                
                Log::info('✅ DeepSeek API SUCCESS - Using API result', [
                    'text' => $text,
                    'api_translation' => $translation
                ]);
                
                return response()->json([
                    'success' => true,
                    'translation' => $translation,
                    'source' => $source,
                    'target' => $target,
                    'method' => 'deepseek_api',
                    'fresh' => true
                ]);
            }
            
            Log::warning('DeepSeek API returned empty result', [
                'text' => $text,
                'translation' => $translation
            ]);
        } else {
            Log::warning('DeepSeek API key not available');
        }
        
        // Fallback ke terjemahan lokal HANYA jika API gagal total
        return $this->fallbackTranslate($text, $source, $target, $cacheKey);
        
    } catch (\Exception $e) {
        Log::error('DeepSeek translation error', [
            'text' => $text,
            'error' => $e->getMessage()
        ]);
        
        // Fallback ke terjemahan lokal
        return $this->fallbackTranslate($text, $source, $target, $cacheKey);
    }
}
    
    /**
     * PERBAIKAN: Validasi cache yang ketat
     */
private function isValidCachedTranslation($cachedTranslation, $originalText)
{
    if (empty($cachedTranslation)) {
        return false;
    }
    
    // PERBAIKAN: Hanya tolak cache yang jelas-jelas buruk
    $invalidPatterns = [
        '[Belum tersedia terjemahan]',
        '[Terjemahan:',
        'Terjemahan untuk:',
        'gagal menerjemahkan',
        'tidak dapat menerjemahkan',
        'error translating',
        'Unknown translation'
    ];
    
    foreach ($invalidPatterns as $pattern) {
        if (str_contains($cachedTranslation, $pattern)) {
            return false;
        }
    }
    
    return true;
}

    
    /**
     * PERBAIKAN: Validasi translation yang lebih baik
     */
    private function isValidTranslation($translation, $originalText)
{
    // PERBAIKAN: Validasi minimal - hanya tolak yang benar-benar kosong atau error jelas
    if (empty($translation)) {
        Log::info('Translation validation failed: empty translation');
        return false;
    }
    
    if ($translation === $originalText) {
        Log::info('Translation validation failed: same as original');
        return false;
    }
    
    // PERBAIKAN: Tolak hanya pattern error yang jelas
    $criticalErrorPatterns = [
        'I cannot translate',
        'I\'m sorry, I cannot',
        'Unable to translate', 
        'Error translating',
        'As an AI, I cannot',
        'I apologize, but I cannot'
    ];
    
    foreach ($criticalErrorPatterns as $pattern) {
        if (stripos($translation, $pattern) !== false) {
            Log::info('Translation validation failed: contains critical error pattern', [
                'pattern' => $pattern,
                'translation' => $translation
            ]);
            return false;
        }
    }
    
    Log::info('✅ Translation validation PASSED', [
        'translation' => $translation,
        'original' => $originalText
    ]);
    
    return true;
}

    
    /**
     * PERBAIKAN: translateWithDeepSeek yang lebih robust
     */
   private function translateWithDeepSeek($text, $source, $target)
{
    Log::info('=== DEEPSEEK API CALL START ===');
    
    // Clean input text
    $cleanText = $this->cleanInputText($text);
    
    Log::info('DeepSeek API parameters', [
        'original_text' => $text,
        'cleaned_text' => $cleanText,
        'source' => $source,
        'target' => $target,
        'api_url' => $this->deepseekApiUrl,
        'api_key_prefix' => substr($this->deepseekApiKey, 0, 10) . '...'
    ]);

    // PERBAIKAN: Prompt yang lebih efektif
    $systemPrompt = $this->buildSystemPrompt($source, $target);
    $userPrompt = $this->buildUserPrompt($cleanText, $source, $target);
    
    $requestData = [
        'model' => 'deepseek-chat',
        'messages' => [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $userPrompt
            ]
        ],
        'max_tokens' => 150,
        'temperature' => 0.1,
        'top_p' => 0.9,
        'stream' => false
    ];
    
    Log::info('DeepSeek request about to be sent', [
        'model' => $requestData['model'],
        'system_prompt' => $systemPrompt,
        'user_prompt' => $userPrompt,
        'max_tokens' => $requestData['max_tokens']
    ]);
    
    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->deepseekApiKey,
            'Content-Type' => 'application/json',
            'User-Agent' => 'Laravel-DeepSeek/1.0'
        ])->timeout(30)->post($this->deepseekApiUrl, $requestData);
        
        Log::info('DeepSeek API response received', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'failed' => $response->failed(),
            'body_length' => strlen($response->body()),
            'response_preview' => substr($response->body(), 0, 500)
        ]);
        
        if ($response->failed()) {
            $errorBody = $response->body();
            Log::error('DeepSeek API request failed', [
                'status' => $response->status(),
                'error_body' => $errorBody,
                'headers' => $response->headers()
            ]);
            
            throw new \Exception("DeepSeek API error: {$response->status()} - {$errorBody}");
        }
        
        $result = $response->json();
        
        Log::info('DeepSeek API parsed response', [
            'full_result' => $result,
            'has_choices' => isset($result['choices']),
            'choices_count' => isset($result['choices']) ? count($result['choices']) : 0
        ]);
        
        if (isset($result['choices'][0]['message']['content'])) {
            $rawTranslation = $result['choices'][0]['message']['content'];
            
            Log::info('DeepSeek raw translation received', [
                'raw_translation' => $rawTranslation,
                'raw_length' => strlen($rawTranslation)
            ]);
            
            $cleanedTranslation = $this->cleanTranslationResult($rawTranslation);
            
            Log::info('DeepSeek translation cleaned', [
                'original_text' => $cleanText,
                'raw_translation' => $rawTranslation,
                'cleaned_translation' => $cleanedTranslation
            ]);
            
            // PERBAIKAN UTAMA: SELALU return hasil API yang sudah dibersihkan
            // JANGAN lakukan validasi yang terlalu ketat
            if (!empty($cleanedTranslation)) {
                Log::info('✅ RETURNING DEEPSEEK API RESULT', [
                    'text' => $cleanText,
                    'api_result' => $cleanedTranslation
                ]);
                return $cleanedTranslation;
            } else if (!empty($rawTranslation)) {
                Log::info('✅ RETURNING RAW DEEPSEEK RESULT', [
                    'text' => $cleanText,
                    'raw_result' => $rawTranslation
                ]);
                return $rawTranslation;
            }
        }
        
        Log::warning('DeepSeek API returned invalid structure', [
            'result_keys' => array_keys($result),
            'full_result' => $result
        ]);
        return null;
        
    } catch (\Exception $e) {
        Log::error('DeepSeek API exception', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        throw $e;
    }
}
    
    /**
     * PERBAIKAN: Clean input text
     */
    private function cleanInputText($text)
    {
        // Remove quotes dan extra whitespace
        $cleaned = trim($text, '"\'');
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return trim($cleaned);
    }
    
    /**
     * PERBAIKAN: Clean translation result
     */
    private function cleanTranslationResult($translation)
    {
        if (empty($translation)) {
            return '';
        }
        
        $cleaned = trim($translation);
        
        // Remove quotes
        $cleaned = trim($cleaned, '"\'`');
        
        // Remove common prefixes
        $prefixes = [
            'Translation: ', 'Terjemahan: ', 'Indonesian: ', 'Arabic: ',
            'The translation is: ', 'Artinya: ', 'Answer: '
        ];
        
        foreach ($prefixes as $prefix) {
            if (stripos($cleaned, $prefix) === 0) {
                $cleaned = substr($cleaned, strlen($prefix));
                break;
            }
        }
        
        return trim($cleaned);
    }

    /**
     * PERBAIKAN: System prompt yang spesifik
     */
private function buildSystemPrompt($source, $target)
{
    if ($source === 'ar' && $target === 'id') {
        return "Kamu adalah penerjemah profesional Arab-Indonesia yang sangat ahli dalam tata bahasa Arab dan kata kerja.

ATURAN KETAT:
1. Berikan HANYA terjemahan Indonesia, tanpa penjelasan
2. Untuk kata kerja past tense (ماضي): gunakan 'dia [verb] (laki-laki)' atau 'dia [verb] (perempuan)'
3. Untuk kata kerja present tense (مضارع): gunakan 'dia [verb] (laki-laki)' atau 'dia [verb] (perempuan)'
4. Untuk kata kerja imperative (أمر): gunakan '[verb]lah!'
5. JANGAN gunakan awalan seperti 'Translation:' atau 'Terjemahan:'
6. فعل artinya 'melakukan' atau 'berbuat'

Contoh spesifik:
- فَعَلَ → dia melakukan (laki-laki)
- يَفْعَلُ → dia melakukan (laki-laki)
- اِفْعَلْ → lakukanlah!
- فعل ثلاثي → kata kerja trilateral
- متعدي → transitif
- صحيح سالم → sehat tanpa cacat";
    }
    
    return "You are a professional Arabic-Indonesian translator. Provide only the translation without explanations.";
}
    /**
     * PERBAIKAN: User prompt yang sederhana
     */
  private function buildUserPrompt($text, $source, $target)
{
    if ($source === 'ar' && $target === 'id') {
        // Untuk kata kerja فعل yang spesifik
        if (strpos($text, 'فَعَلَ') !== false || strpos($text, 'يَفْعَلُ') !== false || strpos($text, 'اِفْعَلْ') !== false) {
            return "Terjemahkan kata kerja Arab ini ke Indonesia dengan format yang tepat: {$text}";
        }
        
        return "Terjemahkan teks Arab ini ke Indonesia: {$text}";
    }
    
    $sourceLanguage = $this->getLanguageName($source);
    $targetLanguage = $this->getLanguageName($target);
    
    return "Translate from {$sourceLanguage} to {$targetLanguage}: {$text}";
}
    /**
     * Konversi kode bahasa ke nama bahasa
     */
    private function getLanguageName($code)
    {
        $languages = [
            'ar' => 'Arabic',
            'id' => 'Indonesian',
            'en' => 'English'
        ];
        
        return $languages[$code] ?? 'Unknown';
    }
    
    /**
     * PERBAIKAN: Fallback translation yang lebih pintar
     */
    private function fallbackTranslate($text, $source, $target, $cacheKey)
    {
        Log::info('Using fallback translation', ['text' => $text]);
        
        $translation = $this->getLocalTranslation($text);
        
        if (!$translation) {
            $translation = $this->getContextualTranslation($text, $source, $target);
        }
        
        if (!$translation) {
            $translation = $this->getSmartFallback($text, $source, $target);
        }
        
        // Jangan cache fallback yang buruk
        if ($translation && !str_contains($translation, '[Belum tersedia')) {
            Cache::put($cacheKey, $translation, now()->addHours(6));
        }
        
        return response()->json([
            'success' => true,
            'translation' => $translation,
            'source' => $source,
            'target' => $target,
            'method' => 'local_fallback'
        ]);
    }
    
    /**
     * PERBAIKAN: Local translation dengan kata kerja نظر
     */
  private function getLocalTranslation($text)
{
    $translations = [
        // Kata kerja فعل (melakukan/berbuat) - PERBAIKAN: Tambahan khusus
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
        'في قاعدة البيانات' => 'dalam database'
    ];
    
    // Exact match
    if (isset($translations[$text])) {
        Log::info('Found exact local translation', [
            'text' => $text,
            'translation' => $translations[$text]
        ]);
        return $translations[$text];
    }
    
    // Partial match dengan prioritas panjang
    $partialMatches = [];
    foreach ($translations as $key => $value) {
        if (str_contains($text, $key)) {
            $partialMatches[$key] = $value;
        }
    }
    
    if (!empty($partialMatches)) {
        // Prioritaskan match yang lebih panjang
        uksort($partialMatches, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        $bestMatch = array_key_first($partialMatches);
        Log::info('Found partial local translation', [
            'text' => $text,
            'matched_key' => $bestMatch,
            'translation' => $partialMatches[$bestMatch]
        ]);
        return $partialMatches[$bestMatch];
    }
    
    return null;
}
    /**
     * PERBAIKAN: Contextual translation
     */
    private function getContextualTranslation($text, $source, $target)
    {
        if ($source === 'ar' && $target === 'id') {
            // Pattern analysis untuk kata kerja نظر
            if (str_contains($text, 'نَظَرَ')) {
                return 'dia melihat (laki-laki)';
            }
            if (str_contains($text, 'يَنْظُرُ')) {
                return 'dia melihat (laki-laki)';
            }
            if (str_contains($text, 'اُنْظُرْ')) {
                return 'lihatlah!';
            }
            

        }
        
        return null;
    }
    
    /**
     * PERBAIKAN: Smart fallback
     */
    private function getSmartFallback($text, $source, $target)
    {
        if ($source === 'ar' && $target === 'id') {
            if (strlen($text) <= 10) {
                return "[perlu terjemahan: {$text}]";
            }
            return "[teks panjang perlu terjemahan manual]";
        }
        
        return "[translation needed]";
    }
    
    /**
     * Cek status DeepSeek API
     */
    public function checkApi(Request $request)
    {
        try {
            Log::info('=== API CHECK START ===');
            
            if (empty($this->deepseekApiKey)) {
                return response()->json([
                    'success' => false,
                    'api_status' => 'error',
                    'message' => 'DeepSeek API key not configured'
                ]);
            }
            
            $testText = 'السلام عليكم';
            $result = $this->translateWithDeepSeek($testText, 'ar', 'id');
            
            if ($result && $this->isValidTranslation($result, $testText)) {
                return response()->json([
                    'success' => true,
                    'api_status' => 'working',
                    'service' => 'DeepSeek API',
                    'sample_result' => $result,
                    'test_text' => $testText
                ]);
            }
            
            return response()->json([
                'success' => false,
                'api_status' => 'error',
                'message' => 'DeepSeek API test failed'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking DeepSeek API: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batch translate
     */
    public function batchTranslate(Request $request)
    {
        $request->validate([
            'texts' => 'required|array',
            'texts.*' => 'required|string',
            'source' => 'nullable|string|in:ar,id,en',
            'target' => 'nullable|string|in:ar,id,en',
        ]);
        
        $texts = $request->input('texts');
        $source = $request->input('source', 'ar');
        $target = $request->input('target', 'id');
        
        $results = [];
        
        foreach ($texts as $text) {
            try {
                if (!empty($this->deepseekApiKey)) {
                    $translation = $this->translateWithDeepSeek($text, $source, $target);
                    
                    if (!$translation || !$this->isValidTranslation($translation, $text)) {
                        $translation = $this->getLocalTranslation($text) ?: $this->getSmartFallback($text, $source, $target);
                    }
                } else {
                    $translation = $this->getLocalTranslation($text) ?: $this->getSmartFallback($text, $source, $target);
                }
                
                $results[] = [
                    'text' => $text,
                    'translation' => $translation,
                    'success' => true
                ];
                
            } catch (\Exception $e) {
                $results[] = [
                    'text' => $text,
                    'translation' => $this->getLocalTranslation($text) ?: $this->getSmartFallback($text, $source, $target),
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'results' => $results,
            'source' => $source,
            'target' => $target
        ]);
    }
}