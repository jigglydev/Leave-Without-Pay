@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dashboard</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Welcome back, {{ Auth::user()->name }}. Here's what's happening today.</p>
</div>

{{-- Stats Cards — 2 cards only --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">

    {{-- Total Employees --}}
    <a href="{{ route('admin.employees') }}"
       class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group block">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 rounded-full">View all →</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $employeeCount }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No. of All Leave Processors</p>
    </a>

    {{-- Recorded Entries This Month --}}
    <a href="{{ route('admin.recorded-entries') }}"
       class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group block">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/30 px-2 py-0.5 rounded-full">{{ now()->format('F') }}</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">
            @if(Auth::user()->isAdmin())
                {{ $recordedEntriesThisMonth }}
            @else
                {{ $myEntriesThisMonth }}
            @endif
        </p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            @if(Auth::user()->isAdmin())
                No. of Recorded Entries
            @else
                My Recorded Entries
            @endif
        </p>
    </a>

</div>

{{-- Recently Added Employees --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-800 dark:text-white text-base">Recently Added Leave Processors</h2>
        <a href="{{ route('admin.employees') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">View all →</a>
    </div>

    @if($recentEmployees->isEmpty())
        <div class="p-10 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">No leave processors yet</p>
            <p class="text-xs text-slate-400 mt-1">Leave Processors will appear here once they register.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Employee No.</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Position</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Office</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($recentEmployees as $emp)
                    @php
                        $displayName = $emp->full_name;
                        $initial     = strtoupper(substr($emp->given_name ?: $emp->name, 0, 1));
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        {{-- Employee Number --}}
                        <td class="px-5 py-4 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $emp->employee_number ?: '—' }}
                        </td>
                        {{-- Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-slate-100 whitespace-nowrap">{{ $displayName }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $emp->email }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Position --}}
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-xs">{{ $emp->position ?: '—' }}</td>
                        {{-- Office --}}
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-xs">{{ $emp->office ?: '—' }}</td>
                        {{-- Registered --}}
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap">{{ $emp->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
