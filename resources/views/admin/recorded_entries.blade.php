@extends('admin.layout')
@section('title', 'Recorded Entries')
@section('content')
@php
    // All employees as JSON for the certifier modal auto-fill (includes admins and employees)
    $allEmpJson = \App\Models\User::whereIn('role', ['admin', 'employee'])
        ->where('is_confirmed', true)
        ->orderBy('last_name')->orderBy('given_name')
        ->get(['id','last_name','given_name','middle_name','suffix','name','position'])
        ->map(fn($u) => ['id'=>$u->id, 'name'=>$u->pdf_name, 'position'=>$u->position ?? ''])
        ->values();
@endphp

{{-- ══ Filter state (Alpine root) ══ --}}
<div x-data="recordFilter()" @keydown.escape.window="open = false">

{{-- Page header --}}
<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Recorded Entries</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">All saved leave record entries. Edit or generate PDF for each.</p>
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
</div>

{{-- ══ Slide-out filter drawer ══ --}}
<div x-show="open" x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-x-4"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-4"
     style="display:none"
     class="fixed top-0 right-0 h-full w-80 z-40 shadow-2xl
            bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 overflow-y-auto">

    {{-- Drawer header --}}
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

    {{-- Pass creators JSON for the Alpine dropdown --}}
    <script>
        window.__CREATORS__ = @json($creators);
    </script>

    <div class="p-5 space-y-6">

        {{-- ── Created By ── --}}
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
            <div class="relative" x-data="{ createdByName: '' }" x-init="
                $watch('createdBy', val => {
                    if (!val) createdByName = '';
                    else {
                        const c = creators.find(cr => cr.id == val);
                        if (c) createdByName = c.name;
                    }
                });
                $watch('createdByName', val => {
                    if (!val) {
                        if (createdBy !== '') { createdBy = ''; applyFilters(); }
                        return;
                    }
                    const match = creators.find(cr => cr.name.toLowerCase() === val.toLowerCase());
                    const newId = match ? match.id : '';
                    if (createdBy !== newId) {
                        createdBy = newId;
                        applyFilters();
                    }
                });
            ">
                <style>
                    /* Hide the native datalist arrow on all states */
                    #createdByInput::-webkit-calendar-picker-indicator {
                        display: none !important;
                        -webkit-appearance: none;
                    }
                </style>
                <input id="createdByInput" type="text" x-model="createdByName" list="creators-list"
                       placeholder="All users"
                       autocomplete="off"
                       class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500 transition pr-9">
                <datalist id="creators-list">
                    <template x-for="c in creators" :key="c.id">
                        <option :value="c.name"></option>
                    </template>
                </datalist>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- ── Search ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </div>
                <button x-show="search" @click="search=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" @input="applyFilters()"
                       placeholder="Search by Name, Office, Position…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400"/>
            </div>
        </div>

        {{-- ── Month ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Month
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
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- ── Year ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Year
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
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- ── Date Range (Created At) ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Date Created Range
                </div>
                <button x-show="dateFrom || dateTo" @click="dateFrom=''; dateTo=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="date" x-model="dateFrom" @change="applyFilters()"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>
                <span class="text-xs text-slate-400 font-semibold flex-shrink-0">—</span>
                <div class="relative flex-1">
                    <input type="date" x-model="dateTo" @change="applyFilters()"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>
            </div>
            {{-- Display formatted range --}}
            <p x-show="dateFrom || dateTo" class="mt-1.5 text-[10px] text-slate-400 text-center" style="display:none">
                <span x-text="formatDateRange()"></span>
            </p>
        </div>

    </div>{{-- /p-5 --}}
</div>{{-- /drawer --}}

{{-- Backdrop --}}
<div x-show="open" @click="open = false"
     class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm"
     style="display:none"></div>



@if(session('status'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700
            dark:bg-green-900/20 dark:border-green-700 dark:text-green-300 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('status') }}
</div>
@endif

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">All Leave Records</h2>
        <div class="flex items-center gap-3">
            <span id="entryCountBadge" class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                {{ $records->count() }} {{ Str::plural('entry', $records->count()) }}
            </span>
            
            <div x-data="{ reportOpen: false }" class="relative">
                <button @click="reportOpen = !reportOpen" @click.away="reportOpen = false"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Report
                </button>
                <div x-show="reportOpen" x-transition
                     style="display:none;"
                     class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden z-20">
                    <button type="button" onclick="exportReport('pdf')"
                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </button>
                    <button type="button" onclick="exportReport('excel')"
                            class="w-full text-left px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export XLSX
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($records->isEmpty())
        <div class="p-10 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">No entries yet</p>
            <p class="text-xs text-slate-400 mt-1">
                Go to <a href="{{ route('admin.leaves') }}" class="text-blue-500 hover:underline">Leave Records</a> to add entries.
            </p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table id="entriesTable" class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-700/40">
                    <th rowspan="3" class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[160px]">NAME</th>
                    <th rowspan="3" class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[120px]">POSITION</th>
                    <th rowspan="3" class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[120px]">OFFICE</th>
                    <th colspan="2" class="border border-slate-200 dark:border-slate-600 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                        EARNED LEAVE CREDITS BALANCE
                    </th>
                    <th colspan="4" class="border border-slate-200 dark:border-slate-600 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                        NO. OF DAYS W/OUT PAY
                    </th>
                    <th colspan="3" class="border border-slate-200 dark:border-slate-600 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                        NO. OF DAYS OF UNDERTIME/TARDY W/OUT PAY
                    </th>
                    <th rowspan="3" class="border border-slate-200 dark:border-slate-600 px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wide align-middle min-w-[130px]">ACTIONS</th>
                </tr>
                <tr class="bg-slate-50 dark:bg-slate-700/40">
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle">VL</th>
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle">SL</th>
                    <th colspan="3" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400">DAYS</th>
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle min-w-[140px]">INCLUSIVE DATES</th>
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle">HRS</th>
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle">MINS</th>
                    <th rowspan="2" class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400 align-middle min-w-[140px]">INCLUSIVE DATES</th>
                </tr>
                <tr class="bg-slate-50 dark:bg-slate-700/40">
                    <th class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400">VL</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400">SL</th>
                    <th class="border border-slate-200 dark:border-slate-600 px-3 py-2 text-center font-semibold text-slate-500 dark:text-slate-400">TOTAL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($records as $rec)
                @php $u = $rec->user; @endphp
                @php $createdDate = $rec->created_at ? $rec->created_at->format('Y-m-d') : ''; @endphp
                @php 
                    $displayName = $rec->snapshot_name ?? ($u ? $u->full_name : '—');
                    $displayPosition = $rec->snapshot_position ?? ($u ? $u->position : '—');
                    $displayOffice = $rec->snapshot_office ?? ($u ? $u->office : '—');
                @endphp
                <tr data-id="{{ $rec->id }}"
                    data-search="{{ strtolower($displayName . ' ' . $displayPosition . ' ' . $displayOffice) }}"
                    data-date="{{ $createdDate }}"
                    data-month="{{ $createdDate ? $rec->created_at->format('m') : '' }}"
                    data-year="{{ $createdDate ? $rec->created_at->format('Y') : '' }}"
                    data-created-by="{{ $rec->created_by }}"
                    class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3">
                        <p class="font-medium text-slate-800 dark:text-slate-100">{{ $displayName }}</p>
                        @if($rec->as_of_date)
                            <p class="text-[10px] text-slate-400 mt-0.5">As of {{ $rec->as_of_date->format('M d, Y') }}</p>
                        @endif
                    </td>
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-slate-600 dark:text-slate-300">{{ $displayPosition ?: '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-4 py-3 text-slate-600 dark:text-slate-300">{{ $displayOffice ?: '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->el_vl !== null ? $rec->el_vl : '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->el_sl !== null ? $rec->el_sl : '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->no_pay_vl ?? '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->no_pay_sl ?? '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center font-semibold text-slate-800 dark:text-slate-100">{{ $rec->no_pay_total ?? '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-slate-600 dark:text-slate-300 text-[11px]">{{ $rec->no_pay_dates ? \App\Models\LeaveRecord::formatDates($rec->no_pay_dates) : '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->undertime_hours ?? '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-center text-slate-700 dark:text-slate-200">{{ $rec->undertime_minutes ?? '—' }}</td>
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3 text-slate-600 dark:text-slate-300 text-[11px]">{{ $rec->undertime_dates ? \App\Models\LeaveRecord::formatDates($rec->undertime_dates) : '—' }}</td>
                    {{-- Actions --}}
                    <td class="border border-slate-100 dark:border-slate-700 px-3 py-3">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Edit --}}
                            <a href="{{ route('admin.recorded-entries.edit', $rec->id) }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
                                      hover:bg-blue-100 dark:hover:bg-blue-900/40 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            {{-- Generate PDF: opens modal --}}
                            <button type="button"
                                    onclick="openPdfModal({{ $rec->id }}, event)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400
                                           hover:bg-rose-100 dark:hover:bg-rose-900/40 text-xs font-semibold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                PDF
                            </button>
                            {{-- Delete --}}
                            <form action="{{ route('admin.recorded-entries.destroy', $rec->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this recorded entry? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900/20 text-slate-600 dark:text-slate-400
                                               hover:bg-slate-100 dark:hover:bg-slate-900/40 text-xs font-semibold transition-colors hover:text-rose-600 dark:hover:text-rose-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ══ Pagination Bar ══ --}}
    <div id="entriesPaginationBar"
         class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-3.5
                border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-700/20 text-sm select-none">

        {{-- Left: items per page --}}
        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium whitespace-nowrap">
            Items per page
            <div class="relative">
                <select id="entriesPerPage"
                        onchange="entriesPaginator && entriesPaginator.setPerPage(+this.value)"
                        class="appearance-none pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600
                               bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold
                               focus:outline-none focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <span id="entriesRangeLabel" class="text-slate-400"></span>
        </div>

        {{-- Right: nav controls --}}
        <div class="flex items-center gap-1">
            {{-- First --}}
            <button id="entriesBtnFirst" onclick="entriesPaginator && entriesPaginator.first()"
                    title="First page"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20
                           disabled:opacity-30 disabled:pointer-events-none transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            {{-- Previous --}}
            <button id="entriesBtnPrev" onclick="entriesPaginator && entriesPaginator.prev()"
                    title="Previous page"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-blue-600 dark:text-blue-400
                           hover:bg-blue-50 dark:hover:bg-blue-900/20
                           disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
            </button>
            {{-- Page input --}}
            <div class="flex items-center gap-1.5 px-1">
                <input id="entriesPageInput" type="number" min="1"
                       onchange="entriesPaginator && entriesPaginator.goTo(+this.value)"
                       class="w-12 text-center rounded-lg border border-slate-200 dark:border-slate-600
                              bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                              text-xs font-semibold py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                <span class="text-xs text-slate-400 whitespace-nowrap">of <span id="entriesTotalPages">1</span></span>
            </div>
            {{-- Next --}}
            <button id="entriesBtnNext" onclick="entriesPaginator && entriesPaginator.next()"
                    title="Next page"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-blue-600 dark:text-blue-400
                           hover:bg-blue-50 dark:hover:bg-blue-900/20
                           disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                Next
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            {{-- Last --}}
            <button id="entriesBtnLast" onclick="entriesPaginator && entriesPaginator.last()"
                    title="Last page"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20
                           disabled:opacity-30 disabled:pointer-events-none transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
    @endif
</div>
{{-- close Alpine root --}}
</div>

@endsection

@push('scripts')
{{-- ══ PDF Certifier Modal ══ --}}
<div id="pdfModal" style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md p-6 mx-4">
        <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">Generate Leave Record PDF</h3>

        {{-- ── Leave Type Checkboxes ── --}}
        <div class="mb-5">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Leave Type(s) <span class="text-rose-500">*</span></label>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mb-2.5">Select one or more types that will appear in the generated document.</p>
            <div class="grid grid-cols-1 gap-2">
                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <input type="checkbox" id="lt_leave_of_absence" value="leave of absence"
                           class="w-4 h-4 rounded accent-rose-600 shrink-0">
                    <span class="text-sm text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">Leave of Absence</span>
                </label>
                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <input type="checkbox" id="lt_undertime" value="undertime"
                           class="w-4 h-4 rounded accent-rose-600 shrink-0">
                    <span class="text-sm text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">Undertime</span>
                </label>
                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <input type="checkbox" id="lt_tardy" value="tardy"
                           class="w-4 h-4 rounded accent-rose-600 shrink-0">
                    <span class="text-sm text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">Tardy</span>
                </label>
                <label class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <input type="checkbox" id="lt_unauthorized" value="unauthorized absence"
                           class="w-4 h-4 rounded accent-rose-600 shrink-0">
                    <span class="text-sm text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">Unauthorized Absence</span>
                </label>
            </div>
            <p id="leaveTypeError" style="display:none;" class="mt-1.5 text-[11px] text-rose-500 font-medium">Please select at least one leave type.</p>
        </div>

        <div class="mb-4 relative">

            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Select the certifying officer. The name and position will appear in the <em>Noted by</em> section of the document.</p>
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Name</label>
            <input type="text" id="certifierInput" autocomplete="off"
                   value=""
                   placeholder="Search or type a name..."
                   onfocus="showCertifierDropdown()"
                   oninput="filterCertifierDropdown()"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
            
            <div id="certifierDropdown" style="display: none;"
                 class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                 @foreach($allEmpJson as $emp)
                     <div class="px-3 py-2 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 text-sm text-slate-800 dark:text-slate-100 certifier-option"
                          data-name="{{ strtolower($emp['name']) }}"
                          data-position="{{ $emp['position'] }}"
                          onclick="selectCertifier('{{ addslashes($emp['name']) }}', '{{ addslashes($emp['position']) }}')">
                         {{ $emp['name'] }}
                     </div>
                 @endforeach
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Position</label>
            <textarea id="certifierPosition" rows="2"
                      placeholder="Position title…"
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                             text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="button" onclick="closePdfModal()"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white transition">Cancel</button>
            <button type="button" onclick="confirmPdf()"
                    class="px-5 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition shadow-sm">
                Generate PDF
            </button>
        </div>
    </div>
</div>

<script>
const PDF_BASE_ROUTES = @json(
    $records->mapWithKeys(fn($r) => [$r->id => route('admin.recorded-entries.pdf', $r->id)])
);
let _activePdfRecordId = null;

function openPdfModal(recordId) {
    _activePdfRecordId = recordId;
    document.getElementById('certifierInput').value = '';
    document.getElementById('certifierPosition').value = '';
    document.getElementById('certifierDropdown').style.display = 'none';
    // Reset checkboxes
    ['lt_leave_of_absence','lt_undertime','lt_tardy','lt_unauthorized'].forEach(id => {
        document.getElementById(id).checked = false;
    });
    document.getElementById('leaveTypeError').style.display = 'none';
    document.getElementById('pdfModal').style.display = 'flex';
}

function closePdfModal() {
    document.getElementById('pdfModal').style.display = 'none';
    document.getElementById('certifierDropdown').style.display = 'none';
    _activePdfRecordId = null;
}

function showCertifierDropdown() {
    document.getElementById('certifierDropdown').style.display = 'block';
    filterCertifierDropdown();
}

function filterCertifierDropdown() {
    const term = document.getElementById('certifierInput').value.toLowerCase().trim();
    const options = document.querySelectorAll('.certifier-option');
    options.forEach(opt => {
        const name = opt.getAttribute('data-name');
        if (!term || name.includes(term)) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });

    // Auto update position if exact match found
    const exactMatch = Array.from(options).find(opt => opt.getAttribute('data-name') === term);
    if (exactMatch) {
        document.getElementById('certifierPosition').value = exactMatch.getAttribute('data-position') || '';
    }
}

function selectCertifier(name, position) {
    document.getElementById('certifierInput').value = name;
    document.getElementById('certifierPosition').value = position;
    document.getElementById('certifierDropdown').style.display = 'none';
}

function confirmPdf() {
    if (!_activePdfRecordId) return;
    const name = document.getElementById('certifierInput').value.trim();
    const pos  = document.getElementById('certifierPosition').value.trim();
    if (!name) { alert('Please enter a certifier name.'); return; }

    // Collect selected leave types
    const leaveTypeIds = ['lt_leave_of_absence','lt_undertime','lt_tardy','lt_unauthorized'];
    const selected = leaveTypeIds
        .filter(id => document.getElementById(id).checked)
        .map(id => document.getElementById(id).value);

    if (selected.length === 0) {
        document.getElementById('leaveTypeError').style.display = 'block';
        return;
    }
    document.getElementById('leaveTypeError').style.display = 'none';

    // Format: "leave of absence/undertime/tardy" etc.
    const leaveTypes = selected.join('/');

    const base = PDF_BASE_ROUTES[_activePdfRecordId];
    const url  = base
        + '?certifier_name='     + encodeURIComponent(name)
        + '&certifier_position=' + encodeURIComponent(pos)
        + '&leave_types='        + encodeURIComponent(leaveTypes);
    window.open(url, '_blank');
    closePdfModal();
}

// Close modal on backdrop click, and hide dropdown on outside click
document.getElementById('pdfModal').addEventListener('click', function(e) {
    if (e.target === this) closePdfModal();
});
document.addEventListener('click', function(e) {
    const input = document.getElementById('certifierInput');
    const dropdown = document.getElementById('certifierDropdown');
    if (input && dropdown && e.target !== input && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
<script>
function recordFilter() {
    return {
        open: false,
        search: '',
        createdBy: '',
        month: '',
        year: '',
        dateFrom: '',
        dateTo: '',
        creators: window.__CREATORS__ || [],

        get activeCount() {
            return [this.search, this.createdBy, this.month, this.year, this.dateFrom || this.dateTo]
                .filter(Boolean).length;
        },

        applyFilters() {
            const rows = document.querySelectorAll('#entriesTable tbody tr[data-search]');
            const term      = this.search.toLowerCase().trim();
            const createdBy = this.createdBy ? String(this.createdBy) : '';
            const month     = this.month;
            const year      = this.year;
            const from      = this.dateFrom;
            const to        = this.dateTo;

            // Determine which rows pass all filters
            const matchedRows = [];
            rows.forEach(row => {
                const searchOk    = !term      || row.dataset.search.includes(term);
                const creatorOk   = !createdBy || row.dataset.createdBy === createdBy;
                const monthOk     = !month     || row.dataset.month === month;
                const yearOk      = !year      || row.dataset.year  === year;

                let dateOk = true;
                const d = row.dataset.date;
                if (d) {
                    if (from && d < from) dateOk = false;
                    if (to   && d > to)   dateOk = false;
                } else if (from || to) {
                    dateOk = false;
                }

                if (searchOk && creatorOk && monthOk && yearOk && dateOk) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            // Hand matched rows to paginator (resets to page 1)
            if (window.entriesPaginator) {
                window.entriesPaginator.resetWithRows(matchedRows);
            }

            const visible = matchedRows.length;

            const empty = document.getElementById('entriesEmpty');
            if (empty) empty.style.display = visible === 0 ? '' : 'none';

            // Update the entry count badge
            const badge = document.getElementById('entryCountBadge');
            if (badge) {
                badge.textContent = visible + (visible === 1 ? ' entry' : ' entries');
            }
        },

        clearAll() {
            this.search    = '';
            this.createdBy = '';
            this.month     = '';
            this.year      = '';
            this.dateFrom  = '';
            this.dateTo    = '';
            this.applyFilters();
        },

        formatDateRange() {
            const fmt = d => {
                if (!d) return '';
                const [y, m, day] = d.split('-');
                return `${m}/${day}/${y}`;
            };
            const from = fmt(this.dateFrom);
            const to   = fmt(this.dateTo);
            if (from && to)  return `${from} — ${to}`;
            if (from)        return `From ${from}`;
            if (to)          return `Until ${to}`;
            return '';
        }
    };
}
</script>
<script>
let entriesPaginator = null;

function makeTablePaginator({ getRows, rangeLabel, totalPagesEl, pageInput, btnFirst, btnPrev, btnNext, btnLast, defaultPerPage }) {
    let currentPage = 1;
    let perPage = defaultPerPage;
    let filteredRows = [];

    function allRows() { return Array.from(getRows()); }

    function render() {
        const total = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, currentPage), totalPages);

        const start = (currentPage - 1) * perPage;
        const end   = start + perPage;

        allRows().forEach(r => { r.style.display = 'none'; });
        filteredRows.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
        });

        if (rangeLabel) {
            const from = total === 0 ? 0 : start + 1;
            const to   = Math.min(end, total);
            rangeLabel.textContent = `${from}\u2013${to} of ${total} items`;
        }
        if (totalPagesEl) totalPagesEl.textContent = totalPages;
        if (pageInput) { pageInput.value = currentPage; pageInput.max = totalPages; }

        const isFirst = currentPage <= 1;
        const isLast  = currentPage >= totalPages;
        [btnFirst, btnPrev].forEach(b => b && (isFirst ? b.setAttribute('disabled','') : b.removeAttribute('disabled')));
        [btnNext,  btnLast ].forEach(b => b && (isLast  ? b.setAttribute('disabled','') : b.removeAttribute('disabled')));
    }

    return {
        init() { filteredRows = allRows(); render(); },
        resetWithRows(rows) { filteredRows = rows; currentPage = 1; render(); },
        setPerPage(n) { perPage = n; currentPage = 1; render(); },
        goTo(n)       { currentPage = n; render(); },
        first()       { currentPage = 1; render(); },
        prev()        { currentPage--; render(); },
        next()        { currentPage++; render(); },
        last()        { currentPage = Math.max(1, Math.ceil(filteredRows.length / perPage)); render(); },
    };
}

document.addEventListener('DOMContentLoaded', function () {
    entriesPaginator = makeTablePaginator({
        getRows:       () => document.querySelectorAll('#entriesTable tbody tr[data-search]'),
        rangeLabel:    document.getElementById('entriesRangeLabel'),
        totalPagesEl:  document.getElementById('entriesTotalPages'),
        pageInput:     document.getElementById('entriesPageInput'),
        btnFirst:      document.getElementById('entriesBtnFirst'),
        btnPrev:       document.getElementById('entriesBtnPrev'),
        btnNext:       document.getElementById('entriesBtnNext'),
        btnLast:       document.getElementById('entriesBtnLast'),
        defaultPerPage: 25,
    });
    window.entriesPaginator = entriesPaginator;
    entriesPaginator.init();
});

function exportReport(type) {
    const rows = document.querySelectorAll('#entriesTable tbody tr[data-search]');
    const ids = [];
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            ids.push(row.dataset.id);
        }
    });
    
    if (ids.length === 0) {
        alert('No records to export.');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = type === 'pdf' 
        ? '{{ route("admin.recorded-entries.export-pdf") }}' 
        : '{{ route("admin.recorded-entries.export-excel") }}';
    
    if (type === 'pdf') {
        form.target = '_blank';
    }
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const idsInput = document.createElement('input');
    idsInput.type = 'hidden';
    idsInput.name = 'ids';
    idsInput.value = ids.join(',');
    form.appendChild(idsInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush
