<?php

namespace App\Http\Controllers;

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
    public function translate(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string',
            'source' => 'nullable|string|in:ar,id,en',
            'target' => 'nullable|string|in:ar,id,en',
            'force' => 'nullable|boolean',
        ]);

        $text = (string) $request->input('text');
        $source = (string) $request->input('source', 'ar');
        $target = (string) $request->input('target', 'id');
        $force = (bool) $request->boolean('force');

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
    public function batchTranslate(Request $request): JsonResponse
    {
        $request->validate([
            'texts' => 'required|array',
            'texts.*' => 'required|string',
            'source' => 'nullable|string|in:ar,id,en',
            'target' => 'nullable|string|in:ar,id,en',
        ]);

        $results = $this->translator->batch(
            (array) $request->input('texts'),
            (string) $request->input('source', 'ar'),
            (string) $request->input('target', 'id'),
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'source' => $request->input('source', 'ar'),
            'target' => $request->input('target', 'id'),
        ]);
    }
}
