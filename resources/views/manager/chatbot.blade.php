@extends('layouts.manager')
<!-- resources/views/manager/chatbot.blade.php -->

@section('title', 'AI Management Chatbot - Manager Agent')
@section('page_title', 'AI Management Assistant')

@section('styles')
<style>
    .chat-container {
        height: 480px;
        overflow-y: auto;
        padding-right: 8px;
    }
    
    .chat-message {
        margin-bottom: 1.25rem;
        max-width: 80%;
        display: flex;
        flex-direction: column;
    }
    
    .message-bubble {
        padding: 0.875rem 1.125rem;
        border-radius: 16px;
        font-size: 0.875rem;
        line-height: 1.5;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .message-user {
        margin-left: auto;
        align-items: flex-end;
    }
    
    .message-user .message-bubble {
        background: linear-gradient(135deg, #a855f7, #6366f1);
        color: white;
        border-top-right-radius: 4px;
    }
    
    .message-assistant {
        margin-right: auto;
        align-items: flex-start;
    }
    
    .message-assistant .message-bubble {
        background-color: #0b0f19;
        color: #f1f5f9;
        border: 1px solid #334155;
        border-top-left-radius: 4px;
    }
    
    .message-time {
        font-size: 10px;
        color: #64748b;
        margin-top: 4px;
        font-weight: 500;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="row g-4 align-items-center mb-4">
            <div class="col-sm-8">
                <h2 class="h3 font-outfit text-white mb-0">AI Chat Assistant</h2>
                <p class="text-secondary small mb-0">Ask questions about team members, completed tasks, git commits, or check-ins.</p>
            </div>
            <div class="col-sm-4 text-sm-end">
                @if(count($chatHistory) > 0)
                    <form method="POST" action="{{ route('manager.chatbot.clear') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-3">
                            Clear Conversation
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card glass-card p-0 overflow-hidden shadow-2xl">
            <!-- Alert banner informing the manager about database integration -->
            <div class="bg-indigo-950 bg-opacity-25 border-bottom border-slate-800 p-3.5 d-flex align-items-center">
                <div class="bg-indigo-500 bg-opacity-10 text-indigo-400 rounded-circle d-flex align-items-center justify-content-center me-2.5" style="width: 28px; height: 28px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM8.228 11h-1.21a2 2 0 0 1 0-.008c-.001-.246.154-.986.714-1.62.42-.477 1.187-.978 2.502-.978.07 0 .141.001.211.003-.502.5-.838 1.171-.97 1.898-.103.568-.18 1.127-.247 1.705ZM6 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </div>
                <span class="text-indigo-300 small">Connected to live Database: Members, Commits, Attendance logs are loaded in prompt context.</span>
            </div>

            <!-- Chat Message Box -->
            <div class="card-body p-4">
                <div class="chat-container" id="chatContainer">
                    @forelse($chatHistory as $msg)
                        <div class="chat-message {{ $msg['role'] === 'user' ? 'message-user' : 'message-assistant' }}">
                            <div class="message-bubble">
                                {!! nl2br(e($msg['text'])) !!}
                            </div>
                            <div class="message-time">{{ $msg['time'] }}</div>
                        </div>
                    @empty
                        <div class="h-100 d-flex flex-column items-center justify-content-center text-center py-5">
                            <div class="bg-slate-900 border border-slate-800 text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-purple-400" viewBox="0 0 16 16">
                                    <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15Z"/>
                                </svg>
                            </div>
                            <h5 class="text-white">Ask anything about the team</h5>
                            <p class="text-secondary small mx-auto" style="max-width: 320px;">Try: <i>"Who is absent today?"</i>, <i>"List today's commits"</i> or <i>"How many tasks are completed?"</i></p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Entry Footer Form -->
            <div class="card-footer p-3 bg-slate-950 bg-opacity-30 border-top border-slate-800">
                <form method="POST" action="{{ route('manager.chatbot.ask') }}" id="chat-form">
                    @csrf
                    <div class="input-group">
                        <input
                            type="text"
                            name="question"
                            id="question-input"
                            class="form-control rounded-start-4 border-slate-700 bg-slate-900 text-white placeholder-secondary px-4 py-2.5 focus:outline-none"
                            placeholder="Type your question here..."
                            required
                            maxlength="1000"
                            autocomplete="off"
                        >
                        <button class="btn accent-btn rounded-end-4 px-4" type="submit" id="send-btn">
                            Ask AI
                        </button>
                    </div>
                    @error('question')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatContainer');
        if(container) {
            // Scroll to bottom on load
            container.scrollTop = container.scrollHeight;
        }

        const form = document.getElementById('chat-form');
        const sendBtn = document.getElementById('send-btn');
        if (form && sendBtn) {
            form.addEventListener('submit', function() {
                sendBtn.disabled = true;
                sendBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Thinking...
                `;
            });
        }
    });
</script>
@endsection
