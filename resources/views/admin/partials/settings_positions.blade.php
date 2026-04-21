        {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
             POSITIONS
        â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Positions</h2>
                </div>
                <span class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                    {{ $positions->count() }} {{ Str::plural('entry', $positions->count()) }}
                </span>
            </div>

            {{-- Multi-entry row input --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700"
                 x-data="chipPairInput()">
                <form method="POST" action="{{ route('admin.settings.positions.store') }}" @submit="prepareSubmit">
                    @csrf

                    {{-- Staged pairs --}}
                    <div class="space-y-1.5 mb-3" x-show="pairs.length > 0">
                        <template x-for="(pair, idx) in pairs" :key="idx">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                                        bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                <span class="text-xs font-mono font-semibold text-blue-500 dark:text-blue-400 min-w-[60px]" x-text="pair.code || 'â€”'"></span>
                                <span class="text-xs text-slate-700 dark:text-slate-200 flex-1" x-text="pair.name"></span>
                                <button type="button" @click="removePair(idx)"
                                        class="w-4 h-4 rounded-full flex items-center justify-center
                                               text-blue-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                               transition-colors font-bold leading-none text-xs">Ã—</button>
                            </div>
                        </template>
                    </div>

                    {{-- Input row --}}
                    <div class="flex items-center gap-2 mb-3">
                        <input type="text"
                               x-ref="codeInput"
                               x-model="currentCode"
                               @keydown.enter.prevent="addPair()"
                               @keydown.tab.prevent="$refs.nameInput.focus()"
                               placeholder="Position Codeâ€¦"
                               class="w-36 flex-shrink-0 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400"/>
                        <input type="text"
                               x-ref="nameInput"
                               x-model="currentName"
                               @keydown.enter.prevent="addPair()"
                               placeholder="Positionâ€¦"
                               class="flex-1 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                                      bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition placeholder-slate-400"/>
                        <button type="button" @click="addPair()"
                                class="flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-700
                                       text-slate-600 dark:text-slate-300 text-sm font-semibold rounded-xl
                                       hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:text-blue-700 dark:hover:text-blue-300
                                       border border-slate-200 dark:border-slate-600 transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Enter
                        </button>
                    </div>

                    {{-- Hidden inputs for each staged pair --}}
                    <template x-for="(pair, idx) in pairs" :key="idx">
                        <span>
                            <input type="hidden" :name="'names[' + idx + ']'" :value="pair.name"/>
                            <input type="hidden" :name="'codes[' + idx + ']'" :value="pair.code"/>
                        </span>
                    </template>

                    {{-- Duplicate warning --}}
                    <p x-show="dupWarning" x-text="'âš  Already staged: ' + dupWarning"
                       class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                    <div class="flex items-center gap-3">
                        <p class="text-xs text-slate-400 flex-1">
                            Press <kbd class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                            after each entry to stage it, then click <strong>Add</strong> to save all.
                        </p>
                        <button type="submit"
                                :disabled="pairs.length === 0"
                                :class="pairs.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-blue-700 active:scale-95 shadow shadow-blue-500/30'"
                                class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm
                                       font-semibold rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add <span x-show="pairs.length > 0" x-text="'(' + pairs.length + ')'" class="ml-0.5"></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Search --}}
            <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ posSearch: '' }">
                <input type="text" x-model="posSearch" placeholder="Search positionsâ€¦"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                              text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"/>

                {{-- Existing entries --}}
                <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50" x-data>
                    @forelse($positions as $pos)
                        <div class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors rounded-lg"
                         x-show="posSearch === '' || '{{ addslashes(strtolower($pos->name)) }}'.includes(posSearch.toLowerCase()) || '{{ addslashes(strtolower($pos->code ?? '')) }}'.includes(posSearch.toLowerCase())"
                             x-data="{ editing: false, editName: '{{ addslashes($pos->name) }}', editCode: '{{ addslashes($pos->code ?? '') }}' }">

                            {{-- Display mode --}}
                            <div x-show="!editing" class="flex items-center gap-2 flex-1 min-w-0 pr-3">
                                @if($pos->code)
                                    <span class="flex-shrink-0 text-xs font-mono font-semibold px-1.5 py-0.5 rounded
                                                 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">{{ $pos->code }}</span>
                                @endif
                                <span class="text-sm text-slate-700 dark:text-slate-300 truncate" x-text="editName"></span>
                            </div>

                            {{-- Edit mode --}}
                            <form x-show="editing" method="POST"
                                  action="{{ route('admin.settings.positions.update', $pos->id) }}"
                                  class="flex-1 flex items-center gap-2 pr-3" style="display:none">
                                @csrf
                                @method('PUT')
                                <input type="text" name="code" x-model="editCode"
                                       placeholder="Code"
                                       class="w-28 flex-shrink-0 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                              bg-white dark:bg-slate-700 text-sm font-mono text-slate-800 dark:text-slate-100
                                              focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                <input type="text" name="name" x-model="editName"
                                       class="flex-1 px-2.5 py-1 rounded-lg border border-blue-300 dark:border-blue-600
                                              bg-white dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                                              focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                <button type="submit"
                                        class="px-2.5 py-1 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
                                <button type="button" @click="editing = false; editName = '{{ addslashes($pos->name) }}'; editCode = '{{ addslashes($pos->code ?? '') }}'"
                                        class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                            </form>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                {{-- Edit --}}
                                <button type="button" @click="editing = true" title="Edit"
                                        class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-blue-500 dark:hover:text-blue-400
                                               hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
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

