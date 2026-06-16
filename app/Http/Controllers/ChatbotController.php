<?php
// app/Http/Controllers/ChatbotController.php

namespace App\Http\Controllers;

use App\Services\ChatbotAgentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected ChatbotAgentService $chatbotService;

    public function __construct(ChatbotAgentService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Display the chatbot conversation logs.
     */
    public function index(): View
    {
        // Get conversation logs from the session, defaults to empty array
        $chatHistory = session('chat_history', []);
        return view('manager.chatbot', compact('chatHistory'));
    }

    /**
     * Post a question to the AI chatbot.
     */
    public function ask(Request $request)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = $request->input('question');
        
        // Retrieve response from the chatbot service
        $answer = $this->chatbotService->answerQuestion($question);

        // Fetch existing history and append current exchange
        $chatHistory = session('chat_history', []);
        $userMsg = [
            'role' => 'user',
            'text' => $question,
            'time' => now()->format('h:i A'),
        ];
        $assistantMsg = [
            'role' => 'assistant',
            'text' => $answer,
            'time' => now()->format('h:i A'),
        ];
        $chatHistory[] = $userMsg;
        $chatHistory[] = $assistantMsg;

        // Store back to session
        session(['chat_history' => $chatHistory]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user' => $userMsg,
                'assistant' => $assistantMsg,
            ]);
        }

        return redirect()->route('manager.chatbot');
    }

    /**
     * Clear the chatbot conversation history.
     */
    public function clear(Request $request)
    {
        session()->forget('chat_history');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation history cleared successfully!'
            ]);
        }

        return redirect()->route('manager.chatbot')->with('success', 'Conversation history cleared successfully!');
    }
}
