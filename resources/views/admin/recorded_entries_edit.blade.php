@extends('admin.layout')
@section('title', 'Edit Leave Record')
@section('content')

@php
    // allUsers is already pre-mapped with {id, name, position, office} using prefixed IDs
    $employeeJson = collect($allUsers)->values();

    $displayName     = $record->snapshot_name ?? '';
    $displayPosition = $record->snapshot_position ?? '';
    $displayOffice   = $record->snapshot_office ?? '';

    $oldNoPay = old('no_pay_dates',    $record->no_pay_dates    ?? []);
    $oldUnder = old('undertime_dates', $record->undertime_dates  ?? []);
@endphp

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.recorded-entries') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Entries
    </a>
    <span class="text-slate-300 dark:text-slate-600">/</span>
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Leave Record</h1>
</div>

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600
            dark:bg-red-900/20 dark:border-red-800 dark:text-red-300 text-sm">
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6"
     x-data="leaveForm({{ $employeeJson->toJson() }}, '{{ addslashes($displayName) }}', '{{ addslashes($displayPosition) }}', '{{ addslashes($displayOffice) }}')">

    <form method="POST" action="{{ route('admin.recorded-entries.update', $record->id) }}">
        @csrf
        @method('PATCH')

        {{-- ── Employee ── --}}
        <div class="mb-5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Employee Information</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative md:col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Employee Name <span class="text-red-400">*</span></label>
                    <input type="hidden" name="person_id" x-model="selectedId"/>
                    <input type="text"
                           x-model="query"
                           @input="search()"
                           @focus="showDropdown = true"
                           @click.outside="showDropdown = false"
                           autocomplete="off"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition pr-8"/>
                    <svg class="absolute right-3 bottom-3 w-4 h-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <ul x-show="showDropdown && filtered.length > 0"
                        x-transition style="display:none"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl max-h-52 overflow-y-auto">
                        <template x-for="emp in filtered" :key="emp.id">
                            <li @click="selectEmployee(emp)"
                                class="px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer"
                                x-text="emp.name"></li>
                        </template>
                    </ul>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Position</label>
                    <input type="text" x-model="position" readonly
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-700/60
                                  text-slate-600 dark:text-slate-300 text-sm cursor-not-allowed"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Office</label>
                    <input type="text" x-model="office" readonly
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-700/60
                                  text-slate-600 dark:text-slate-300 text-sm cursor-not-allowed"/>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ── Earned Leave Credits ── --}}
            <div class="bg-slate-50 dark:bg-slate-700/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-600">
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-4">Earned Leave Credits Balance</p>
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">As of Date <span class="text-red-400">*</span></label>
                    <input type="date" name="as_of_date"
                           value="{{ old('as_of_date', $record->as_of_date?->format('Y-m-d')) }}"
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">VL</label>
                        <input type="text" name="el_vl"
                               value="{{ old('el_vl', $record->el_vl) }}" placeholder="0.00"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">SL</label>
                        <input type="text" name="el_sl"
                               value="{{ old('el_sl', $record->el_sl) }}" placeholder="0.00"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                </div>
            </div>

            {{-- ── No-Pay Days ── --}}
            <div class="bg-slate-50 dark:bg-slate-700/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-600">
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-4">No. of Days W/OUT Pay</p>
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">VL</label>
                        <input type="number" name="no_pay_vl" step="0.01" min="0"
                               x-model.number="noPayVl" @input="calcTotal()"
                               value="{{ old('no_pay_vl', $record->no_pay_vl) }}" placeholder="0"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">SL</label>
                        <input type="number" name="no_pay_sl" step="0.01" min="0"
                               x-model.number="noPaySl" @input="calcTotal()"
                               value="{{ old('no_pay_sl', $record->no_pay_sl) }}" placeholder="0"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Total</label>
                        <input type="text" readonly :value="noPayTotal" placeholder="0"
                               class="w-full px-3 py-2.5 rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20
                                      text-blue-700 dark:text-blue-300 text-sm font-semibold cursor-not-allowed"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Inclusive Dates</label>
                    <div class="relative" x-data="multiDatePicker('noPay', {{ json_encode($oldNoPay) }})">
                        <div class="w-full min-h-[42px] px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                    flex flex-wrap gap-1.5 items-center" @click.self="open = !open">
                            <template x-for="d in selectedDates" :key="d">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-medium">
                                    <span x-text="chipLabel(d)"></span>
                                    <button type="button" @click.stop="removeDate(d)" class="hover:text-red-500 font-bold ml-0.5">×</button>
                                </span>
                            </template>
                            <template x-if="selectedDates.length === 0">
                                <span class="text-slate-400 text-sm select-none" @click="open = !open">Select dates…</span>
                            </template>
                            <button type="button" @click="open = !open" class="ml-auto text-slate-400 hover:text-blue-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                        <div x-show="open" @click.outside="open = false" style="display:none"
                             class="absolute z-50 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-xl p-4 w-72">
                            <div class="flex items-center justify-between mb-3">
                                <button type="button" @click="prevMonth()" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="monthLabel()"></span>
                                <button type="button" @click="nextMonth()" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                <template x-for="d in ['Su','Mo','Tu','We','Th','Fr','Sa']"><span class="text-center text-[10px] font-semibold text-slate-400 uppercase" x-text="d"></span></template>
                            </div>
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="blank in firstBlank()"><span></span></template>
                                <template x-for="day in daysInMonth()" :key="day">
                                    <button type="button" @click="toggleDay(day)"
                                            :class="isSelected(day) ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'"
                                            class="rounded-lg text-xs py-1.5 transition-colors" x-text="day"></button>
                                </template>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                <button type="button" @click="open = false" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700">Done</button>
                            </div>
                        </div>
                        <template x-for="d in selectedDates" :key="d">
                            <input type="hidden" name="no_pay_dates[]" :value="d"/>
                        </template>
                    </div>
                </div>
                {{-- Remarks --}}
                <div class="mt-3">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Remarks (Optional)</label>
                    <textarea name="remarks" rows="2"
                              placeholder="Add any notes here…"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                     text-slate-800 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('remarks', $record->remarks) }}</textarea>
                </div>
            </div>

            {{-- ── Undertime ── --}}
            <div class="bg-slate-50 dark:bg-slate-700/40 rounded-2xl p-5 border border-slate-200 dark:border-slate-600">
                <p class="text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-4">No. of Days of Undertime/Tardy W/OUT Pay</p>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Hours</label>
                        <input type="number" name="undertime_hours" min="0"
                               value="{{ old('undertime_hours', $record->undertime_hours) }}" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Minutes</label>
                        <input type="number" name="undertime_minutes" min="0" max="59"
                               value="{{ old('undertime_minutes', $record->undertime_minutes) }}" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Inclusive Dates</label>
                    <div class="relative" x-data="multiDatePicker('undertime', {{ json_encode($oldUnder) }})">
                        <div class="w-full min-h-[42px] px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                                    flex flex-wrap gap-1.5 items-center" @click.self="open = !open">
                            <template x-for="d in selectedDates" :key="d">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-medium">
                                    <span x-text="chipLabel(d)"></span>
                                    <button type="button" @click.stop="removeDate(d)" class="hover:text-red-500 font-bold ml-0.5">×</button>
                                </span>
                            </template>
                            <template x-if="selectedDates.length === 0">
                                <span class="text-slate-400 text-sm select-none" @click="open = !open">Select dates…</span>
                            </template>
                            <button type="button" @click="open = !open" class="ml-auto text-slate-400 hover:text-blue-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                        <div x-show="open" @click.outside="open = false" style="display:none"
                             class="absolute z-50 mt-1 right-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-2xl shadow-xl p-4 w-72">
                            <div class="flex items-center justify-between mb-3">
                                <button type="button" @click="prevMonth()" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="monthLabel()"></span>
                                <button type="button" @click="nextMonth()" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                <template x-for="d in ['Su','Mo','Tu','We','Th','Fr','Sa']"><span class="text-center text-[10px] font-semibold text-slate-400 uppercase" x-text="d"></span></template>
                            </div>
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="blank in firstBlank()"><span></span></template>
                                <template x-for="day in daysInMonth()" :key="day">
                                    <button type="button" @click="toggleDay(day)"
                                            :class="isSelected(day) ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'"
                                            class="rounded-lg text-xs py-1.5 transition-colors" x-text="day"></button>
                                </template>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                                <button type="button" @click="open = false" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700">Done</button>
                            </div>
                        </div>
                        <template x-for="d in selectedDates" :key="d">
                            <input type="hidden" name="undertime_dates[]" :value="d"/>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('admin.recorded-entries') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-sm font-semibold text-slate-600 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95
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
function leaveForm(employees, currentName, currentPosition, currentOffice) {
    return {
        employees,
        query: currentName || '',
        filtered: [],
        showDropdown: false,
        selectedId: '{{ $currentPersonId }}',
        position: currentPosition || '',
        office: currentOffice || '',
        noPayVl: {{ $record->no_pay_vl ?? 0 }},
        noPaySl: {{ $record->no_pay_sl ?? 0 }},
        noPayTotal: {{ $record->no_pay_total ?? 0 }},

        search() {
            const q = this.query.toLowerCase();
            this.filtered = q ? this.employees.filter(e => e.name.toLowerCase().includes(q)) : this.employees;
            this.showDropdown = true;
        },
        selectEmployee(emp) {
            this.query = emp.name; this.selectedId = emp.id;
            this.position = emp.position; this.office = emp.office;
            this.showDropdown = false; this.filtered = [];
        },
        calcTotal() {
            const vl = parseFloat(this.noPayVl) || 0;
            const sl = parseFloat(this.noPaySl) || 0;
            this.noPayTotal = (vl + sl) % 1 === 0 ? (vl + sl) : (vl + sl).toFixed(2);
        }
    };
}

function multiDatePicker(prefix, initial) {
    const now = new Date();
    return {
        open: false, prefix,
        year: now.getFullYear(), month: now.getMonth(),
        selectedDates: Array.isArray(initial) ? [...initial] : [],
        monthLabel() { return new Date(this.year, this.month, 1).toLocaleString('en-US', { month: 'long', year: 'numeric' }); },
        daysInMonth() { return Array.from({ length: new Date(this.year, this.month + 1, 0).getDate() }, (_, i) => i + 1); },
        firstBlank() { return Array(new Date(this.year, this.month, 1).getDay()).fill(null); },
        dateKey(day) { return `${this.year}-${String(this.month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`; },
        isSelected(day) { return this.selectedDates.includes(this.dateKey(day)); },
        toggleDay(day) {
            const key = this.dateKey(day);
            const idx = this.selectedDates.indexOf(key);
            if (idx === -1) this.selectedDates.push(key); else this.selectedDates.splice(idx, 1);
            this.selectedDates.sort();
        },
        prevMonth() { if (this.month === 0) { this.month = 11; this.year--; } else this.month--; },
        nextMonth() { if (this.month === 11) { this.month = 0; this.year++; } else this.month++; },
        chipLabel(d) {
            const dt = new Date(d + 'T00:00:00');
            const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            return `${MONTHS[dt.getMonth()]} ${dt.getDate()}, ${dt.getFullYear()}`;
        },
        removeDate(d) { const i = this.selectedDates.indexOf(d); if (i !== -1) this.selectedDates.splice(i, 1); }
    };
}
</script>

@endsection
