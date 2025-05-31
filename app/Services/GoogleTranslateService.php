<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleTranslateService
{
    protected $translator;
    
    public function __construct()
    {
        $this->translator = new GoogleTranslate();
        
        // Set bahasa target default ke Indonesia
        $this->translator->setTarget('id');
    }
    
    /**
     * Terjemahkan teks dari bahasa Arab ke Indonesia
     */
    public function translateArabicToIndonesian($text)
    {
        try {
            // Validasi input
            if (empty($text) || $text === '-') {
                return null;
            }
            
            // Buat cache key
            $cacheKey = 'google_translate_' . md5($text);
            
            // Cek cache terlebih dahulu
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                Log::info('Using cached Google translation', [
                    'text' => $text,
                    'translation' => $cached
                ]);
                return $cached;
            }
            
            // Set bahasa sumber ke Arab
            $this->translator->setSource('ar');
            $this->translator->setTarget('id');
            
            // Lakukan terjemahan
            $translation = $this->translator->translate($text);
            
            // Validasi hasil terjemahan
            if ($translation && $translation !== $text) {
                // Simpan ke cache selama 7 hari
                Cache::put($cacheKey, $translation, now()->addDays(7));
                
                Log::info('Google translation success', [
                    'text' => $text,
                    'translation' => $translation
                ]);
                
                return $translation;
            }
            
            Log::warning('Google translation returned same text', [
                'text' => $text,
                'translation' => $translation
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Google translation failed', [
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Terjemahkan dengan deteksi bahasa otomatis
     */
    public function translateWithAutoDetect($text, $targetLang = 'id')
    {
        try {
            if (empty($text) || $text === '-') {
                return null;
            }
            
            $cacheKey = 'google_translate_auto_' . md5($text . $targetLang);
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Set target language
            $this->translator->setTarget($targetLang);
            
            // Auto-detect source language
            $this->translator->setSource(null);
            
            $translation = $this->translator->translate($text);
            
            if ($translation && $translation !== $text) {
                Cache::put($cacheKey, $translation, now()->addDays(7));
                return $translation;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Google auto-detect translation failed', [
                'text' => $text,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Batch translate multiple texts
     */
    public function batchTranslate(array $texts)
    {
        $results = [];
        
        foreach ($texts as $key => $text) {
            $results[$key] = $this->translateArabicToIndonesian($text);
        }
        
        return $results;
    }
}