<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchVerbRequest;
use App\Services\VerbSearchService;
use Illuminate\Http\JsonResponse;

/**
 * Thin HTTP layer over the external Qutrub conjugation API.
 */
class ApiController extends Controller
{
    public function __construct(private readonly VerbSearchService $verbSearch)
    {
    }

    /**
     * GET /api/search-verb?verb=...
     */
    public function searchVerb(SearchVerbRequest $request): JsonResponse
    {
        $verb = (string) $request->validated()['verb'];
        $result = $this->verbSearch->search($verb);

        if (! ($result['success'] ?? false)) {
            return response()->json(
                ['error' => $result['error'] ?? 'Unknown error'],
                $result['status'] ?? 500
            );
        }

        return response()->json($result['data'] ?? []);
    }
}
