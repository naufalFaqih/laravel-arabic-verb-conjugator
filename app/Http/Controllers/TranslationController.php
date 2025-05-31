<?php

namespace App\Http\Controllers;

use App\Services\GoogleTranslateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    protected $googleTranslateService;
    
    public function __construct(GoogleTranslateService $googleTranslateService)
    {
        $this->googleTranslateService = $googleTranslateService;
    }
    
    /**
     * Terjemahkan teks menggunakan Google Translate
     */
    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'source' => 'required|string|size:2',
            'target' => 'required|string|size:2',
        ]);

        $text = $request->input('text');
        $source = $request->input('source', 'ar');
        $target = $request->input('target', 'id');
        $force = $request->input('force', false);
        
        // Log request untuk debugging
        Log::info('Translation request', [
            'text' => $text,
            'source' => $source,
            'target' => $target,
            'force' => $force
        ]);
        
        // Kunci cache
        $cacheKey = "translation:{$source}:{$target}:" . md5($text);
        
        // Cek cache kecuali jika force=true
        if (!$force && Cache::has($cacheKey)) {
            $cachedTranslation = Cache::get($cacheKey);
            
            // Jangan gunakan cache jika terjemahan sama dengan teks asli
            if ($cachedTranslation === $text) {
                Cache::forget($cacheKey);
                Log::info('Removed invalid cache', ['text' => $text]);
            } else {
                Log::info('Using cached translation', [
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
            }
        }

        try {
            // Gunakan Google Translate Service
            if ($source === 'ar' && $target === 'id') {
                $translation = $this->googleTranslateService->translateArabicToIndonesian($text);
            } else {
                $translation = $this->googleTranslateService->translateWithAutoDetect($text, $target);
            }
            
            if ($translation) {
                // Simpan ke cache
                Cache::put($cacheKey, $translation, now()->addDays(7));
                
                return response()->json([
                    'success' => true,
                    'translation' => $translation,
                    'source' => $source,
                    'target' => $target,
                    'method' => 'google'
                ]);
            }
            
            // Jika Google Translate gagal, gunakan mock translation
            return $this->mockTranslate($text, $source, $target, $cacheKey);
            
        } catch (\Exception $e) {
            Log::error('Translation error: ' . $e->getMessage());
            
            // Fallback ke terjemahan lokal jika terjadi error
            return $this->mockTranslate($text, $source, $target, $cacheKey);
        }
    }
    
    /**
     * Batch translate untuk multiple texts
     */
    public function batchTranslate(Request $request)
    {
        $request->validate([
            'texts' => 'required|array',
            'texts.*' => 'required|string',
            'source' => 'required|string|size:2',
            'target' => 'required|string|size:2',
        ]);

        $texts = $request->input('texts');
        $source = $request->input('source', 'ar');
        $target = $request->input('target', 'id');
        
        try {
            $results = [];
            
            foreach ($texts as $key => $text) {
                if ($source === 'ar' && $target === 'id') {
                    $translation = $this->googleTranslateService->translateArabicToIndonesian($text);
                } else {
                    $translation = $this->googleTranslateService->translateWithAutoDetect($text, $target);
                }
                
                $results[$key] = [
                    'original' => $text,
                    'translation' => $translation ?: $this->getLocalTranslation($text)
                ];
            }
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'method' => 'google_batch'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Batch translation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Batch translation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Gunakan kamus lokal untuk terjemahan fallback
     */
    private function mockTranslate($text, $source, $target, $cacheKey)
    {
        Log::info('Using mock translation for: ' . $text);
        
        $translation = $this->getLocalTranslation($text);
        
        if (!$translation) {
            $translation = "Terjemahan untuk: " . $text;
        }
        
        // Simpan ke cache
        Cache::put($cacheKey, $translation, now()->addDays(7));
        
        return response()->json([
            'success' => true,
            'translation' => $translation,
            'source' => $source,
            'target' => $target,
            'method' => 'mock'
        ]);
    }
    
    /**
     * Ambil terjemahan dari kamus lokal
     */
    private function getLocalTranslation($text)
    {
        $mockTranslations = [
            'كَتَبَ' => 'menulis (dia lk)',
            'يَكْتُبُ' => 'sedang menulis (dia lk)',
            'اُكْتُبْ' => 'tulislah!',
            'كَتَبَتْ' => 'menulis (dia pr)',
            'تَكْتُبُ' => 'sedang menulis (dia pr)',
            'كَتَبْتُ' => 'saya menulis',
            'أَكْتُبُ' => 'saya sedang menulis',
            'كَتَبْنَا' => 'kami menulis',
            'نَكْتُبُ' => 'kami sedang menulis',
            'الفعل' => 'kata kerja',
            'قَرَأَ' => 'membaca',
            'يَقْرَأُ' => 'sedang membaca',
            'اِقْرَأْ' => 'bacalah!',
            'سَلَّمَ' => 'menyerahkan/mengucapkan salam',
            'يُسَلِّمُ' => 'sedang menyerahkan/mengucapkan salam',
            'سَلِّمْ' => 'serahkanlah!/ucapkan salam!',
            'ضَرَبَ' => 'memukul',
            'يَضْرِبُ' => 'sedang memukul',
            'اِضْرِبْ' => 'pukullah!',
            'اِشْتَغَلَ' => 'bekerja/sibuk dengan',
            'يَشْتَغِلُ' => 'sedang bekerja/sibuk dengan',
            'اِشْتَغِلْ' => 'bekerjalah!/sibukkan diri dengan!',
            'فعل' => 'kata kerja',
            'فعل ثلاثي' => 'kata kerja berwazan ثلاثي',
            'فعل خماسي' => 'kata kerja berwazan خماسي (5 huruf)',
            'متعدي' => 'transitif (membutuhkan objek)',
            'لازم' => 'intransitif (tidak membutuhkan objek)',
            'صحيح' => 'kata kerja tanpa huruf illat',
            'سالم' => 'kata kerja dengan huruf yang selamat'
        ];
        
        // Cari terjemahan langsung
        if (isset($mockTranslations[$text])) {
            return $mockTranslations[$text];
        }
        
        // Cari sebagian
        foreach ($mockTranslations as $key => $value) {
            if (strpos($text, $key) !== false) {
                return $value . ' (dalam konteks)';
            }
        }
        
        return null;
    }
    
    /**
     * Cek status Google Translate
     */
    public function checkApi(Request $request)
    {
        try {
            $result = $this->googleTranslateService->translateArabicToIndonesian('السلام عليكم');
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'api_status' => 'working',
                    'service' => 'Google Translate (Stichoza)',
                    'sample_result' => $result
                ]);
            }
            
            return response()->json([
                'success' => false,
                'api_status' => 'error',
                'message' => 'Google Translate returned no result'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking Google Translate',
                'error' => $e->getMessage()
            ]);
        }
    }
}