<?php
// app/Http/Controllers/ChatbotController.php

namespace App\Http\Controllers;

use App\Services\ChatbotAgentService;
use App\Models\AiInsight;
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
        $insights = AiInsight::latest()->take(5)->get();
        return view('manager.chatbot', compact('chatHistory', 'insights'));
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
        
        $chatHistory = session('chat_history', []);

        // Retrieve response from the chatbot service
        $answer = $this->chatbotService->answerQuestion($question, $chatHistory);

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

    /**
     * Post a question to the AI chatbot for an employee.
     */
    public function userAsk(Request $request)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');

        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = $request->input('question');
        
        $employeeEmail = auth()->user()->email;
        $employee = \App\Models\TeamMember::where('email', $employeeEmail)->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Your profile is not linked to any employee record.',
            ], 403);
        }

        $chatHistory = session('user_chat_history', []);

        $answer = $this->chatbotService->answerEmployeeQuestion($question, $employee, $chatHistory);

        $chatHistory = session('user_chat_history', []);
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

        session(['user_chat_history' => $chatHistory]);

        return response()->json([
            'success' => true,
            'user' => $userMsg,
            'assistant' => $assistantMsg,
        ]);
    }

    /**
     * Clear the employee chatbot conversation history.
     */
    public function userClear(Request $request)
    {
        session()->forget('user_chat_history');

        return response()->json([
            'success' => true,
            'message' => 'Conversation history cleared successfully!'
        ]);
    }
}
