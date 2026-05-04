@extends('admin.layout')
@section('title', 'Generated Letters')
@section('content')

@php
    $creators = $letters->pluck('createdByUser')->filter()->unique('id')->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name])->values();
@endphp

<div x-data="letterFilter()" @keydown.escape.window="open = false">

<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Generated Letters</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Storage for all created Tardy and Undertime letters.</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Search Bar --}}
        <div class="relative w-64 hidden sm:block">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search" @input="applyFilters()"
                   placeholder="Search reference, employee, type, position..."
                   class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                          bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100
                          focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm placeholder-slate-400"/>
            {{-- Clear Search --}}
            <button x-show="search" @click="search=''; applyFilters()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 transition-colors"
                    style="display:none;">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Filter button --}}
        <button @click="open = !open"
                class="relative flex items-center gap-2 px-4 py-2.5 rounded-xl border transition-all
                       font-semibold text-sm shadow-sm select-none
                       bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600
                       text-slate-700 dark:text-slate-200
                       hover:border-blue-400 dark:hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 4a1 1 0 000 2h18a1 1 0 000-2H3zm3 6a1 1 0 000 2h12a1 1 0 000-2H6zm3 6a1 1 0 000 2h6a1 1 0 000-2H9z"/>
            </svg>
            Filter
            {{-- Active count badge --}}
            <span x-show="activeCount > 0" x-text="activeCount"
                  class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-blue-600 text-white
                         text-[10px] font-bold flex items-center justify-center leading-none"
                  style="display:none"></span>
        </button>

        <a href="{{ route('admin.undertime-tardy.tardy-letter') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-xl shadow-sm shadow-amber-400/30 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Tardy Letter
        </a>
        <a href="{{ route('admin.undertime-tardy.undertime-letter') }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-blue-500/30 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Undertime Letter
        </a>
    </div>
</div>

{{-- Slide-out filter drawer --}}
<div x-show="open" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-x-4"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-4"
     style="display:none"
     class="fixed top-0 right-0 h-full w-80 z-40 shadow-2xl
            bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 overflow-y-auto">

    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 sticky top-0
                bg-white dark:bg-slate-800 z-10">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Filters</h3>
            <span x-show="activeCount > 0" x-text="activeCount"
                  class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold
                         flex items-center justify-center" style="display:none"></span>
        </div>
        <div class="flex items-center gap-3">
            <button @click="clearAll()"
                    class="text-xs font-semibold text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                Clear all
            </button>
            <button @click="open = false"
                    class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="p-5 space-y-6">

        {{-- Type --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Type
                </div>
                <button x-show="type" @click="type=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <select x-model="type" @change="applyFilters()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none pr-9">
                    <option value="">All Types</option>
                    <option value="tardy">Tardy</option>
                    <option value="undertime">Undertime</option>
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Created By --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Created By
                </div>
                <button x-show="createdBy" @click="createdBy=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <select x-model="createdBy" @change="applyFilters()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none pr-9">
                    <option value="">All users</option>
                    <template x-for="c in creators" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Position --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Position
                </div>
                <button x-show="position" @click="position=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <input type="text" x-model="position" @input="applyFilters()"
                   placeholder="e.g., Clerk, Auditor..."
                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
        </div>

        {{-- Month --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Month Saved
                </div>
                <button x-show="month" @click="month=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <select x-model="month" @change="applyFilters()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none pr-9">
                    <option value="">All months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Year --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Year Saved
                </div>
                <button x-show="year" @click="year=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <select x-model="year" @change="applyFilters()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none pr-9">
                    <option value="">All years</option>
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear; $y >= $currentYear - 10; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

    </div>
</div>

{{-- Backdrop --}}
<div x-show="open" @click="open = false"
     class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm"
     style="display:none"></div>

{{-- Table card --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">All Generated Letters</h2>
        <span class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
            {{ $letters->count() }} letter(s) total
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse" id="gl-table">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-700/40">
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[120px]">Reference No.</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[120px]">Type</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle">Employee</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle">Position / Office</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle">Month / Year</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle">Occurrences</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[120px]">Created By</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[200px]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="gl-tbody">

                @forelse($letters as $i => $letter)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors gl-row"
                    data-ref="{{ strtolower($letter->reference_no ?? '') }}"
                    data-name="{{ strtolower($letter->employee_name) }}"
                    data-position="{{ strtolower($letter->employee_position ?? '') }}"
                    data-office="{{ strtolower($letter->employee_office ?? '') }}"
                    data-type="{{ strtolower($letter->type) }}"
                    data-created-by="{{ $letter->created_by }}"
                    data-created-by-name="{{ strtolower($letter->createdByUser->full_name ?? '') }}"
                    data-month="{{ $letter->created_at->format('m') }}"
                    data-year="{{ $letter->created_at->format('Y') }}">

                    {{-- Reference No --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-center text-slate-500 dark:text-slate-400 font-mono text-xs font-medium whitespace-nowrap">
                        {{ $letter->reference_no ?? '—' }}
                    </td>

                    {{-- Type badge --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-center">
                        @if($letter->type === 'tardy')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tardy
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                            Undertime
                        </span>
                        @endif
                    </td>

                    {{-- Employee --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3">
                        <div class="font-medium text-slate-800 dark:text-slate-100">
                            {{ $letter->employee_prefix }} {{ strtoupper($letter->employee_name) }}
                        </div>
                    </td>

                    {{-- Position / Office --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-slate-600 dark:text-slate-300">
                        @if($letter->employee_position)
                            <span class="block">{{ $letter->employee_position }}</span>
                        @endif
                        @if($letter->employee_office)
                            <span class="text-slate-400 dark:text-slate-500">{{ $letter->employee_office }}</span>
                        @endif
                        @if(!$letter->employee_position && !$letter->employee_office)
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Month / Year --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-center text-slate-700 dark:text-slate-300 whitespace-nowrap">
                        {{ $letter->month }} {{ $letter->year }}
                    </td>

                    {{-- Occurrences --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-center text-slate-700 dark:text-slate-200">
                        <span class="font-semibold">{{ $letter->occurrences }}</span> <span class="text-[11px] text-slate-400">time(s)</span>
                    </td>

                    {{-- Created By --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-center align-middle">
                        <div class="flex flex-col items-center gap-1">
                            @if($letter->createdByUser)
                            <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-[11px] font-bold shadow-sm cursor-help" title="{{ $letter->createdByUser->full_name }}">
                                {{ strtoupper(substr($letter->createdByUser->given_name ?: $letter->createdByUser->name, 0, 1)) }}
                            </div>
                            @else
                            <div class="w-7 h-7 rounded-full bg-slate-300 flex items-center justify-center text-white text-[11px] font-bold shadow-sm">
                                ?
                            </div>
                            @endif
                            <div class="text-[10px] leading-tight text-slate-400 dark:text-slate-500 text-center mt-0.5">
                                {{ $letter->created_at->format('M d, Y') }}<br>
                                {{ $letter->created_at->format('h:i A') }}
                            </div>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Edit --}}
                            <a href="{{ route('admin.undertime-tardy.generated-letters.edit', $letter->id) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
                                      hover:bg-blue-100 dark:hover:bg-blue-900/40 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            
                            {{-- Generate / Print PDF --}}
                            <a href="{{ route('admin.undertime-tardy.generated-letters.print', $letter->id) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400
                                      hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Generate
                            </a>

                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('admin.undertime-tardy.generated-letters.destroy', $letter->id) }}"
                                  onsubmit="return confirm('Delete this letter record?')"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900/20 text-slate-600 dark:text-slate-400
                                               hover:bg-slate-100 dark:hover:bg-slate-900/40 text-xs font-semibold transition-colors hover:text-rose-600 dark:hover:text-rose-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400 dark:text-slate-500">
                            <svg class="w-12 h-12 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium">No generated letters yet.</p>
                            <p class="text-xs">Use the Tardy Letter or Undertime Letter forms to create one.</p>
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    @if($letters->isNotEmpty())
    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-700/20 text-xs text-slate-400 dark:text-slate-500 font-medium">
        {{ $letters->count() }} letter(s) total
    </div>
    @endif
</div>

</div> {{-- end letterFilter --}}

@push('scripts')
<script>
function letterFilter() {
    return {
        open: false,
        search: '',
        type: '',
        position: '',
        createdBy: '',
        month: '',
        year: '',
        creators: @json($creators),

        get activeCount() {
            let c = 0;
            if (this.type) c++;
            if (this.position) c++;
            if (this.createdBy) c++;
            if (this.month) c++;
            if (this.year) c++;
            return c;
        },

        clearAll() {
            this.search = '';
            this.type = '';
            this.position = '';
            this.createdBy = '';
            this.month = '';
            this.year = '';
            this.applyFilters();
        },

        applyFilters() {
            const q = this.search.toLowerCase();
            const pos = this.position.toLowerCase();

            document.querySelectorAll('#gl-tbody .gl-row').forEach(row => {
                const ref = row.dataset.ref || '';
                const nameText = row.dataset.name || '';
                const positionText = row.dataset.position || '';
                const officeText = row.dataset.office || '';
                const typeText = row.dataset.type || '';
                const createdByName = row.dataset.createdByName || '';
                const cb = row.dataset.createdBy || '';
                const m = row.dataset.month || '';
                const y = row.dataset.year || '';

                const matchQ = !q || 
                               ref.includes(q) || 
                               nameText.includes(q) || 
                               positionText.includes(q) || 
                               officeText.includes(q) || 
                               typeText.includes(q) || 
                               createdByName.includes(q);

                const matchPos = !pos || positionText.includes(pos);
                const matchType = !this.type || typeText === this.type;
                const matchCb = !this.createdBy || cb === this.createdBy;
                const matchMonth = !this.month || m === this.month;
                const matchYear = !this.year || y === this.year;

                if (matchQ && matchPos && matchType && matchCb && matchMonth && matchYear) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    };
}
</script>
@endpush

@endsection
