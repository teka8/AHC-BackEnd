<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');
        $apiKey = trim(env('GEMINI_API_KEY', ''));

        if (!$apiKey) {
            return response()->json(['error' => 'API key not configured'], 500);
        }

        $contextConfig = config('ahc_context');
        
        if (!$contextConfig) {
            Log::error('AHC Context config is null. Config cache might be stale.');
            return response()->json(['error' => 'Configuration error: ahc_context is null. Run php artisan optimize:clear'], 500);
        }

        $systemInstruction = $contextConfig['system_instruction'] ?? '';
        $faqData = $contextConfig['faq'] ?? [];

        // Build context string from FAQ
        $contextString = "FAQ Knowledge Base:\n";
        foreach ($faqData as $category => $questions) {
            $contextString .= "\nCategory: $category\n";
            foreach ($questions as $q) {
                $contextString .= "Q: " . $q['question'] . "\nA: " . $q['answer'] . "\n";
            }
        }

        $fullPrompt = $systemInstruction . "\n\n" . $contextString . "\n\nUser Question: " . $userMessage;

        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullPrompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';
                return response()->json(['reply' => $botReply]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json(['error' => 'AI Service Error: ' . $response->body()], 502);
            }

        } catch (\Exception $e) {
            Log::error('Chat Controller Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
