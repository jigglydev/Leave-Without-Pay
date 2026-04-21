        {{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
             OFFICES
        â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">List of Offices</h2>
                </div>
                <span class="text-xs text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full font-medium">
                    {{ $offices->count() }} {{ Str::plural('entry', $offices->count()) }}
                </span>
            </div>

            {{-- Multi-entry chip input --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700"
                 x-data="chipInput()">
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
                                               transition-colors font-bold leading-none">Ã—</button>
                            </span>
                        </template>
                        <input type="text"
                               x-ref="chipInput"
                               x-model="current"
                               @keydown.enter.prevent="add()"
                               @keydown.","="add(); $event.preventDefault()"
                               placeholder="Type an office name, press Enter to stage itâ€¦"
                               class="flex-1 min-w-[200px] bg-transparent text-sm text-slate-800 dark:text-slate-100
                                      outline-none placeholder-slate-400 py-0.5"/>
                    </div>

                    <template x-for="chip in chips" :key="chip">
                        <input type="hidden" name="names[]" :value="chip"/>
                    </template>

                    <p x-show="dupWarning" x-text="'âš  Already staged: ' + dupWarning"
                       class="text-xs text-amber-600 dark:text-amber-400 mb-2 px-1" style="display:none"></p>

                    <div class="flex items-center gap-3">
                        <p class="text-xs text-slate-400 flex-1">
                            Press <kbd class="px-1 py-0.5 rounded bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 font-mono text-[10px]">Enter</kbd>
                            after each entry, then click <strong>Add</strong> to save all.
                        </p>
                        <button type="submit"
                                :disabled="chips.length === 0"
                                :class="chips.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-700 active:scale-95 shadow shadow-indigo-500/30'"
                                class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm
                                       font-semibold rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add <span x-show="chips.length > 0" x-text="'(' + chips.length + ')'" class="ml-0.5"></span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Search + existing entries --}}
            <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-700" x-data="{ offSearch: '' }">
                <input type="text" x-model="offSearch" placeholder="Search officesâ€¦"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100
                              text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"/>

                <div class="mt-3 max-h-64 overflow-y-auto -mx-3 divide-y divide-slate-50 dark:divide-slate-700/50" x-data>
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
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                                <button type="submit"
                                        class="px-2.5 py-1 text-xs font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                                <button type="button" @click="editing = false; editVal = '{{ addslashes($office->name) }}'"
                                        class="px-2.5 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">Cancel</button>
                            </form>

                            <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                                <button type="button" @click="editing = true" title="Edit"
                                        class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-indigo-500 dark:hover:text-indigo-400
                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('admin.settings.offices.destroy', $office->id) }}"
                                      onsubmit="return confirm('Delete office &quot;{{ addslashes($office->name) }}&quot;?')">
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
                        <p class="text-xs text-slate-400 text-center py-4">No offices yet. Add some above.</p>
                    @endforelse
                </div>
            </div>
        </div>
