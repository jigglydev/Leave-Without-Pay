@extends('admin.layout')
@section('title', 'Edit Generated Letter')
@section('content')

<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.undertime-tardy.generated-letters') }}" class="w-8 h-8 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Generated Letter</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Reference No. <span class="font-mono">{{ $letter->reference_no ?? '—' }}</span></p>
        </div>
    </div>
</div>

<div class="max-w-4xl"
     x-data="editLetterForm({{ $allUsers->toJson() }}, '{{ $letter->person_id }}', '{{ addslashes($letter->employee_name) }}', '{{ addslashes($letter->employee_position) }}', '{{ addslashes($letter->employee_office) }}')">

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.undertime-tardy.generated-letters.update', $letter->id) }}">
        @csrf
        @method('PATCH')

        {{-- ── Section: Employee Info ── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Employee Information</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Employee Search --}}
                <div class="relative md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Employee Name <span class="text-red-400">*</span>
                    </label>
                    <input type="hidden" name="person_id" x-model="selectedId"/>
                    <div class="relative">
                        <input type="text"
                               x-model="query"
                               @input="search()"
                               @focus="showDropdown = true"
                               @click.outside="showDropdown = false"
                               placeholder="Type to search employee…"
                               autocomplete="off"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition pr-10"/>
                        <svg class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <ul x-show="showDropdown && filtered.length > 0"
                            x-transition
                            style="display:none"
                            class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                   rounded-xl shadow-xl max-h-56 overflow-y-auto">
                            <template x-for="emp in filtered" :key="emp.id">
                                <li @click="selectEmployee(emp)"
                                    class="px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-colors"
                                    x-text="emp.name"></li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Prefix --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Prefix <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <select name="prefix" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                       text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none">
                            <option value="">Select Prefix</option>
                            @foreach($prefixes as $p)
                                <option value="{{ $p->name }}" {{ $letter->employee_prefix === $p->name ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Position (auto-filled) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Position</label>
                    <input type="text" x-model="selectedPosition" readonly
                           placeholder="Auto-filled from employee"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-700/60
                                  text-slate-600 dark:text-slate-300 text-sm focus:outline-none cursor-not-allowed"/>
                </div>

                {{-- Office (auto-filled) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Office / Department</label>
                    <input type="text" x-model="selectedOffice" readonly
                           placeholder="Auto-filled from employee"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-700/60
                                  text-slate-600 dark:text-slate-300 text-sm focus:outline-none cursor-not-allowed"/>
                </div>

            </div>
        </div>

        {{-- ── Section: Letter Details ── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-5">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Letter Details ({{ ucfirst($letter->type) }})</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- Month --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Month <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <select name="month" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                       text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ date('F', mktime(0, 0, 0, $m, 1)) }}" {{ $letter->month === date('F', mktime(0, 0, 0, $m, 1)) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <svg class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Year --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Year <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <select name="year" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                       text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none">
                            @for($y=date('Y'); $y>=date('Y')-5; $y--)
                                <option value="{{ $y }}" {{ $letter->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <svg class="absolute right-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Occurrences --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        No. of {{ ucfirst($letter->type) }} Occurrences <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="occurrences" required
                           value="{{ $letter->occurrences }}"
                           placeholder='e.g. "three (3)" or "5"'
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>

            </div>
        </div>

        {{-- ── Section: Signatory ── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Signatory</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Signatory Name</label>
                    <input type="text" name="certifier_name" value="{{ $letter->certifier_name }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Signatory Position</label>
                    <input type="text" name="certifier_position" value="{{ $letter->certifier_position }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>
            </div>
        </div>

        {{-- ── Submit ── --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.undertime-tardy.generated-letters') }}" class="inline-flex items-center gap-2 px-7 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl transition-all">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-7 py-3 bg-blue-600 hover:bg-blue-700 active:scale-95
                           text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/30 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
function editLetterForm(employees, currentPersonId, currentName, currentPosition, currentOffice) {
    return {
        employees,
        query: currentName,
        filtered: [],
        showDropdown: false,
        selectedId: currentPersonId,
        selectedPosition: currentPosition,
        selectedOffice: currentOffice,

        search() {
            const q = this.query.toLowerCase();
            this.filtered = q
                ? this.employees.filter(e => e.name.toLowerCase().includes(q))
                : this.employees.slice(0, 30);
            this.showDropdown = true;
            // Clear selection if user edits the field
            this.selectedId = '';
            this.selectedPosition = '';
            this.selectedOffice = '';
        },

        selectEmployee(emp) {
            this.query            = emp.name;
            this.selectedId       = emp.id;
            this.selectedPosition = emp.position;
            this.selectedOffice   = emp.office;
            this.showDropdown     = false;
            this.filtered         = [];
        }
    };
}
</script>

@endsection
