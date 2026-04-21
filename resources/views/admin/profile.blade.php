@extends('admin.layout')
@section('title', 'Profile')
@section('content')

    @php
        $officeNames = $offices->pluck('name')->values()->all();
        $positionNames = $positions->pluck('name')->values()->all();
        $user = Auth::user();
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Profile</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Manage your account information.</p>
    </div>

    {{-- Success message --}}
    @if(session('status'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700
                        dark:bg-green-900/20 dark:border-green-700 dark:text-green-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <div
        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-8 max-w-2xl">

        {{-- Avatar + Name --}}
        <div class="flex items-center gap-4 mb-8">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-blue-500/30">
                {{ strtoupper(substr($user->given_name ?: $user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-lg font-bold text-slate-800 dark:text-white">{{ $user->full_name }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full font-medium
                    {{ $user->role === 'admin'
        ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
        : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        {{-- Edit Form --}}
        <form method="POST" action="{{ route('admin.profile.update') }}" x-data="profileForm()" novalidate>
            @csrf
            @method('PATCH')

            <div class="space-y-5">

                {{-- ── Name Fields ──────────────────────────────── --}}
                <div class="pb-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Name</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Last Name --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Last
                                Name <span class="text-red-400">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                placeholder="e.g. dela Cruz" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- Given Name --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Given
                                Name <span class="text-red-400">*</span></label>
                            <input type="text" name="given_name" value="{{ old('given_name', $user->given_name) }}"
                                placeholder="e.g. Juan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('given_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- Middle Name --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Middle
                                Initial <span
                                    class="text-slate-300 dark:text-slate-600 font-normal">(optional)</span></label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                placeholder="e.g. Santos"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('middle_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        {{-- Suffix --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Suffix
                                <span class="text-slate-300 dark:text-slate-600 font-normal">(optional)</span></label>
                            <input type="text" name="suffix" value="{{ old('suffix', $user->suffix) }}"
                                placeholder="e.g. Jr., Sr., III"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                            @error('suffix')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── Employee Number ───────────────────────────── --}}
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Employee
                        Number <span class="text-slate-300 dark:text-slate-600 font-normal">(optional)</span></label>
                    <input type="text" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}"
                        placeholder="e.g. EMP-00123"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                  text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" />
                    @error('employee_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- ── Office (searchable dropdown) ─────────────── --}}
                <div x-data="searchableSelect('office', {{ json_encode($officeNames) }}, {{ json_encode(old('office', $user->office ?? '')) }})"
                    class="relative" @click.outside="close()">
                    <label
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Office</label>
                    <input type="hidden" name="office" :value="selected" />
                    <div class="relative">
                        <input type="text" x-model="query" @focus="open = true" @input="open = true"
                            @keydown.escape="close()" placeholder="Search or select office…"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-9" />
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <ul x-show="open && filtered().length > 0" x-transition style="display:none"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg max-h-52 overflow-y-auto">
                        <template x-for="item in filtered()" :key="item">
                            <li @mousedown.prevent="select(item)"
                                class="px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-colors"
                                x-text="item"></li>
                        </template>
                    </ul>
                    @error('office')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- ── Position (searchable dropdown) ──────────── --}}
                <div x-data="searchableSelect('position', {{ json_encode($positionNames) }}, {{ json_encode(old('position', $user->position ?? '')) }})"
                    class="relative" @click.outside="close()">
                    <label
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Position</label>
                    <input type="hidden" name="position" :value="selected" />
                    <div class="relative">
                        <input type="text" x-model="query" @focus="open = true" @input="open = true"
                            @keydown.escape="close()" placeholder="Search or select position…"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                      text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-9" />
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <ul x-show="open && filtered().length > 0" x-transition style="display:none"
                        class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg max-h-52 overflow-y-auto">
                        <template x-for="item in filtered()" :key="item">
                            <li @mousedown.prevent="select(item)"
                                class="px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-colors"
                                x-text="item"></li>
                        </template>
                    </ul>
                    @error('position')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>


                {{-- Joined at --}}
                <div
                    class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 text-sm text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Joined {{ $user->created_at->format('F d, Y') }}</span>
                </div>

                {{-- Save button --}}
                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-semibold shadow-md shadow-blue-500/30 transition-all">
                    Save Changes
                </button>

            </div>
        </form>
    </div>

    <script>
        function searchableSelect(field, items, currentValue) {
            return {
                open: false,
                query: currentValue || '',
                selected: currentValue || '',
                items: items || [],
                filtered() {
                    const q = (this.query || '').toString().toLowerCase();
                    return q ? this.items.filter(i => (i || '').toString().toLowerCase().includes(q)) : this.items;
                },
                select(item) {
                    this.selected = item;
                    this.query = item;
                    this.open = false;
                },
                close() {
                    // Snap query back to the last confirmed selection so they stay in sync
                    this.query = this.selected || '';
                    this.open = false;
                }
            }
        }
        function profileForm() { return {}; }
    </script>

@endsection