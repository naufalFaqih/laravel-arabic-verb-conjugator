<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function searchVerb(Request $request)
    {
        $verb = $request->query('verb'); // Ambil parameter 'verb' dari request

        if (!$verb) {
            return response()->json(['error' => 'Parameter verb is required'], 400);
        }

        // URL API eksternal
        $apiUrl = "http://qutrub.arabeyes.org/api?verb=" . urlencode($verb);

        try {
            // Kirim request ke API eksternal
            $response = Http::get($apiUrl);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to fetch data from external API'], 500);
            }

            return response()->json($response->json()); // Kembalikan hasil API ke frontend
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}