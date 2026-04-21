@extends('admin.layout')
@section('title', 'All Leave Processors')

@section('content')

@if(session('status'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-700 dark:text-green-300 text-sm flex items-center gap-2 transition-all">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('status') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300 text-sm flex items-center gap-2 transition-all">
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">All Leave Processors</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
            {{ $employees->count() }} {{ Str::plural('leave processor', $employees->count()) }} registered
        </p>
    </div>
    {{-- Fixed-width search bar on the right --}}
    <div class="relative flex-shrink-0 w-64">
        <input type="text"
               id="empSearch"
               placeholder="Search leave processors…"
               oninput="empPaginator && empPaginator.search(this.value)"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                      bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm"/>
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

    @if($employees->isEmpty())
        <div class="p-10 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">No leave processors found</p>
            <p class="text-xs text-slate-400 mt-1">Leave processors will appear here once they register.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table id="employeesTable" class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            Employee No.
                        </th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Position
                        </th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Office
                        </th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            Registered
                        </th>
                        @if(auth()->user()->isAdmin())
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            Actions
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($employees as $employee)
                    @php
                        // Build formatted name: Last Name, Given Name MI., Suffix
                        $displayName = $employee->full_name;

                        // Avatar initial — prefer given name, fallback to name
                        $initial = strtoupper(substr($employee->given_name ?: $employee->name, 0, 1));
                    @endphp
                    <tr data-search="{{ strtolower($employee->full_name . ' ' . $employee->position . ' ' . $employee->office . ' ' . $employee->employee_number) }}"
                        class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                        {{-- Employee Number --}}
                        <td class="px-5 py-4 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $employee->employee_number ?: '—' }}
                        </td>

                        {{-- Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600
                                            flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-slate-100 whitespace-nowrap">
                                        {{ $displayName }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $employee->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Position --}}
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-xs">
                            {{ $employee->position ?: '—' }}
                        </td>

                        {{-- Office --}}
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-xs">
                            {{ $employee->office ?: '—' }}
                        </td>

                        {{-- Registered --}}
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap">
                            {{ $employee->created_at->format('M d, Y') }}
                        </td>

                        {{-- Actions --}}
                        @if(auth()->user()->isAdmin())
                        <td class="px-5 py-4 text-right">
                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this leave processor account? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition" title="Delete Account">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                        @endif

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ══ Pagination Bar ══ --}}
        <div id="empPaginationBar"
             class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-3.5
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-700/20 text-sm select-none">

            {{-- Left: items per page --}}
            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium whitespace-nowrap">
                Items per page
                <div class="relative">
                    <select id="empPerPage"
                            onchange="empPaginator && empPaginator.setPerPage(+this.value)"
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
                <span id="empRangeLabel" class="text-slate-400"></span>
            </div>

            {{-- Right: nav controls --}}
            <div class="flex items-center gap-1">
                {{-- First --}}
                <button id="empBtnFirst" onclick="empPaginator && empPaginator.first()"
                        title="First page"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20
                               disabled:opacity-30 disabled:pointer-events-none transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
                {{-- Previous --}}
                <button id="empBtnPrev" onclick="empPaginator && empPaginator.prev()"
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
                    <input id="empPageInput" type="number" min="1"
                           onchange="empPaginator && empPaginator.goTo(+this.value)"
                           class="w-12 text-center rounded-lg border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                  text-xs font-semibold py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    <span class="text-xs text-slate-400 whitespace-nowrap">of <span id="empTotalPages">1</span></span>
                </div>
                {{-- Next --}}
                <button id="empBtnNext" onclick="empPaginator && empPaginator.next()"
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
                <button id="empBtnLast" onclick="empPaginator && empPaginator.last()"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('employeesTable');
    if (!table) return;
    
    const rows = Array.from(table.querySelectorAll('tbody tr[data-search]'));
    const perPageSelect = document.getElementById('empPerPage');
    const rangeLabel = document.getElementById('empRangeLabel');
    const totalPagesEl = document.getElementById('empTotalPages');
    const pageInput = document.getElementById('empPageInput');
    const btnFirst = document.getElementById('empBtnFirst');
    const btnPrev = document.getElementById('empBtnPrev');
    const btnNext = document.getElementById('empBtnNext');
    const btnLast = document.getElementById('empBtnLast');
    
    let currentPage = 1;
    let perPage = 25;
    let filteredRows = [...rows];
    
    function render() {
        const total = filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, currentPage), totalPages);
        
        const start = (currentPage - 1) * perPage;
        const end   = start + perPage;
        
        rows.forEach(r => { r.style.display = 'none'; });
        filteredRows.forEach((r, i) => {
            if (i >= start && i < end) r.style.display = '';
        });
        
        if (rangeLabel) {
            const from = total === 0 ? 0 : start + 1;
            const to   = Math.min(start + perPage, total);
            rangeLabel.textContent = `${from}–${to} of ${total} items`;
        }
        if (totalPagesEl) totalPagesEl.textContent = totalPages;
        if (pageInput) { pageInput.value = currentPage; pageInput.max = totalPages; }
        
        if (btnFirst) btnFirst.disabled = currentPage <= 1;
        if (btnPrev) btnPrev.disabled = currentPage <= 1;
        if (btnNext) btnNext.disabled = currentPage >= totalPages;
        if (btnLast) btnLast.disabled = currentPage >= totalPages;
    }
    
    window.empPaginator = {
        search(q) {
            const term = (q || '').toLowerCase().trim();
            filteredRows = rows.filter(r => {
                return !term || (r.dataset.search || '').toLowerCase().includes(term);
            });
            currentPage = 1;
            render();
        },
        setPerPage(n) { perPage = n; currentPage = 1; render(); },
        goTo(n) { currentPage = n; render(); },
        first() { currentPage = 1; render(); },
        prev() { currentPage--; render(); },
        next() { currentPage++; render(); },
        last() { currentPage = Math.max(1, Math.ceil(filteredRows.length / perPage)); render(); }
    };
    
    // Initialize
    if (perPageSelect) {
        perPage = parseInt(perPageSelect.value) || 25;
    }
    render();
});
</script>
@endsection
