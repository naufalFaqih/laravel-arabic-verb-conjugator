<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchTranslateRequest;
use App\Http\Requests\TranslateRequest;
use App\Services\DeepSeekTranslator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Thin HTTP layer over {@see DeepSeekTranslator}. All translation
 * logic, prompt-building, caching and fallback live in the service.
 */
class TranslationController extends Controller
{
    public function __construct(private readonly DeepSeekTranslator $translator)
    {
    }

    /**
     * POST /api/translate
     */
    public function translate(TranslateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $text = (string) ($validated['text'] ?? '');
        $source = (string) ($validated['source'] ?? 'ar');
        $target = (string) ($validated['target'] ?? 'id');
        $force = (bool) ($validated['force'] ?? false);

        Log::info('Translation request', compact('text', 'source', 'target', 'force'));

        $result = $this->translator->translate($text, $source, $target, $force);

        return response()->json($result);
    }

    /**
     * POST /api/translate/check
     */
    public function checkApi(Request $request): JsonResponse
    {
        return response()->json($this->translator->check());
    }

    /**
     * POST /api/translate/batch
     */
    public function batchTranslate(BatchTranslateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $source = (string) ($validated['source'] ?? 'ar');
        $target = (string) ($validated['target'] ?? 'id');

        $results = $this->translator->batch(
            (array) $validated['texts'],
            $source,
            $target,
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'source' => $source,
            'target' => $target,
        ]);
    }
}
