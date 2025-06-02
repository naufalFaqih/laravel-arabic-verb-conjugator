<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        $message = [
            ['role' => 'system',
            'content' => 'Kamu adalan asisten AI yang hanya menerjemahkan kata dari bahasa Arab ke bahasa Indonesia. Jangan menjawab pertanyaan lain selain terjemahan kata.'],
            ['role' => 'user','content' => $request->post('content')],
        ];

        $res = Http::withToken('sk-ad905c84a5b3455681cdd48513a6f00e') -> post('https://api.deepseek.com/v1/chat/completions', [
            'model' => 'deepseek-chat',
            'messages' => $message,
            'stream' => false,
        ]);

        return $res;
    }
}
