// resources/js/Pages/ManagerAgent/Dashboard.jsx
import React, { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard({ reports = [], totalMembers = 0 }) {
    const [generating, setGenerating] = useState(false);
    const [reportExpanded, setReportExpanded] = useState(true);

    const latestReport = reports[0] || null;
    const totalReports = reports.length;

    // Helper to format date
    const formatDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    };

    // Calculate circular chart progress values
    const pct = latestReport ? latestReport.team_productivity : 0;
    const radius = 50;
    const circ = 2 * Math.PI * radius; // 314.159
    const strokePct = ((100 - pct) / 100) * circ;

    // Color code based on productivity percent
    const getProductivityColor = (value) => {
        if (value >= 80) return 'text-emerald-400 stroke-emerald-500';
        if (value >= 60) return 'text-amber-400 stroke-amber-500';
        return 'text-rose-400 stroke-rose-500';
    };

    const handleGenerate = () => {
        setGenerating(true);
        router.post('/manager-agent/generate', {}, {
            onFinish: () => {
                setGenerating(false);
            }
        });
    };

    // Format list values (handling json/string array parsing safety)
    const renderListItems = (items, type) => {
        if (!items) return <p class="text-slate-500 text-sm italic">None identified.</p>;
        const list = Array.isArray(items) ? items : [];
        if (list.length === 0) return <p class="text-slate-500 text-sm italic">None identified.</p>;

        return (
            <ul class="space-y-3.5">
                {list.map((item, idx) => (
                    <li key={idx} class="flex items-start space-x-3 text-sm text-slate-300">
                        {type === 'top' && (
                            <div class="flex-shrink-0 mt-0.5 flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500/10 text-emerald-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        )}
                        {type === 'attention' && (
                            <div class="flex-shrink-0 mt-0.5 flex items-center justify-center w-5 h-5 rounded-full bg-rose-500/10 text-rose-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        )}
                        {type === 'risks' && (
                            <div class="flex-shrink-0 mt-1 flex items-center justify-center w-2 h-2 rounded-full bg-amber-500 shadow-md shadow-amber-500/50"></div>
                        )}
                        
                        <div class="flex-1">
                            <span class="font-medium text-slate-200">{item}</span>
                            {type === 'top' && (
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-2.5 h-2.5 mr-0.5">
                                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                                    </svg>
                                    Star
                                </span>
                            )}
                        </div>
                    </li>
                ))}
            </ul>
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Manager Agent Dashboard" />

            <div class="space-y-8 animate-fade-in-up">
                {/* Header Section */}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-white font-outfit">
                            Performance Dashboard
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            AI-generated insights and automated daily productivity reviews.
                        </p>
                    </div>

                    <button
                        onClick={handleGenerate}
                        disabled={generating}
                        class="relative inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 hover:from-indigo-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-300 shadow-lg shadow-indigo-500/25 disabled:opacity-50 disabled:cursor-not-allowed group overflow-hidden"
                    >
                        {generating ? (
                            <>
                                <svg class="animate-spin -ml-1 mr-2.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Analyzing team data...
                            </>
                        ) : (
                            <>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform">
                                    <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 6.012l-1.17 1.169a.75.75 0 01-1.061-1.061l1.17-1.17a5.5 5.5 0 016.012-9.2l3.235-3.236a1 1 0 011.414 0l1.586 1.586a1 1 0 010 1.414l-3.236 3.236zm-8.816 5.57a3.999 3.999 0 105.657-5.656 4 4 0 00-5.657 5.656z" clip-rule="evenodd" />
                                </svg>
                                Generate Evening Report
                            </>
                        )}
                    </button>
                </div>

                {/* Stats Row */}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    {/* Stat Card 1 */}
                    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 shadow-xl hover:border-slate-700/60 transition-all duration-300">
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Team Productivity</span>
                        <div class="flex items-baseline mt-2 space-x-1.5">
                            <span class={`text-3xl font-bold ${latestReport ? getProductivityColor(pct).split(' ')[0] : 'text-slate-400'}`}>
                                {latestReport ? `${pct}%` : 'N/A'}
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-medium">Daily average index score</p>
                    </div>

                    {/* Stat Card 2 */}
                    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 shadow-xl hover:border-slate-700/60 transition-all duration-300">
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Members</span>
                        <div class="flex items-baseline mt-2">
                            <span class="text-3xl font-bold text-slate-100">{totalMembers}</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-medium">Active registered resources</p>
                    </div>

                    {/* Stat Card 3 */}
                    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 shadow-xl hover:border-slate-700/60 transition-all duration-300">
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Reports Logged</span>
                        <div class="flex items-baseline mt-2">
                            <span class="text-3xl font-bold text-slate-100">{totalReports}</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-medium">Archived historical data</p>
                    </div>

                    {/* Stat Card 4 */}
                    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 shadow-xl hover:border-slate-700/60 transition-all duration-300">
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Last Sync Date</span>
                        <div class="flex items-baseline mt-2">
                            <span class="text-lg font-bold text-slate-100 truncate w-full">
                                {latestReport ? formatDate(latestReport.report_date) : 'No reports'}
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 font-medium">Latest analytical record</p>
                    </div>
                </div>

                {latestReport ? (
                    /* Latest Report Details */
                    <div class="space-y-6">
                        <div class="flex items-center space-x-2.5">
                            <div class="h-1.5 w-1.5 rounded-full bg-indigo-500"></div>
                            <h2 class="text-xl font-bold text-white font-outfit">Latest AI Evaluation Insights</h2>
                        </div>

                        {/* Top layout: circular chart and performing grids */}
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            {/* Productivity Gauge Card */}
                            <div class="lg:col-span-4 backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl p-6 flex flex-col items-center justify-center shadow-xl">
                                <span class="text-sm text-slate-400 font-semibold mb-6">Productivity Index</span>
                                
                                <div class="relative w-44 h-44 flex items-center justify-center">
                                    {/* SVG Ring */}
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 120 120">
                                        {/* Background circle */}
                                        <circle
                                            cx="60"
                                            cy="60"
                                            r={radius}
                                            class="stroke-slate-800"
                                            stroke-width="10"
                                            fill="transparent"
                                        />
                                        {/* Colored value circle */}
                                        <circle
                                            cx="60"
                                            cy="60"
                                            r={radius}
                                            class={`transition-all duration-1000 ease-out ${getProductivityColor(pct).split(' ')[1]}`}
                                            stroke-width="10"
                                            stroke-dasharray={circ}
                                            stroke-dashoffset={strokePct}
                                            stroke-linecap="round"
                                            fill="transparent"
                                        />
                                    </svg>
                                    {/* Score Text */}
                                    <div class="absolute flex flex-col items-center justify-center">
                                        <span class="text-4xl font-extrabold text-white font-outfit">{pct}%</span>
                                        <span class="text-[10px] font-semibold tracking-wider text-slate-500 uppercase mt-0.5">Rating</span>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400 mt-6 font-medium text-center">
                                    {pct >= 80 ? '🟢 Strong Team Momentum' : pct >= 60 ? '🟡 Moderate Output' : '🔴 Alert: High Blockers Found'}
                                </span>
                            </div>

                            {/* Top Performers and Attention Grids */}
                            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Top Performers Card */}
                                <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl p-6 shadow-xl hover:border-slate-800 transition-all duration-300">
                                    <h3 class="text-base font-bold text-white mb-4 flex items-center">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 shadow-lg shadow-emerald-500/50"></span>
                                        Top Performers
                                    </h3>
                                    <div class="mt-4">
                                        {renderListItems(latestReport.top_performers, 'top')}
                                    </div>
                                </div>

                                {/* Attention Required Card */}
                                <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl p-6 shadow-xl hover:border-slate-800 transition-all duration-300">
                                    <h3 class="text-base font-bold text-white mb-4 flex items-center">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2 shadow-lg shadow-rose-500/50"></span>
                                        Attention Required
                                    </h3>
                                    <div class="mt-4">
                                        {renderListItems(latestReport.attention_required, 'attention')}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Middle layout: Risks Card */}
                        <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl p-6 shadow-xl">
                            <h3 class="text-base font-bold text-white mb-4 flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2 shadow-lg shadow-amber-500/50"></span>
                                Identified Risks & Roadblocks
                            </h3>
                            <div class="mt-4">
                                {renderListItems(latestReport.risks, 'risks')}
                            </div>
                        </div>

                        {/* Full Narrative AI Report */}
                        <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl p-6 shadow-xl overflow-hidden">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-800/50">
                                <h3 class="text-base font-bold text-white flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2 text-indigo-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Complete Narrative Review
                                </h3>
                                <button
                                    onClick={() => setReportExpanded(!reportExpanded)}
                                    class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors"
                                >
                                    {reportExpanded ? 'Collapse' : 'Expand'}
                                </button>
                            </div>

                            {reportExpanded && (
                                <div class="mt-5 text-sm text-slate-300 leading-relaxed font-normal whitespace-pre-wrap max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                    {latestReport.full_report}
                                </div>
                            )}
                        </div>
                    </div>
                ) : (
                    /* Empty State when no reports logged */
                    <div class="backdrop-blur-md bg-slate-900/20 border border-dashed border-slate-800 rounded-3xl py-20 flex flex-col items-center justify-center text-center shadow-xl">
                        <div class="w-16 h-16 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-center text-indigo-500 mb-5 shadow-lg shadow-indigo-500/5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-200">No Reports Logged Yet</h3>
                        <p class="text-sm text-slate-500 max-w-sm mt-1.5">
                            Click the button in the top right to analyze recent tasks, git commits, and standup notes.
                        </p>
                    </div>
                )}

                {/* Report History Section */}
                <div class="space-y-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="h-1.5 w-1.5 rounded-full bg-indigo-500"></div>
                        <h2 class="text-xl font-bold text-white font-outfit">Report History</h2>
                    </div>

                    <div class="backdrop-blur-md bg-slate-900/40 border border-slate-800/80 rounded-3xl overflow-hidden shadow-xl">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800/60">
                                <thead class="bg-slate-900/60">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Report Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Productivity Index</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40 bg-slate-950/20">
                                    {reports.map((report, idx) => (
                                        <tr key={report.id} class="hover:bg-slate-900/25 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-200">
                                                {formatDate(report.report_date)}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <span class={`text-sm font-bold ${getProductivityColor(report.team_productivity).split(' ')[0]}`}>
                                                        {report.team_productivity}%
                                                    </span>
                                                    {/* Small progress bar */}
                                                    <div class="w-16 h-1.5 bg-slate-800 rounded-full overflow-hidden hidden sm:block">
                                                        <div
                                                            class={`h-full rounded-full ${
                                                                report.team_productivity >= 80 ? 'bg-emerald-500' :
                                                                report.team_productivity >= 60 ? 'bg-amber-500' :
                                                                'bg-rose-500'
                                                            }`}
                                                            style={{ width: `${report.team_productivity}%` }}
                                                        ></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    report.team_productivity >= 80 ? 'bg-emerald-500/10 text-emerald-400' :
                                                    report.team_productivity >= 60 ? 'bg-amber-500/10 text-amber-400' :
                                                    'bg-rose-500/10 text-rose-400'
                                                }`}>
                                                    {report.team_productivity >= 80 ? 'Stable' : report.team_productivity >= 60 ? 'Warning' : 'Critical'}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <button
                                                    onClick={() => {
                                                        // Update state to make this report active (mocked as swapping first index or trigger scroll)
                                                        window.scrollTo({ top: 150, behavior: 'smooth' });
                                                    }}
                                                    class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors"
                                                >
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    {reports.length === 0 && (
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500 italic">
                                                No historical performance reports logged.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
