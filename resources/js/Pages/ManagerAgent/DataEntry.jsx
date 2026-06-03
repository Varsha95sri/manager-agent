// resources/js/Pages/ManagerAgent/DataEntry.jsx
import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function DataEntry({ teamMembers = [] }) {
    const [activeTab, setActiveTab] = useState('members');
    const { flash } = usePage().props;

    // 1. Team Members Form
    const memberForm = useForm({
        name: '',
        email: '',
        role: '',
    });

    const submitMember = (e) => {
        e.preventDefault();
        memberForm.post('/manager-agent/team-member', {
            onSuccess: () => memberForm.reset(),
        });
    };

    // 2. Tasks Form
    const taskForm = useForm({
        team_member_id: '',
        title: '',
        status: 'pending',
        due_date: '',
    });

    const submitTask = (e) => {
        e.preventDefault();
        taskForm.post('/manager-agent/task', {
            onSuccess: () => taskForm.reset(),
        });
    };

    // 3. Git Commits Form
    const commitForm = useForm({
        team_member_id: '',
        commit_hash: '',
        message: '',
        committed_at: '',
    });

    const submitCommit = (e) => {
        e.preventDefault();
        commitForm.post('/manager-agent/commit', {
            onSuccess: () => commitForm.reset(),
        });
    };

    // 4. Attendance Form
    const attendanceForm = useForm({
        team_member_id: '',
        date: '',
        status: 'present',
        check_in: '',
    });

    const submitAttendance = (e) => {
        e.preventDefault();
        attendanceForm.post('/manager-agent/attendance', {
            onSuccess: () => attendanceForm.reset(),
        });
    };

    // 5. Meeting Notes Form
    const meetingForm = useForm({
        title: '',
        notes: '',
        meeting_date: '',
    });

    const submitMeeting = (e) => {
        e.preventDefault();
        meetingForm.post('/manager-agent/meeting', {
            onSuccess: () => meetingForm.reset(),
        });
    };

    const tabs = [
        { id: 'members', label: 'Team Members' },
        { id: 'tasks', label: 'Tasks' },
        { id: 'commits', label: 'Git Commits' },
        { id: 'attendance', label: 'Attendance Logs' },
        { id: 'meetings', label: 'Meeting Notes' },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Manager Agent - Data Entry" />

            <div class="space-y-6 max-w-4xl mx-auto animate-fade-in-up">
                {/* Header */}
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white font-outfit">
                        Manual Data Logger
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">
                        Log mock team details and activities to simulate the daily database states.
                    </p>
                </div>

                {/* Tab Navigation buttons */}
                <div class="flex flex-wrap gap-2 border-b border-slate-800 pb-3">
                    {tabs.map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            class={`px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 ${
                                activeTab === tab.id
                                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20'
                                    : 'text-slate-400 hover:text-slate-200 bg-slate-900/40 hover:bg-slate-800/40 border border-slate-800/60'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>

                {/* Light Theme Form Card Container */}
                <div class="bg-slate-50 text-slate-900 border border-slate-200 rounded-3xl p-8 shadow-2xl relative overflow-hidden transition-all duration-300">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500"></div>

                    {/* Tab 1: Team Members */}
                    {activeTab === 'members' && (
                        <form onSubmit={submitMember} class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-800 font-outfit pb-2 border-b border-slate-200/80">Add Team Member</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Full Name</label>
                                    <input
                                        type="text"
                                        value={memberForm.data.name}
                                        onChange={(e) => memberForm.setData('name', e.target.value)}
                                        placeholder="e.g. Rahul Kumar"
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    />
                                    {memberForm.errors.name && <span class="text-xs text-rose-500 font-medium">{memberForm.errors.name}</span>}
                                </div>

                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                                    <input
                                        type="email"
                                        value={memberForm.data.email}
                                        onChange={(e) => memberForm.setData('email', e.target.value)}
                                        placeholder="e.g. rahul@company.com"
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    />
                                    {memberForm.errors.email && <span class="text-xs text-rose-500 font-medium">{memberForm.errors.email}</span>}
                                </div>
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Designated Role</label>
                                <input
                                    type="text"
                                    value={memberForm.data.role}
                                    onChange={(e) => memberForm.setData('role', e.target.value)}
                                    placeholder="e.g. Backend Dev, Designer, DevOps"
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {memberForm.errors.role && <span class="text-xs text-rose-500 font-medium">{memberForm.errors.role}</span>}
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={memberForm.processing}
                                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Add Member
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Tab 2: Tasks */}
                    {activeTab === 'tasks' && (
                        <form onSubmit={submitTask} class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-800 font-outfit pb-2 border-b border-slate-200/80">Log Task Assignment</h2>
                            
                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Team Member</label>
                                <select
                                    value={taskForm.data.team_member_id}
                                    onChange={(e) => taskForm.setData('team_member_id', e.target.value)}
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Select a member...</option>
                                    {teamMembers.map((m) => (
                                        <option key={m.id} value={m.id}>{m.name} ({m.role})</option>
                                    ))}
                                </select>
                                {taskForm.errors.team_member_id && <span class="text-xs text-rose-500 font-medium">{taskForm.errors.team_member_id}</span>}
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Task Title / Description</label>
                                <input
                                    type="text"
                                    value={taskForm.data.title}
                                    onChange={(e) => taskForm.setData('title', e.target.value)}
                                    placeholder="e.g. Implement Oauth endpoints"
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {taskForm.errors.title && <span class="text-xs text-rose-500 font-medium">{taskForm.errors.title}</span>}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</label>
                                    <select
                                        value={taskForm.data.status}
                                        onChange={(e) => taskForm.setData('status', e.target.value)}
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    {taskForm.errors.status && <span class="text-xs text-rose-500 font-medium">{taskForm.errors.status}</span>}
                                </div>

                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Due Date</label>
                                    <input
                                        type="date"
                                        value={taskForm.data.due_date}
                                        onChange={(e) => taskForm.setData('due_date', e.target.value)}
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    />
                                    {taskForm.errors.due_date && <span class="text-xs text-rose-500 font-medium">{taskForm.errors.due_date}</span>}
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={taskForm.processing}
                                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Log Task
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Tab 3: Git Commits */}
                    {activeTab === 'commits' && (
                        <form onSubmit={submitCommit} class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-800 font-outfit pb-2 border-b border-slate-200/80">Log Git Commit</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Author Member</label>
                                    <select
                                        value={commitForm.data.team_member_id}
                                        onChange={(e) => commitForm.setData('team_member_id', e.target.value)}
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    >
                                        <option value="">Select a member...</option>
                                        {teamMembers.map((m) => (
                                            <option key={m.id} value={m.id}>{m.name} ({m.role})</option>
                                        ))}
                                    </select>
                                    {commitForm.errors.team_member_id && <span class="text-xs text-rose-500 font-medium">{commitForm.errors.team_member_id}</span>}
                                </div>

                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Commit Hash</label>
                                    <input
                                        type="text"
                                        value={commitForm.data.commit_hash}
                                        onChange={(e) => commitForm.setData('commit_hash', e.target.value)}
                                        placeholder="e.g. 7f4a21d"
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    />
                                    {commitForm.errors.commit_hash && <span class="text-xs text-rose-500 font-medium">{commitForm.errors.commit_hash}</span>}
                                </div>
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Commit Message</label>
                                <input
                                    type="text"
                                    value={commitForm.data.message}
                                    onChange={(e) => commitForm.setData('message', e.target.value)}
                                    placeholder="e.g. feat: core api integrations for manager layout"
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {commitForm.errors.message && <span class="text-xs text-rose-500 font-medium">{commitForm.errors.message}</span>}
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Committed At (DateTime)</label>
                                <input
                                    type="datetime-local"
                                    value={commitForm.data.committed_at}
                                    onChange={(e) => commitForm.setData('committed_at', e.target.value)}
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {commitForm.errors.committed_at && <span class="text-xs text-rose-500 font-medium">{commitForm.errors.committed_at}</span>}
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={commitForm.processing}
                                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Log Commit
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Tab 4: Attendance */}
                    {activeTab === 'attendance' && (
                        <form onSubmit={submitAttendance} class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-800 font-outfit pb-2 border-b border-slate-200/80">Log Attendance Record</h2>
                            
                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Team Member</label>
                                <select
                                    value={attendanceForm.data.team_member_id}
                                    onChange={(e) => attendanceForm.setData('team_member_id', e.target.value)}
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Select a member...</option>
                                    {teamMembers.map((m) => (
                                        <option key={m.id} value={m.id}>{m.name} ({m.role})</option>
                                    ))}
                                </select>
                                {attendanceForm.errors.team_member_id && <span class="text-xs text-rose-500 font-medium">{attendanceForm.errors.team_member_id}</span>}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Attendance Date</label>
                                    <input
                                        type="date"
                                        value={attendanceForm.data.date}
                                        onChange={(e) => attendanceForm.setData('date', e.target.value)}
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    />
                                    {attendanceForm.errors.date && <span class="text-xs text-rose-500 font-medium">{attendanceForm.errors.date}</span>}
                                </div>

                                <div class="flex flex-col space-y-1.5">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</label>
                                    <select
                                        value={attendanceForm.data.status}
                                        onChange={(e) => attendanceForm.setData('status', e.target.value)}
                                        class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    >
                                        <option value="present">Present</option>
                                        <option value="late">Late</option>
                                        <option value="absent">Absent</option>
                                    </select>
                                    {attendanceForm.errors.status && <span class="text-xs text-rose-500 font-medium">{attendanceForm.errors.status}</span>}
                                </div>
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Check-in Time (Optional)</label>
                                <input
                                    type="time"
                                    value={attendanceForm.data.check_in}
                                    onChange={(e) => attendanceForm.setData('check_in', e.target.value)}
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {attendanceForm.errors.check_in && <span class="text-xs text-rose-500 font-medium">{attendanceForm.errors.check_in}</span>}
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={attendanceForm.processing}
                                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Log Attendance
                                </button>
                            </div>
                        </form>
                    )}

                    {/* Tab 5: Meeting Notes */}
                    {activeTab === 'meetings' && (
                        <form onSubmit={submitMeeting} class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-800 font-outfit pb-2 border-b border-slate-200/80">Log Meeting Summary</h2>
                            
                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Meeting Title</label>
                                <input
                                    type="text"
                                    value={meetingForm.data.title}
                                    onChange={(e) => meetingForm.setData('title', e.target.value)}
                                    placeholder="e.g. Daily standup status sync"
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {meetingForm.errors.title && <span class="text-xs text-rose-500 font-medium">{meetingForm.errors.title}</span>}
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Meeting Date</label>
                                <input
                                    type="date"
                                    value={meetingForm.data.meeting_date}
                                    onChange={(e) => meetingForm.setData('meeting_date', e.target.value)}
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                />
                                {meetingForm.errors.meeting_date && <span class="text-xs text-rose-500 font-medium">{meetingForm.errors.meeting_date}</span>}
                            </div>

                            <div class="flex flex-col space-y-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Discussion Notes & Decisions</label>
                                <textarea
                                    value={meetingForm.data.notes}
                                    onChange={(e) => meetingForm.setData('notes', e.target.value)}
                                    rows="5"
                                    placeholder="Summarize key talking points, progress, blockages..."
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                ></textarea>
                                {meetingForm.errors.notes && <span class="text-xs text-rose-500 font-medium">{meetingForm.errors.notes}</span>}
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button
                                    type="submit"
                                    disabled={meetingForm.processing}
                                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                                >
                                    Log Meeting Note
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
