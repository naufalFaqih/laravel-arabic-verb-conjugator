<?php

namespace App\Http\Controllers;

use App\Services\VerbSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function searchVerb(Request $request): JsonResponse
    {
        $verb = (string) $request->query('verb', '');
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
