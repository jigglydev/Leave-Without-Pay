@extends('admin.layout')
@section('title', 'Settings')
@section('content')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Settings</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                @if(Auth::user()->role === 'admin')
                    Manage positions, offices, user access levels, and appearance.
                @else
                    Manage employees, password, and appearance.
                @endif
            </p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('status'))
        <div
            class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700
                                                                dark:bg-green-900/20 dark:border-green-700 dark:text-green-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div
            class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700
                                                                dark:bg-red-900/20 dark:border-red-700 dark:text-red-300 text-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $defaultTab = Auth::user()->role === 'admin' ? 'organization' : 'org-employee'; @endphp
    <div class="flex flex-col md:flex-row gap-8" x-data="{ tab: '{{ $defaultTab }}' }" x-cloak>

        {{-- ── SIDEBAR NAV ─────────────── --}}
        <div class="w-full md:w-64 flex-shrink-0 flex flex-col gap-2">
            @if(Auth::user()->role === 'admin')
                <button type="button" @click="tab = 'organization'"
                    :class="tab === 'organization' ? 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800' : 'text-slate-600 hover:bg-slate-50 border-transparent dark:text-slate-400 dark:hover:bg-slate-800/50'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left border">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Organization
                </button>
                <button type="button" @click="tab = 'access'"
                    :class="tab === 'access' ? 'bg-violet-50 text-violet-700 border-violet-100 dark:bg-violet-900/30 dark:text-violet-400 dark:border-violet-800' : 'text-slate-600 hover:bg-slate-50 border-transparent dark:text-slate-400 dark:hover:bg-slate-800/50'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left border">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Access & Security
                </button>
            @else
                <button type="button" @click="tab = 'org-employee'"
                    :class="tab === 'org-employee' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800' : 'text-slate-600 hover:bg-slate-50 border-transparent dark:text-slate-400 dark:hover:bg-slate-800/50'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left border">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Organization
                </button>
                <button type="button" @click="tab = 'security'"
                    :class="tab === 'security' ? 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800' : 'text-slate-600 hover:bg-slate-50 border-transparent dark:text-slate-400 dark:hover:bg-slate-800/50'"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left border">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security
                </button>
            @endif
            <button type="button" @click="tab = 'preferences'"
                :class="tab === 'preferences' ? 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800' : 'text-slate-600 hover:bg-slate-50 border-transparent dark:text-slate-400 dark:hover:bg-slate-800/50'"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left border">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Preferences
            </button>
        </div>

        {{-- ── CONTENT AREA ─────────────── --}}
        <div class="flex-1 min-w-0">

            @if(Auth::user()->role === 'admin')
                {{-- TAB: ORGANIZATION --}}
                <div x-show="tab === 'organization'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-6 max-w-4xl" style="display:none;">

                    {{-- ═══════════════════════════════════
                    POSITIONS
                    ═══════════════════════════════════ --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Positions</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $positions->count() }} {{ Str::plural('entry', $positions->count()) }}
                            </span>
                        </div>

                        {{-- Multi-entry row input --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700" x-data="chipPairInput()">
                            <form method="POST" action="{{ route('admin.settings.positions.store') }}" @submit="prepareSubmit">
                                @csrf

                                {{-- Staged pairs --}}
                                <div class="space-y-1.5 mb-3" x-show="pairs.length > 0">
                                    <template x-for="(pair, idx) in pairs" :key="idx">
                                        <div
                                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                                                                                        bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                            <span
                                                class="text-xs font-mono font-semibold text-blue-500 dark:text-blue-400 min-w-[60px]"
                                                x-text="pair.code || '—'"></span>
                                            <span class="text-xs text-slate-700 dark:text-slate-200 flex-1"
                                                x-text="pair.name"></span>
                                            <button type="button" @click="removePair(idx)"
                                                class="w-4 h-4 rounded-full flex items-center justify-center
                                                                                               text-blue-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                                                                               transition-colors font-bold leading-none text-xs">×</button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Input row --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <input type="text" x-ref="codeInput" x-model="currentCode"
                                        @keydown.enter.prevent="addPair()" @keydown.tab.prevent="$refs.nameInput.focus()"
                                        placeholder="Position Code…"
                                        class="w-36 flex-shrink-0 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400" />
                                    <input type="text" x-ref="nameInput" x-model="currentName"
                                        @keydown.enter.prevent="addPair()" placeholder="Position…"
                                        class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400" />
                                    <button type="button" @click="addPair()"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-700
                                                                                       text-slate-600 dark:text-slate-300 text-sm font-semibold rounded-xl
                                                                                       hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-700 dark:hover:text-blue-300
                                                                                       border border-slate-200 dark:border-slate-600 transition-all active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Enter
                                    </button>
                                </div>

                                {{-- Hidden inputs for each staged pair --}}
                                <template x-for="(pair, idx) in pairs" :key="idx">
                                    <span>
                                        <input type="hidden" :name="'names[' + idx + ']'" :value="pair.name" />
                                        <input type="hidden" :name="'codes[' + idx + ']'" :value="pair.code" />
                                    </span>
                                </template>

                                {{-- Duplicate warning --}}
                                <p x-show="dupWarning" x-text="'⚠ Already staged: ' + dupWarning"
                                    class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                                <div class="flex items-center gap-3">
                                    <p class="text-xs text-slate-400 flex-1">
                                        Press <kbd
                                            class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                                        after each entry to stage it, then click <strong>Add</strong> to save all.
                                    </p>
                                    <button type="submit" :disabled="pairs.length === 0"
                                        :class="pairs.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-blue-700 active:scale-95 shadow shadow-blue-500/30'"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm
                                                                                       font-semibold rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add <span x-show="pairs.length > 0" x-text="'(' + pairs.length + ')'"
                                            class="ml-0.5"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search --}}
                        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ posSearch: '' }">
                            <input type="text" x-model="posSearch" placeholder="Search positions…"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />

                            {{-- Existing entries --}}
                            <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50"
                                x-data>
                                @forelse($positions as $pos)
                                    <div class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        x-show="posSearch === '' || '{{ addslashes(strtolower($pos->name)) }}'.includes(posSearch.toLowerCase()) || '{{ addslashes(strtolower($pos->code ?? '')) }}'.includes(posSearch.toLowerCase())"
                                        x-data="{ editing: false, editName: '{{ addslashes($pos->name) }}', editCode: '{{ addslashes($pos->code ?? '') }}' }">

                                        {{-- Display mode --}}
                                        <div x-show="!editing" class="flex items-center gap-2 flex-1 min-w-0 pr-3">
                                            @if($pos->code)
                                                <span
                                                    class="flex-shrink-0 text-xs font-mono font-semibold px-1.5 py-0.5 rounded
                                                                                                                                                 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">{{ $pos->code }}</span>
                                            @endif
                                            <span class="text-sm text-slate-700 dark:text-slate-300 truncate"
                                                x-text="editName"></span>
                                        </div>

                                        {{-- Edit mode --}}
                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.positions.update', $pos->id) }}"
                                            class="flex-1 flex items-center gap-2 pr-3" style="display:none">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="code" x-model="editCode" placeholder="Code"
                                                class="w-28 flex-shrink-0 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                                                                                                      bg-white dark:bg-slate-700 text-sm font-mono text-slate-800 dark:text-slate-100
                                                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                            <input type="text" name="name" x-model="editName"
                                                class="flex-1 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                                                                                                      bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                            <button type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
                                            <button type="button"
                                                @click="editing = false; editName = '{{ addslashes($pos->name) }}'; editCode = '{{ addslashes($pos->code ?? '') }}'"
                                                class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                                        </form>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                            {{-- Edit --}}
                                            <button type="button" @click="editing = true" title="Edit"
                                                class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-blue-500 dark:hover:text-blue-400
                                                                                                                       hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('admin.settings.positions.destroy', $pos->id) }}"
                                                onsubmit="return confirm('Delete position &quot;{{ addslashes($pos->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No positions yet. Add some above.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════
                    OFFICES
                    ═══════════════════════════════════ --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Offices</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $offices->count() }} {{ Str::plural('entry', $offices->count()) }}
                            </span>
                        </div>

                        {{-- Multi-entry chip input --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700" x-data="chipInput()">
                            <form method="POST" action="{{ route('admin.settings.offices.store') }}" @submit="prepareSubmit">
                                @csrf

                                <div class="w-full min-h-[44px] px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                bg-slate-50 dark:bg-slate-700 flex flex-wrap gap-1.5 items-center cursor-text mb-3"
                                    @click="$refs.chipInput.focus()">
                                    <template x-for="chip in chips" :key="chip">
                                        <span class="inline-flex items-center gap-1 pl-2.5 pr-1.5 py-1 rounded-lg
                                                                                         bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300
                                                                                         text-xs font-medium select-none">
                                            <span x-text="chip"></span>
                                            <button type="button" @click.stop="remove(chip)"
                                                class="w-3.5 h-3.5 rounded-full hover:bg-indigo-200 dark:hover:bg-indigo-800
                                                                                               flex items-center justify-center text-indigo-500 hover:text-red-500
                                                                                               transition-colors font-bold leading-none">×</button>
                                        </span>
                                    </template>
                                    <input type="text" x-ref="chipInput" x-model="current" @keydown.enter.prevent="add()"
                                        @keydown.","="add(); $event.preventDefault()"
                                        placeholder="Type an office name, press Enter to stage it…"
                                        class="flex-1 min-w-[200px] bg-transparent text-sm text-slate-800 dark:text-slate-100
                                                                                      outline-none placeholder-slate-400 py-0.5" />
                                </div>

                                <template x-for="chip in chips" :key="chip">
                                    <input type="hidden" name="names[]" :value="chip" />
                                </template>

                                <p x-show="dupWarning" x-text="'⚠ Already staged: ' + dupWarning"
                                    class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                                <div class="flex items-center gap-3">
                                    <p class="text-xs text-slate-400 flex-1">
                                        Press <kbd
                                            class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                                        after each entry, then click <strong>Add</strong> to save all.
                                    </p>
                                    <button type="submit" :disabled="chips.length === 0"
                                        :class="chips.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95 shadow shadow-indigo-500/30'"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm
                                                                                       font-semibold rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add <span x-show="chips.length > 0" x-text="'(' + chips.length + ')'"
                                            class="ml-0.5"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search + existing entries --}}
                        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ offSearch: '' }">
                            <input type="text" x-model="offSearch" placeholder="Search offices…"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />

                            <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50"
                                x-data>
                                @forelse($offices as $office)
                                    <div class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        x-show="offSearch === '' || '{{ addslashes(strtolower($office->name)) }}'.includes(offSearch.toLowerCase())"
                                        x-data="{ editing: false, editVal: '{{ addslashes($office->name) }}' }">

                                        <span class="text-sm text-slate-700 dark:text-slate-300 flex-1 truncate pr-3"
                                            x-show="!editing" x-text="editVal"></span>

                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.offices.update', $office->id) }}"
                                            class="flex-1 flex items-center gap-2 pr-3" style="display:none">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" x-model="editVal"
                                                class="flex-1 px-2.5 py-1 rounded-lg border border-indigo-300 dark:border-indigo-600
                                                                                                                      bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                      focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                            <button type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                                            <button type="button"
                                                @click="editing = false; editVal = '{{ addslashes($office->name) }}'"
                                                class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                                        </form>

                                        <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                            <button type="button" @click="editing = true" title="Edit"
                                                class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-indigo-500 dark:hover:text-indigo-400
                                                                                                                       hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.settings.offices.destroy', $office->id) }}"
                                                onsubmit="return confirm('Delete office &quot;{{ addslashes($office->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No offices yet. Add some above.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════
                    EMPLOYEES (admin org tab)
                    ═══════════════════════════════════ --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
                        x-data="employeeForm()" x-init="init()">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Added Employees</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $employees->count() }} {{ Str::plural('entry', $employees->count()) }}
                            </span>
                        </div>

                        {{-- Add Form --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                            <form method="POST" action="{{ route('admin.settings.employees.store') }}">
                                @csrf
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Add Employee</p>

                                {{-- Duplicate / general error banner --}}
                                @if ($errors->has('last_name'))
                                    <div
                                        class="mb-3 px-4 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                                        <p class="text-xs font-semibold text-red-600 dark:text-red-400">
                                            {{ $errors->first('last_name') }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Row 1: Employee Number, Last Name, Given Name --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Employee
                                            No. <span class="text-red-400">*</span></label>
                                        <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                                            placeholder="e.g. 2024-001" required
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('employee_number') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        @error('employee_number')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Last
                                            Name <span class="text-red-400">*</span></label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                                            placeholder="Dela Cruz" required
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Given
                                            Name <span class="text-red-400">*</span></label>
                                        <input type="text" name="given_name" value="{{ old('given_name') }}" placeholder="Juan"
                                            required
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                </div>

                                {{-- Row 2: Middle Name, Suffix, Office, Position --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Middle
                                            Name <span class="font-normal text-slate-400">(opt.)</span></label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                            placeholder="Santos"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Suffix
                                            <span class="font-normal text-slate-400">(opt.)</span></label>
                                        <input type="text" name="suffix" value="{{ old('suffix') }}" placeholder="Jr."
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div class="relative">
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Office
                                            <span class="text-red-400">*</span></label>
                                        <input type="text" name="office" id="admin-emp-add-office" value="{{ old('office') }}"
                                            list="admin-office-list" autocomplete="off" required placeholder="Type or select…"
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('office') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        <datalist id="admin-office-list">
                                            @foreach($offices as $office)
                                                <option value="{{ $office->name }}">
                                            @endforeach
                                        </datalist>
                                        @error('office')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="relative">
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Position
                                            <span class="text-red-400">*</span></label>
                                        <input type="text" name="position" id="admin-emp-add-position"
                                            value="{{ old('position') }}" list="admin-position-list" autocomplete="off" required
                                            placeholder="Type or select…"
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('position') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        <datalist id="admin-position-list">
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->name }}">
                                            @endforeach
                                        </datalist>
                                        @error('position')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm
                                                                                       font-semibold rounded-xl hover:bg-emerald-700 active:scale-95
                                                                                       shadow shadow-emerald-500/30 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Employee
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search + employee list --}}
                        <div class="px-6 py-3">
                            <input type="text" id="settingsEmpSearch" placeholder="Search employees…"
                                oninput="settingsEmpPaginator && settingsEmpPaginator.search(this.value)"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition mb-3" />

                            <div id="settingsEmpList" class="-mx-3 divide-y divide-slate-50 dark:divide-slate-700/50">
                                @forelse($employees as $emp)
                                    <div class="px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        data-search="{{ strtolower($emp->full_name . ' ' . $emp->employee_number . ' ' . $emp->position . ' ' . $emp->office) }}"
                                        x-data="{
                                                                                                         editing: false,
                                                                                                         editLast: '{{ addslashes($emp->last_name) }}',
                                                                                                         editGiven: '{{ addslashes($emp->given_name) }}',
                                                                                                         editMiddle: '{{ addslashes($emp->middle_name ?? '') }}',
                                                                                                         editSuffix: '{{ addslashes($emp->suffix ?? '') }}',
                                                                                                         editEmpNo: '{{ addslashes($emp->employee_number ?? '') }}',
                                                                                                         editOffice: '{{ addslashes($emp->office ?? '') }}',
                                                                                                         editPosition: '{{ addslashes($emp->position ?? '') }}',
                                                                                                     }">

                                        {{-- Display mode --}}
                                        <div x-show="!editing" class="flex items-center gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if($emp->employee_number)
                                                        <span
                                                            class="flex-shrink-0 text-xs font-mono font-semibold px-1.5 py-0.5 rounded
                                                                                                                                                         bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                                            {{ $emp->employee_number }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $emp->full_name }}</span>
                                                </div>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    @if($emp->position)
                                                        <span
                                                            class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $emp->position }}</span>
                                                    @endif
                                                    @if($emp->office)
                                                        <span class="text-xs text-slate-400 dark:text-slate-500 truncate">·
                                                            {{ $emp->office }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button type="button" @click="editing = true" title="Edit"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-emerald-500 dark:hover:text-emerald-400
                                                                                                                           hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('admin.settings.employees.destroy', $emp->id) }}"
                                                    onsubmit="return confirm('Delete employee &quot;{{ addslashes($emp->full_name) }}&quot;?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete"
                                                        class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Edit mode --}}
                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.employees.update', $emp->id) }}"
                                            class="mt-2 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 space-y-3"
                                            style="display:none">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Emp
                                                        No.</label>
                                                    <input type="text" name="employee_number" x-model="editEmpNo"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Last
                                                        Name <span class="text-red-400">*</span></label>
                                                    <input type="text" name="last_name" x-model="editLast" required
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Given
                                                        Name <span class="text-red-400">*</span></label>
                                                    <input type="text" name="given_name" x-model="editGiven" required
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Middle
                                                        Name</label>
                                                    <input type="text" name="middle_name" x-model="editMiddle"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Suffix</label>
                                                    <input type="text" name="suffix" x-model="editSuffix"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div class="relative">
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Office</label>
                                                    <input type="text" name="office" x-model="editOffice" list="admin-office-list"
                                                        autocomplete="off"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div class="relative">
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Position</label>
                                                    <input type="text" name="position" x-model="editPosition"
                                                        list="admin-position-list" autocomplete="off"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                              focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center gap-3 justify-end pt-2 border-t border-slate-200 dark:border-slate-600 mt-2">
                                                <button type="button"
                                                    @click="editing = false; editLast = '{{ addslashes($emp->last_name) }}'; editGiven = '{{ addslashes($emp->given_name) }}'; editMiddle = '{{ addslashes($emp->middle_name ?? '') }}'; editSuffix = '{{ addslashes($emp->suffix ?? '') }}'; editEmpNo = '{{ addslashes($emp->employee_number ?? '') }}'; editOffice = '{{ addslashes($emp->office ?? '') }}'; editPosition = '{{ addslashes($emp->position ?? '') }}'"
                                                    class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-sm shadow-emerald-500/30 transition-all">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No employees added yet. Use the form above to
                                        add one.</p>
                                @endforelse
                            </div>

                            {{-- ══ Pagination Bar ══ --}}
                            <div id="settingsEmpPaginationBar"
                                class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 mt-4 pt-3
                                                                            border-t border-slate-100 dark:border-slate-700 text-sm select-none">

                                {{-- Left: items per page --}}
                                <div
                                    class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium whitespace-nowrap">
                                    Items per page
                                    <div class="relative">
                                        <select id="settingsEmpPerPage"
                                            onchange="settingsEmpPaginator && settingsEmpPaginator.setPerPage(+this.value)"
                                            class="appearance-none pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                           bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold
                                                                                           focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <span id="settingsEmpRangeLabel" class="text-slate-400"></span>
                                </div>

                                {{-- Right: nav controls --}}
                                <div class="flex items-center gap-1">
                                    {{-- First --}}
                                    <button id="settingsEmpBtnFirst"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.first()" title="First page"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    {{-- Previous --}}
                                    <button id="settingsEmpBtnPrev"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.prev()" title="Previous page"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-emerald-600 dark:text-emerald-400
                                                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </button>
                                    {{-- Page input --}}
                                    <div class="flex items-center gap-1.5 px-1">
                                        <input id="settingsEmpPageInput" type="number" min="1"
                                            onchange="settingsEmpPaginator && settingsEmpPaginator.goTo(+this.value)"
                                            class="w-12 text-center rounded-lg border border-slate-200 dark:border-slate-600
                                                                                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                                                          text-xs font-semibold py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                        <span class="text-xs text-slate-400 whitespace-nowrap">of <span
                                                id="settingsEmpTotalPages">1</span></span>
                                    </div>
                                    {{-- Next --}}
                                    <button id="settingsEmpBtnNext"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.next()" title="Next page"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-emerald-600 dark:text-emerald-400
                                                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                                        Next
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    {{-- Last --}}
                                    <button id="settingsEmpBtnLast"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.last()" title="Last page"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            {{-- TAB: ORGANIZATION (employees only) --}}
            @if(Auth::user()->role !== 'admin')
                <div x-show="tab === 'org-employee'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                    class="space-y-6 max-w-4xl" style="display:none;">



                    {{-- ═══════════════════════════════════
                    EMPLOYEES
                    ═══════════════════════════════════ --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
                        x-data="employeeForm()" x-init="init()">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Added Employees</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $employees->count() }} {{ Str::plural('entry', $employees->count()) }}
                            </span>
                        </div>

                        {{-- Add Form --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                            <form method="POST" action="{{ route('admin.settings.employees.store') }}">
                                @csrf
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Add Employee</p>

                                {{-- Duplicate / general error banner --}}
                                @if ($errors->has('last_name'))
                                    <div
                                        class="mb-3 px-4 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700">
                                        <p class="text-xs font-semibold text-red-600 dark:text-red-400">
                                            {{ $errors->first('last_name') }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Row 1: Employee Number, Last Name, Given Name --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Employee
                                            No. <span class="text-red-400">*</span></label>
                                        <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                                            placeholder="e.g. 2024-001" required
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('employee_number') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        @error('employee_number')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Last
                                            Name <span class="text-red-400">*</span></label>
                                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                                            placeholder="Dela Cruz" required
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Given
                                            Name <span class="text-red-400">*</span></label>
                                        <input type="text" name="given_name" value="{{ old('given_name') }}" placeholder="Juan"
                                            required
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                </div>

                                {{-- Row 2: Middle Name, Suffix, Office, Position --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Middle
                                            Initial <span class="font-normal text-slate-400">(opt.)</span></label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                            placeholder="Santos"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Suffix
                                            <span class="font-normal text-slate-400">(opt.)</span></label>
                                        <input type="text" name="suffix" value="{{ old('suffix') }}" placeholder="Jr."
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                    </div>
                                    <div class="relative">
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Office
                                            <span class="text-red-400">*</span></label>
                                        <input type="text" name="office" id="emp-add-office-emp" value="{{ old('office') }}"
                                            list="office-list-emp" autocomplete="off" required placeholder="Type or select…"
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('office') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        <datalist id="office-list-emp">
                                            @foreach($offices as $office)
                                                <option value="{{ $office->name }}">
                                            @endforeach
                                        </datalist>
                                        @error('office')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="relative">
                                        <label
                                            class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Position
                                            <span class="text-red-400">*</span></label>
                                        <input type="text" name="position" id="emp-add-position-emp"
                                            value="{{ old('position') }}" list="position-list-emp" autocomplete="off" required
                                            placeholder="Type or select…"
                                            class="w-full px-3 py-2 rounded-xl border {{ $errors->has('position') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}
                                                                                          bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition placeholder-slate-400" />
                                        <datalist id="position-list-emp">
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->name }}">
                                            @endforeach
                                        </datalist>
                                        @error('position')
                                            <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm
                                                                                       font-semibold rounded-xl hover:bg-emerald-700 active:scale-95
                                                                                       shadow shadow-emerald-500/30 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Employee
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search + employee list --}}
                        <div class="px-6 py-3">
                            <input type="text" id="settingsEmpSearch" placeholder="Search employees…"
                                oninput="settingsEmpPaginator && settingsEmpPaginator.search(this.value)"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition mb-3" />

                            <div id="settingsEmpList" class="-mx-3 divide-y divide-slate-50 dark:divide-slate-700/50">
                                @forelse($employees as $emp)
                                    <div class="px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        data-search="{{ strtolower($emp->full_name . ' ' . $emp->employee_number . ' ' . $emp->position . ' ' . $emp->office) }}"
                                        x-data="{
                                                                                                     editing: false,
                                                                                                     editLast: '{{ addslashes($emp->last_name) }}',
                                                                                                     editGiven: '{{ addslashes($emp->given_name) }}',
                                                                                                     editMiddle: '{{ addslashes($emp->middle_name ?? '') }}',
                                                                                                     editSuffix: '{{ addslashes($emp->suffix ?? '') }}',
                                                                                                     editEmpNo: '{{ addslashes($emp->employee_number ?? '') }}',
                                                                                                     editOffice: '{{ addslashes($emp->office ?? '') }}',
                                                                                                     editPosition: '{{ addslashes($emp->position ?? '') }}',
                                                                                                 }">

                                        {{-- Display mode --}}
                                        <div x-show="!editing" class="flex items-center gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @if($emp->employee_number)
                                                        <span
                                                            class="flex-shrink-0 text-xs font-mono font-semibold px-1.5 py-0.5 rounded
                                                                                                                                                 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                                            {{ $emp->employee_number }}
                                                        </span>
                                                    @endif
                                                    <span
                                                        class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $emp->full_name }}</span>
                                                </div>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    @if($emp->position)
                                                        <span
                                                            class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $emp->position }}</span>
                                                    @endif
                                                    @if($emp->office)
                                                        <span class="text-xs text-slate-400 dark:text-slate-500 truncate">·
                                                            {{ $emp->office }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button type="button" @click="editing = true" title="Edit"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-emerald-500 dark:hover:text-emerald-400
                                                                                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('admin.settings.employees.destroy', $emp->id) }}"
                                                    onsubmit="return confirm('Delete employee &quot;{{ addslashes($emp->full_name) }}&quot;?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete"
                                                        class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Edit mode --}}
                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.employees.update', $emp->id) }}"
                                            class="mt-2 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 space-y-3"
                                            style="display:none">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Emp
                                                        No.</label>
                                                    <input type="text" name="employee_number" x-model="editEmpNo"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Last
                                                        Name <span class="text-red-400">*</span></label>
                                                    <input type="text" name="last_name" x-model="editLast" required
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Given
                                                        Name <span class="text-red-400">*</span></label>
                                                    <input type="text" name="given_name" x-model="editGiven" required
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Middle
                                                        Initial</label>
                                                    <input type="text" name="middle_name" x-model="editMiddle"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Suffix</label>
                                                    <input type="text" name="suffix" x-model="editSuffix"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div class="relative">
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Office</label>
                                                    <input type="text" name="office" x-model="editOffice" list="office-list-emp"
                                                        autocomplete="off"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                                <div class="relative">
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Position</label>
                                                    <input type="text" name="position" x-model="editPosition"
                                                        list="position-list-emp" autocomplete="off"
                                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                                                          bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center gap-3 justify-end pt-2 border-t border-slate-200 dark:border-slate-600 mt-2">
                                                <button type="button"
                                                    @click="editing = false; editLast = '{{ addslashes($emp->last_name) }}'; editGiven = '{{ addslashes($emp->given_name) }}'; editMiddle = '{{ addslashes($emp->middle_name ?? '') }}'; editSuffix = '{{ addslashes($emp->suffix ?? '') }}'; editEmpNo = '{{ addslashes($emp->employee_number ?? '') }}'; editOffice = '{{ addslashes($emp->office ?? '') }}'; editPosition = '{{ addslashes($emp->position ?? '') }}'"
                                                    class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-sm shadow-emerald-500/30 transition-all">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No employees added yet. Use the form above to
                                        add one.</p>
                                @endforelse
                            </div>

                            {{-- ══ Pagination Bar ══ --}}
                            <div id="settingsEmpPaginationBar"
                                class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 mt-4 pt-3
                                                                            border-t border-slate-100 dark:border-slate-700 text-sm select-none">

                                {{-- Left: items per page --}}
                                <div
                                    class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium whitespace-nowrap">
                                    Items per page
                                    <div class="relative">
                                        <select id="settingsEmpPerPage"
                                            onchange="settingsEmpPaginator && settingsEmpPaginator.setPerPage(+this.value)"
                                            class="appearance-none pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600
                                                                                           bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold
                                                                                           focus:outline-none focus:ring-2 focus:ring-emerald-500 transition cursor-pointer">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <span id="settingsEmpRangeLabel" class="text-slate-400"></span>
                                </div>

                                {{-- Right: nav controls --}}
                                <div class="flex items-center gap-1">
                                    {{-- First --}}
                                    <button id="settingsEmpBtnFirst"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.first()" title="First page"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    {{-- Previous --}}
                                    <button id="settingsEmpBtnPrev"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.prev()" title="Previous page"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-emerald-600 dark:text-emerald-400
                                                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Previous
                                    </button>
                                    {{-- Page input --}}
                                    <div class="flex items-center gap-1.5 px-1">
                                        <input id="settingsEmpPageInput" type="number" min="1"
                                            onchange="settingsEmpPaginator && settingsEmpPaginator.goTo(+this.value)"
                                            class="w-12 text-center rounded-lg border border-slate-200 dark:border-slate-600
                                                                                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                                                          text-xs font-semibold py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                                        <span class="text-xs text-slate-400 whitespace-nowrap">of <span
                                                id="settingsEmpTotalPages">1</span></span>
                                    </div>
                                    {{-- Next --}}
                                    <button id="settingsEmpBtnNext"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.next()" title="Next page"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-emerald-600 dark:text-emerald-400
                                                                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
                                        Next
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    {{-- Last --}}
                                    <button id="settingsEmpBtnLast"
                                        onclick="settingsEmpPaginator && settingsEmpPaginator.last()" title="Last page"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                                                       disabled:opacity-30 disabled:pointer-events-none transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════
                    POSITIONS (employee view)
                    ═══════════════════════════════════ --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Positions</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $positions->count() }} {{ Str::plural('entry', $positions->count()) }}
                            </span>
                        </div>

                        {{-- Multi-entry row input --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700" x-data="chipPairInput()">
                            <form method="POST" action="{{ route('admin.settings.positions.store') }}" @submit="prepareSubmit">
                                @csrf

                                {{-- Staged pairs --}}
                                <div class="space-y-1.5 mb-3" x-show="pairs.length > 0">
                                    <template x-for="(pair, idx) in pairs" :key="idx">
                                        <div
                                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                                                                                        bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                            <span
                                                class="text-xs font-mono font-semibold text-blue-500 dark:text-blue-400 min-w-[60px]"
                                                x-text="pair.code || '—'"></span>
                                            <span class="text-xs text-slate-700 dark:text-slate-200 flex-1"
                                                x-text="pair.name"></span>
                                            <button type="button" @click="removePair(idx)"
                                                class="w-4 h-4 rounded-full flex items-center justify-center
                                                                                               text-blue-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                                                                               transition-colors font-bold leading-none text-xs">×</button>
                                        </div>
                                    </template>
                                </div>

                                {{-- Input row --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <input type="text" x-ref="codeInput" x-model="currentCode"
                                        @keydown.enter.prevent="addPair()" @keydown.tab.prevent="$refs.nameInput.focus()"
                                        placeholder="Position Code…"
                                        class="w-36 flex-shrink-0 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400" />
                                    <input type="text" x-ref="nameInput" x-model="currentName"
                                        @keydown.enter.prevent="addPair()" placeholder="Position…"
                                        class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400" />
                                    <button type="button" @click="addPair()"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-700
                                                                                       text-slate-600 dark:text-slate-300 text-sm font-semibold rounded-xl
                                                                                       hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-700 dark:hover:text-blue-300
                                                                                       border border-slate-200 dark:border-slate-600 transition-all active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Enter
                                    </button>
                                </div>

                                {{-- Hidden inputs for each staged pair --}}
                                <template x-for="(pair, idx) in pairs" :key="idx">
                                    <span>
                                        <input type="hidden" :name="'names[' + idx + ']'" :value="pair.name" />
                                        <input type="hidden" :name="'codes[' + idx + ']'" :value="pair.code" />
                                    </span>
                                </template>

                                {{-- Duplicate warning --}}
                                <p x-show="dupWarning" x-text="'⚠ Already staged: ' + dupWarning"
                                    class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                                <div class="flex items-center gap-3">
                                    <p class="text-xs text-slate-400 flex-1">
                                        Press <kbd
                                            class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                                        after each entry to stage it, then click <strong>Add</strong> to save all.
                                    </p>
                                    <button type="submit" :disabled="pairs.length === 0"
                                        :class="pairs.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-blue-700 active:scale-95 shadow shadow-blue-500/30'"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm
                                                                                       font-semibold rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add <span x-show="pairs.length > 0" x-text="'(' + pairs.length + ')'"
                                            class="ml-0.5"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search + existing entries --}}
                        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ posSearch: '' }">
                            <input type="text" x-model="posSearch" placeholder="Search positions…"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />

                            <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50"
                                x-data>
                                @forelse($positions as $pos)
                                    <div class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        x-show="posSearch === '' || '{{ addslashes(strtolower($pos->name)) }}'.includes(posSearch.toLowerCase()) || '{{ addslashes(strtolower($pos->code ?? '')) }}'.includes(posSearch.toLowerCase())"
                                        x-data="{ editing: false, editName: '{{ addslashes($pos->name) }}', editCode: '{{ addslashes($pos->code ?? '') }}' }">

                                        {{-- Display mode --}}
                                        <div x-show="!editing" class="flex items-center gap-2 flex-1 min-w-0 pr-3">
                                            @if($pos->code)
                                                <span
                                                    class="flex-shrink-0 text-xs font-mono font-semibold px-1.5 py-0.5 rounded
                                                                                                                                         bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">{{ $pos->code }}</span>
                                            @endif
                                            <span class="text-sm text-slate-700 dark:text-slate-300 truncate"
                                                x-text="editName"></span>
                                        </div>

                                        {{-- Edit mode --}}
                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.positions.update', $pos->id) }}"
                                            class="flex-1 flex items-center gap-2 pr-3" style="display:none">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="code" x-model="editCode" placeholder="Code"
                                                class="w-28 flex-shrink-0 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                                                                                                  bg-white dark:bg-slate-700 text-sm font-mono text-slate-800 dark:text-slate-100
                                                                                                                  focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                            <input type="text" name="name" x-model="editName"
                                                class="flex-1 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                                                                                                  bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                  focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                            <button type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
                                            <button type="button"
                                                @click="editing = false; editName = '{{ addslashes($pos->name) }}'; editCode = '{{ addslashes($pos->code ?? '') }}'"
                                                class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                                        </form>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                            <button type="button" @click="editing = true" title="Edit"
                                                class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-blue-500 dark:hover:text-blue-400
                                                                                                                   hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.settings.positions.destroy', $pos->id) }}"
                                                onsubmit="return confirm('Delete position &quot;{{ addslashes($pos->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                       hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No positions yet. Add some above.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════
                    OFFICES (employee view)
                    ═══════════════════════════════════ --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Offices</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $offices->count() }} {{ Str::plural('entry', $offices->count()) }}
                            </span>
                        </div>

                        {{-- Multi-entry chip input --}}
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700" x-data="chipInput()">
                            <form method="POST" action="{{ route('admin.settings.offices.store') }}" @submit="prepareSubmit">
                                @csrf

                                <div class="w-full min-h-[44px] px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                                bg-slate-50 dark:bg-slate-700 flex flex-wrap gap-1.5 items-center cursor-text mb-3"
                                    @click="$refs.chipInput.focus()">
                                    <template x-for="chip in chips" :key="chip">
                                        <span class="inline-flex items-center gap-1 pl-2.5 pr-1.5 py-1 rounded-lg
                                                                                         bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300
                                                                                         text-xs font-medium select-none">
                                            <span x-text="chip"></span>
                                            <button type="button" @click.stop="remove(chip)"
                                                class="w-3.5 h-3.5 rounded-full hover:bg-indigo-200 dark:hover:bg-indigo-800
                                                                                               flex items-center justify-center text-indigo-500 hover:text-red-500
                                                                                               transition-colors font-bold leading-none">×</button>
                                        </span>
                                    </template>
                                    <input type="text" x-ref="chipInput" x-model="current" @keydown.enter.prevent="add()"
                                        @keydown.","="add(); $event.preventDefault()"
                                        placeholder="Type an office name, press Enter to stage it…"
                                        class="flex-1 min-w-[200px] bg-transparent text-sm text-slate-800 dark:text-slate-100
                                                                                      outline-none placeholder-slate-400 py-0.5" />
                                </div>

                                <template x-for="chip in chips" :key="chip">
                                    <input type="hidden" name="names[]" :value="chip" />
                                </template>

                                <p x-show="dupWarning" x-text="'⚠ Already staged: ' + dupWarning"
                                    class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                                <div class="flex items-center gap-3">
                                    <p class="text-xs text-slate-400 flex-1">
                                        Press <kbd
                                            class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                                        after each entry, then click <strong>Add</strong> to save all.
                                    </p>
                                    <button type="submit" :disabled="chips.length === 0"
                                        :class="chips.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95 shadow shadow-indigo-500/30'"
                                        class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm
                                                                                       font-semibold rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add <span x-show="chips.length > 0" x-text="'(' + chips.length + ')'"
                                            class="ml-0.5"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Search + existing entries --}}
                        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ offSearch: '' }">
                            <input type="text" x-model="offSearch" placeholder="Search offices…"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />

                            <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50"
                                x-data>
                                @forelse($offices as $office)
                                    <div class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                                        x-show="offSearch === '' || '{{ addslashes(strtolower($office->name)) }}'.includes(offSearch.toLowerCase())"
                                        x-data="{ editing: false, editVal: '{{ addslashes($office->name) }}' }">

                                        <span class="text-sm text-slate-700 dark:text-slate-300 flex-1 truncate pr-3"
                                            x-show="!editing" x-text="editVal"></span>

                                        <form x-show="editing" method="POST"
                                            action="{{ route('admin.settings.offices.update', $office->id) }}"
                                            class="flex-1 flex items-center gap-2 pr-3" style="display:none">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" x-model="editVal"
                                                class="flex-1 px-2.5 py-1 rounded-lg border border-indigo-300 dark:border-indigo-600
                                                                                                                  bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                                                                                                  focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                            <button type="submit"
                                                class="px-2.5 py-1 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                                            <button type="button"
                                                @click="editing = false; editVal = '{{ addslashes($office->name) }}'"
                                                class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                                        </form>

                                        <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                            <button type="button" @click="editing = true" title="Edit"
                                                class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-indigo-500 dark:hover:text-indigo-400
                                                                                                                   hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.settings.offices.destroy', $office->id) }}"
                                                onsubmit="return confirm('Delete office &quot;{{ addslashes($office->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"
                                                    class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400
                                                                                                                       hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">No offices yet. Add some above.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            {{-- TAB: ACCESS & SECURITY --}}
            <div x-show="tab === 'access' || tab === 'security'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-6 max-w-4xl" style="display:none;">

                @if(Auth::user()->role === 'admin')
                    {{-- ═══════════════════════════════════
                    USER ACCESS LEVEL
                    ═══════════════════════════════════ --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden"
                        x-data="userAccessLevel()" x-init="init()">

                        {{-- Header --}}
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">User Access Level</h2>
                            </div>
                            <span
                                class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                                {{ $allUsers->count() }}
                            </span>
                        </div>

                        {{-- Search --}}
                        <div class="px-6 py-3">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                                </svg>
                                <input type="text" x-model="search" @input="selectedUser = null; selectedRole = ''"
                                    placeholder="Search user by name…"
                                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                                                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                                                              text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 transition" />
                            </div>
                        </div>

                        {{-- User list --}}
                        <div class="px-6 pb-2 max-h-40 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <button type="button" @click="selectUser(user)"
                                    :class="selectedUser && selectedUser.id === user.id
                                                                            ? 'bg-violet-50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-700'
                                                                            : 'hover:bg-slate-50 dark:hover:bg-slate-700/30 border-transparent'"
                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl border transition-colors text-left">
                                    {{-- Avatar --}}
                                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold"
                                        :class="selectedUser && selectedUser.id === user.id
                                                                             ? 'bg-violet-600 text-white'
                                                                             : 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300'">
                                        <span x-text="user.initials"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate"
                                            x-text="user.display_name"></p>
                                        <p class="text-xs" :class="user.role === 'admin'
                                                                               ? 'text-violet-500 dark:text-violet-400'
                                                                               : 'text-slate-400 dark:text-slate-500'"
                                            x-text="user.role === 'admin' ? 'Super Admin' : 'Employee'"></p>
                                    </div>
                                    {{-- Current badge --}}
                                    <span x-show="selectedUser && selectedUser.id === user.id"
                                        class="flex-shrink-0 w-2 h-2 rounded-full bg-violet-500"></span>
                                </button>
                            </template>
                            <p x-show="filteredUsers.length === 0" class="text-xs text-slate-400 text-center py-4">
                                No users match your search.
                            </p>
                        </div>

                        {{-- Role assignment panel --}}
                        <div x-show="selectedUser !== null" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="px-6 py-4 border-t border-slate-100 dark:border-slate-700" style="display:none">

                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Access level for <span class="text-violet-600 dark:text-violet-400"
                                    x-text="selectedUser ? selectedUser.display_name : ''"></span>
                            </p>

                            {{-- Role option cards --}}
                            <div class="grid grid-cols-1 gap-2 mb-4">

                                {{-- Super Admin --}}
                                <label
                                    :class="selectedRole === 'admin'
                                                                                ? 'ring-2 ring-violet-500 border-violet-300 dark:border-violet-600 bg-violet-50 dark:bg-violet-900/20'
                                                                                : 'border-slate-200 dark:border-slate-600 hover:border-violet-300 dark:hover:border-violet-600 cursor-pointer'"
                                    class="flex items-start gap-2 p-3 rounded-xl border transition-all">
                                    <input type="radio" name="role_pick" value="admin" x-model="selectedRole"
                                        class="mt-0.5 accent-violet-600 w-4 h-4 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Super Admin</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 leading-snug">Full
                                            access. Can manage employees, settings, and all records.</p>
                                    </div>
                                </label>

                                {{-- Employee --}}
                                <label
                                    :class="selectedRole === 'employee'
                                                                                ? 'ring-2 ring-blue-500 border-blue-300 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                                                                                : 'border-slate-200 dark:border-slate-600 hover:border-blue-300 dark:hover:border-blue-600 cursor-pointer'"
                                    class="flex items-start gap-2 p-3 rounded-xl border transition-all">
                                    <input type="radio" name="role_pick" value="employee" x-model="selectedRole"
                                        class="mt-0.5 accent-blue-600 w-4 h-4 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Employee</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 leading-snug">Limited
                                            access. Can view and submit own records only.</p>
                                    </div>
                                </label>
                            </div>

                            {{-- Hidden form to submit role change --}}
                            <form id="roleForm" method="POST" :action="formAction">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" :value="selectedRole" />
                                <div class="flex items-center gap-3">
                                    <button type="submit"
                                        :disabled="!selectedRole || (selectedUser && selectedUser.role === selectedRole)"
                                        :class="(!selectedRole || (selectedUser && selectedUser.role === selectedRole))
                                                                                ? 'bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 cursor-not-allowed border border-slate-200 dark:border-slate-700'
                                                                                : 'bg-violet-600 text-white border border-transparent hover:bg-violet-700 active:scale-95 shadow shadow-violet-500/30'"
                                        class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl transition-all w-full justify-center">
                                        Save Action
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                @endif

                {{-- Change Password --}}
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 max-w-2xl">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Change Password</h2>
                    </div>

                    <form method="POST" action="{{ route('admin.settings.password') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')



                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">New
                                Password</label>
                            <input type="password" name="password" autocomplete="new-password"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Confirm
                                New Password</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700
                                                          text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white text-sm font-semibold shadow shadow-rose-500/30 transition-all">
                            Update Password
                        </button>
                    </form>
                </div>

            </div> <!-- End Access/Security Tab -->

            {{-- TAB: PREFERENCES --}}
            <div x-show="tab === 'preferences'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-6 max-w-2xl" style="display:none;">

                {{-- Appearance / Dark Mode --}}
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Appearance</h2>
                    </div>

                    <div x-data="{ dm: localStorage.getItem('darkMode') === 'true' }"
                        @click="dm = !dm; localStorage.setItem('darkMode', dm); document.documentElement.classList.toggle('dark', dm);"
                        class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 cursor-pointer select-none transition-colors hover:bg-slate-100 dark:hover:bg-slate-700">
                        <div class="flex items-center gap-3">
                            <span x-show="!dm">
                                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <span x-show="dm" style="display:none">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300"
                                    x-text="dm ? 'Dark Mode' : 'Light Mode'">Light Mode</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Click to toggle theme</p>
                            </div>
                        </div>
                        <div class="toggle-pill flex-shrink-0"
                            :class="dm ? 'bg-indigo-500 toggle-on' : 'bg-slate-300 dark:bg-slate-600'">
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400 dark:text-slate-500 leading-relaxed">
                        Your preference is saved locally and applied across all pages.
                    </p>
                </div>

            </div> <!-- End Preferences Tab -->

        </div> <!-- End Content Area -->

    </div> <!-- End Section layout -->

    <script>
        // ── Users data for access level panel (server-rendered JSON) ─────────────────
        @if(Auth::user()->role === 'admin')
            const _allUsers = @json($allUsers);
            const _userRoleBaseUrl = '{{ url('settings/users') }}';
        @endif

            // ── Employee form stub (card is server-rendered; no JS state needed) ──────────
            function employeeForm() {
                return {
                    init() { /* no-op */ }
                };
            }

        // ── Chip pair input (Position Code + Position) ────────────────────────────────
        function chipPairInput() {
            return {
                pairs: [],
                currentCode: '',
                currentName: '',
                dupWarning: '',

                addPair() {
                    const name = this.currentName.trim();
                    const code = this.currentCode.trim();
                    if (!name) {
                        this.$refs.nameInput.focus();
                        return;
                    }
                    if (this.pairs.some(p => p.name === name)) {
                        this.dupWarning = name;
                        setTimeout(() => this.dupWarning = '', 2000);
                        this.currentName = '';
                        this.currentCode = '';
                        return;
                    }
                    this.pairs.push({ code, name });
                    this.currentCode = '';
                    this.currentName = '';
                    this.dupWarning = '';
                    this.$refs.codeInput.focus();
                },

                removePair(idx) {
                    this.pairs.splice(idx, 1);
                },

                prepareSubmit(e) {
                    if (this.currentName.trim()) this.addPair();
                    if (this.pairs.length === 0) {
                        e.preventDefault();
                    }
                }
            };
        }

        // ── Chip input component (used by Offices) ────────────────────────────────────
        function chipInput() {
            return {
                chips: [],
                current: '',
                dupWarning: '',

                add() {
                    const val = this.current.trim();
                    if (!val) return;
                    if (this.chips.includes(val)) {
                        this.dupWarning = val;
                        setTimeout(() => this.dupWarning = '', 2000);
                        this.current = '';
                        return;
                    }
                    this.chips.push(val);
                    this.current = '';
                    this.dupWarning = '';
                },

                remove(chip) {
                    this.chips = this.chips.filter(c => c !== chip);
                },

                prepareSubmit(e) {
                    // If there's text in the input, add it as a chip first
                    if (this.current.trim()) this.add();
                    // Block submit if no chips
                    if (this.chips.length === 0) {
                        e.preventDefault();
                    }
                }
            };
        }

        // ── User Access Level component ───────────────────────────────────────────────
        function userAccessLevel() {
            return {
                users: [],
                search: '',
                selectedUser: null,
                selectedRole: '',
                formAction: '',

                init() {
                    this.users = _allUsers;
                },

                get filteredUsers() {
                    const q = this.search.trim().toLowerCase();
                    if (!q) return this.users;
                    return this.users.filter(u => u.search_str.includes(q));
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.selectedRole = user.role; // pre-select their current role
                    this.formAction = `${_userRoleBaseUrl}/${user.id}/role`;
                },
            };
        }
        document.addEventListener('DOMContentLoaded', function () {
            const listContainer = document.getElementById('settingsEmpList');
            if (!listContainer) return;

            const items = Array.from(listContainer.querySelectorAll('div[data-search]'));
            const perPageSelect = document.getElementById('settingsEmpPerPage');
            const rangeLabel = document.getElementById('settingsEmpRangeLabel');
            const totalPagesEl = document.getElementById('settingsEmpTotalPages');
            const pageInput = document.getElementById('settingsEmpPageInput');
            const btnFirst = document.getElementById('settingsEmpBtnFirst');
            const btnPrev = document.getElementById('settingsEmpBtnPrev');
            const btnNext = document.getElementById('settingsEmpBtnNext');
            const btnLast = document.getElementById('settingsEmpBtnLast');

            let currentPage = 1;
            let perPage = 25;
            let filteredItems = [...items];

            function render() {
                if (items.length === 0) return;

                const total = filteredItems.length;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                currentPage = Math.min(Math.max(1, currentPage), totalPages);

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                items.forEach(el => { el.style.display = 'none'; });
                filteredItems.forEach((el, i) => {
                    if (i >= start && i < end) el.style.display = '';
                });

                if (rangeLabel) {
                    const from = total === 0 ? 0 : start + 1;
                    const to = Math.min(start + perPage, total);
                    rangeLabel.textContent = `${from}–${to} of ${total} items`;
                }
                if (totalPagesEl) totalPagesEl.textContent = totalPages;
                if (pageInput) { pageInput.value = currentPage; pageInput.max = totalPages; }

                if (btnFirst) btnFirst.disabled = currentPage <= 1;
                if (btnPrev) btnPrev.disabled = currentPage <= 1;
                if (btnNext) btnNext.disabled = currentPage >= totalPages;
                if (btnLast) btnLast.disabled = currentPage >= totalPages;
            }

            window.settingsEmpPaginator = {
                search(q) {
                    const term = (q || '').toLowerCase().trim();
                    filteredItems = items.filter(el => {
                        return !term || (el.dataset.search || '').toLowerCase().includes(term);
                    });
                    currentPage = 1;
                    render();
                },
                setPerPage(n) { perPage = n; currentPage = 1; render(); },
                goTo(n) { currentPage = n; render(); },
                first() { currentPage = 1; render(); },
                prev() { currentPage--; render(); },
                next() { currentPage++; render(); },
                last() { currentPage = Math.max(1, Math.ceil(filteredItems.length / perPage)); render(); }
            };

            if (perPageSelect) {
                perPage = parseInt(perPageSelect.value) || 25;
            }
            render();
        });
    </script>

@endsection