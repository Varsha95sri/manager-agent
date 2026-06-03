// resources/js/Layouts/AuthenticatedLayout.jsx
import React from 'react';
import { Link, usePage, router } from '@inertiajs/react';

export default function AuthenticatedLayout({ children }) {
    const { auth, flash, url } = usePage().props;
    const user = auth?.user;

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div class="min-h-screen bg-[#090d16] text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
            {/* Navigation Header */}
            <nav class="bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        {/* Logo and Brand */}
                        <div class="flex items-center space-x-8">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21L14.907 18M18 10.5c0 3.202-2.155 5.901-5.113 6.786m5.113-6.786A7.5 7.5 0 105.106 17.785m12.893-7.285A7.494 7.494 0 0012 3c-1.39 0-2.697.378-3.82 1.036M12 3a7.494 7.494 0 00-3.82 1.036m0 0a7.498 7.498 0 0110.82 10.82M8.18 8.18l8.64 8.64" />
                                    </svg>
                                </div>
                                <span class="font-extrabold tracking-tight text-xl bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent font-outfit">
                                    Manager Agent
                                </span>
                            </div>

                            {/* Nav Links */}
                            <div class="hidden md:flex space-x-1">
                                <Link
                                    href="/manager-agent"
                                    class={`px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
                                        url === '/manager-agent'
                                            ? 'bg-slate-800 text-white font-semibold shadow-inner border border-slate-700/50'
                                            : 'text-slate-400 hover:text-white hover:bg-slate-800/40'
                                    }`}
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    href="/manager-agent/data-entry"
                                    class={`px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
                                        url === '/manager-agent/data-entry'
                                            ? 'bg-slate-800 text-white font-semibold shadow-inner border border-slate-700/50'
                                            : 'text-slate-400 hover:text-white hover:bg-slate-800/40'
                                    }`}
                                >
                                    Data Entry
                                </Link>
                            </div>
                        </div>

                        {/* User Profile / Logout */}
                        <div class="flex items-center space-x-4">
                            {user && (
                                <div class="hidden sm:flex flex-col text-right">
                                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Manager</span>
                                    <span class="text-sm font-medium text-slate-300">{user.name}</span>
                                </div>
                            )}
                            <button
                                onClick={handleLogout}
                                class="px-3.5 py-1.5 rounded-lg border border-slate-700 bg-slate-900/60 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white hover:border-slate-600 transition-all duration-200"
                            >
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Flash Messages */}
            {flash?.success && (
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full animate-fade-in">
                    <div class="flex items-center justify-between p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-sm">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span>{flash.success}</span>
                        </div>
                    </div>
                </div>
            )}

            {flash?.error && (
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full animate-fade-in">
                    <div class="flex items-center justify-between p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-sm">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                            <span>{flash.error}</span>
                        </div>
                    </div>
                </div>
            )}

            {/* Main Content Area */}
            <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {children}
            </main>

            {/* Footer */}
            <footer class="mt-auto py-6 border-t border-slate-900 bg-slate-950/40 text-center text-xs text-slate-600">
                &copy; {new Date().getFullYear()} Manager Agent. Powered by Claude Sonnet.
            </footer>
        </div>
    );
}
