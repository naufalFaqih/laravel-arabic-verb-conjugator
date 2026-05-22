<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle the incoming chat request, proxying to DeepSeek.
     */
    public function __invoke(ChatRequest $request)
    {
        $apiKey = (string) config('services.deepseek.api_key', '');
        $apiUrl = (string) config('services.deepseek.api_url', 'https://api.deepseek.com/v1/chat/completions');
        $model = (string) config('services.deepseek.model', 'deepseek-chat');

        if ($apiKey === '') {
            Log::warning('Chat request received but DEEPSEEK_API_KEY is not configured.');

            return response()->json([
                'success' => false,
                'message' => 'DeepSeek API key belum dikonfigurasi. Set DEEPSEEK_API_KEY di .env.',
            ], 503);
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'Kamu adalah asisten AI yang hanya menerjemahkan kata dari bahasa Arab ke bahasa Indonesia. Jangan menjawab pertanyaan lain selain terjemahan kata.',
            ],
            [
                'role' => 'user',
                'content' => (string) $request->input('content', $request->input('message', '')),
            ],
        ];

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post($apiUrl, [
                'model' => $model,
                'messages' => $messages,
                'stream' => false,
            ]);

        return $response;
    }
}
