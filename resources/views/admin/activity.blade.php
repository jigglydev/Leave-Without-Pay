@extends('admin.layout')
@section('title', 'Activity Log')
@section('content')

@php
    $activityTypes  = [
        'user_registered'        => 'New User',
        'registration_confirmed' => 'Confirmed User',
        'registration_rejected'  => 'Rejected User',
        'record_edited'          => 'Edit',
        'record_deleted'         => 'Deleted',
        'pdf_generated'          => 'PDF Generation',
        'role_updated'           => 'Role Change',
        'org_added'              => 'Org Added',
        'org_edited'             => 'Org Edited',
        'org_deleted'            => 'Org Deleted',
    ];
    $months = [
        1  => 'January',  2  => 'February', 3  => 'March',
        4  => 'April',    5  => 'May',       6  => 'June',
        7  => 'July',     8  => 'August',    9  => 'September',
        10 => 'October',  11 => 'November',  12 => 'December',
    ];
@endphp

{{-- ── Alpine root ───────────────────────────────────────────────────────────── --}}
<div x-data="activityFilter()" @keydown.escape.window="open = false">

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Activity Log</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Real-time record of system actions performed by users.</p>
    </div>

    <div class="flex items-center gap-3">
        {{-- Entry count --}}
        <span id="activityCountBadge" class="text-xs text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-full font-medium shadow-sm whitespace-nowrap">
            {{ $logs->count() }} {{ Str::plural('entry', $logs->count()) }}
        </span>

        {{-- Filter button --}}
        <button @click="open = !open"
                class="relative flex items-center gap-2 px-4 py-2.5 rounded-xl border transition-all
                       font-semibold text-sm shadow-sm select-none
                       bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600
                       text-slate-700 dark:text-slate-200
                       hover:border-rose-400 dark:hover:border-rose-500 hover:text-rose-600 dark:hover:text-rose-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 4a1 1 0 000 2h18a1 1 0 000-2H3zm3 6a1 1 0 000 2h12a1 1 0 000-2H6zm3 6a1 1 0 000 2h6a1 1 0 000-2H9z"/>
            </svg>
            Filter
            {{-- Active count badge --}}
            <span x-show="activeCount > 0" x-text="activeCount"
                  class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white
                         text-[10px] font-bold flex items-center justify-center leading-none" style="display:none">
            </span>
        </button>
    </div>
</div>

{{-- ── Slide-out filter drawer ─────────────────────────────────────────────── --}}
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
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
            <span x-show="activeCount > 0" x-text="activeCount" style="display:none"
                  class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] font-bold
                         flex items-center justify-center"></span>
        </div>
        <div class="flex items-center gap-3">
            <button x-show="activeCount > 0" @click="clearAll()" style="display:none"
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

    {{-- Drawer body — filter form --}}
    <div class="p-5 space-y-6">

        {{-- ── Name ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Name
                </div>
                <button x-show="name" @click="name=''; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="name"
                       @input="applyFilters()"
                       list="actor-names-list"
                       autocomplete="off"
                       placeholder="Search by name…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                              bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                              focus:outline-none focus:ring-2 focus:ring-rose-500 transition placeholder-slate-400">
                <datalist id="actor-names-list">
                    @foreach($actorNames as $n)
                        <option value="{{ $n }}">
                    @endforeach
                </datalist>
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
                <select x-model="month"
                        @change="applyFilters()"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               bg-slate-50 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100
                               focus:outline-none focus:ring-2 focus:ring-rose-500 transition appearance-none pr-9">
                    <option value="">All months</option>
                    @foreach($months as $num => $label)
                        <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- ── Activity Type (multi-select pills) ── --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Activity Type
                </div>
                <button x-show="types.length > 0" @click="types=[]; applyFilters()"
                        class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors" style="display:none">Clear</button>
            </div>
            <div class="flex flex-col gap-2">
                @php
                    $pillColorMap = [
                        'user_registered'        => ['ring' => 'ring-amber-400',   'bg' => 'bg-amber-500',   'dot' => 'bg-amber-400'],
                        'registration_confirmed' => ['ring' => 'ring-green-400',   'bg' => 'bg-green-500',   'dot' => 'bg-green-400'],
                        'registration_rejected'  => ['ring' => 'ring-red-400',     'bg' => 'bg-red-500',     'dot' => 'bg-red-400'],
                        'record_edited'          => ['ring' => 'ring-blue-400',    'bg' => 'bg-blue-500',    'dot' => 'bg-blue-400'],
                        'record_deleted'         => ['ring' => 'ring-slate-400',   'bg' => 'bg-slate-500',   'dot' => 'bg-slate-400'],
                        'pdf_generated'          => ['ring' => 'ring-rose-400',    'bg' => 'bg-rose-500',    'dot' => 'bg-rose-400'],
                        'role_updated'           => ['ring' => 'ring-slate-400',   'bg' => 'bg-slate-500',   'dot' => 'bg-slate-400'],
                        'org_added'              => ['ring' => 'ring-emerald-400', 'bg' => 'bg-emerald-500', 'dot' => 'bg-emerald-400'],
                        'org_edited'             => ['ring' => 'ring-blue-400',    'bg' => 'bg-blue-500',    'dot' => 'bg-blue-400'],
                        'org_deleted'            => ['ring' => 'ring-red-400',     'bg' => 'bg-red-500',     'dot' => 'bg-red-400'],
                    ];
                @endphp
                @foreach($activityTypes as $value => $label)
                @php
                    $pc = $pillColorMap[$value];
                @endphp
                <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer select-none transition-all
                              border"
                       :class="types.includes('{{ $value }}') 
                            ? 'border-transparent ring-2 {{ $pc['ring'] }} bg-slate-50 dark:bg-slate-700/60' 
                            : 'border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-500'">
                    <input type="checkbox" value="{{ $value }}"
                           class="sr-only"
                           @change="toggleType('{{ $value }}')"
                           :checked="types.includes('{{ $value }}')">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $pc['dot'] }}"></span>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200 flex-1">{{ $label }}</span>
                    <svg x-show="types.includes('{{ $value }}')" style="display:none" class="w-4 h-4 text-slate-500 dark:text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </label>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Backdrop --}}
<div x-show="open" @click="open = false"
     class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm"
     style="display:none"></div>


{{-- ── Flash message ────────────────────────────────────────────────────────── --}}
@if(session('status'))
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('status') }}
</div>
@endif

{{-- ── Log Entries ──────────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

    <div id="activityEmptyState" class="p-10 flex flex-col items-center justify-center text-center" style="{{ $logs->isEmpty() ? '' : 'display:none;' }}">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
            <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">No activity found</p>
        <p class="text-xs text-slate-400 mt-1">
            No logs match your current filters or there are no logs yet.
            <button type="button" @click="clearAll()" class="underline hover:text-slate-600 dark:hover:text-slate-200">Clear filters</button>
            to see all activity.
        </p>
    </div>

    @if(!$logs->isEmpty())
        <div id="activityLogList" class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($logs as $log)
            @php
                $badge = $log->badge();
                $colors = [
                    'rose'  => ['bg' => 'bg-rose-100 dark:bg-rose-900/30',   'text' => 'text-rose-600 dark:text-rose-400'],
                    'blue'  => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',   'text' => 'text-blue-600 dark:text-blue-400'],
                    'green' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400'],
                    'amber' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400'],
                    'red'   => ['bg' => 'bg-red-100 dark:bg-red-900/30',     'text' => 'text-red-600 dark:text-red-400'],
                    'slate' => ['bg' => 'bg-slate-100 dark:bg-slate-700',    'text' => 'text-slate-600 dark:text-slate-400'],
                ];
                $c = $colors[$badge['color']] ?? $colors['slate'];

                $registeredUser = null;
                if ($log->action === 'user_registered' && isset($log->meta['new_user_id'])) {
                    $registeredUser = \App\Models\User::find($log->meta['new_user_id']);
                }
            @endphp
            <div data-name="{{ $log->user ? strtolower($log->user->full_name) : 'system' }}" data-month="{{ $log->created_at->format('n') }}" data-type="{{ $log->action }}" class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">

                {{-- Icon badge --}}
                <div class="flex-shrink-0 mt-0.5">
                    <div class="w-9 h-9 rounded-xl {{ $c['bg'] }} flex items-center justify-center">
                        @if($badge['icon'] === 'document')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        @elseif($badge['icon'] === 'pencil')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        @elseif($badge['icon'] === 'trash')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        @elseif($badge['icon'] === 'user')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        @elseif($badge['icon'] === 'check')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        @elseif($badge['icon'] === 'x')
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        @else
                        <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @endif
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $c['bg'] }} {{ $c['text'] }}">
                            {{ $badge['label'] }}
                        </span>
                        @if($log->user)
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            by <span class="font-medium text-slate-700 dark:text-slate-200">{{ $log->user->full_name }}</span>
                        </span>
                        @else
                        <span class="text-xs text-slate-400">by <em>system</em></span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-700 dark:text-slate-200">
                        {{ $log->description }}
                        @if($log->action === 'pdf_generated')
                            @php
                                $email = $log->meta['employee_email'] ?? null;
                                if (!$email && isset($log->meta['leave_record_id'])) {
                                    $email = \App\Models\LeaveRecord::find($log->meta['leave_record_id'])?->user?->email;
                                }
                            @endphp
                            @if($email)
                                <span class="text-sm text-slate-500 dark:text-slate-400 ml-1">({{ $email }})</span>
                            @endif
                        @endif
                    </p>

                    {{-- Confirm / Reject actions for pending registrations --}}
                    @if($log->action === 'user_registered' && $registeredUser)
                        <div class="mt-2 flex items-center gap-2">
                            @if($registeredUser->isPending())
                                <span class="inline-flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 font-medium bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-full px-2.5 py-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pending approval
                                </span>
                                <form method="POST" action="{{ route('admin.activity.confirm', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        title="Confirm account — allow this employee to log in"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-green-500 hover:bg-green-600 active:bg-green-700 px-3 py-1 rounded-full shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Confirm
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.activity.reject', $log) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        title="Reject account — block this employee from logging in"
                                        onclick="return confirm('Reject this account? The employee will not be able to log in.')"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-white bg-red-500 hover:bg-red-600 active:bg-red-700 px-3 py-1 rounded-full shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject
                                    </button>
                                </form>
                            @elseif($registeredUser->isConfirmedAccount())
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 dark:text-green-300 font-semibold bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-full px-2.5 py-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Confirmed
                                </span>
                            @elseif($registeredUser->isRejected())
                                <span class="inline-flex items-center gap-1 text-xs text-red-700 dark:text-red-300 font-semibold bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-full px-2.5 py-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rejected
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Time --}}
                <div class="flex-shrink-0 text-right">
                    <p class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log->created_at->format('M d, Y') }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $log->created_at->format('h:i A') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ══ Activity Pagination Bar ══ --}}
<div id="activityPaginationBar"
     class="mt-0 bg-white dark:bg-slate-800 rounded-b-2xl border border-t-0 border-slate-200 dark:border-slate-700 shadow-sm
            flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-5 py-3.5 text-sm select-none">

    {{-- Left: items per page --}}
    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-medium whitespace-nowrap">
        Items per page
        <div class="relative">
            <select id="activityPerPage"
                    onchange="activityPaginator && activityPaginator.setPerPage(+this.value)"
                    class="appearance-none pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600
                           bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold
                           focus:outline-none focus:ring-2 focus:ring-rose-500 transition cursor-pointer">
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
        <span id="activityRangeLabel" class="text-slate-400"></span>
    </div>

    {{-- Right: nav controls --}}
    <div class="flex items-center gap-1">
        {{-- First --}}
        <button id="activityBtnFirst" onclick="activityPaginator && activityPaginator.first()"
                title="First page"
                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20
                       disabled:opacity-30 disabled:pointer-events-none transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
        {{-- Previous --}}
        <button id="activityBtnPrev" onclick="activityPaginator && activityPaginator.prev()"
                title="Previous page"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-rose-600 dark:text-rose-400
                       hover:bg-rose-50 dark:hover:bg-rose-900/20
                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Previous
        </button>
        {{-- Page input --}}
        <div class="flex items-center gap-1.5 px-1">
            <input id="activityPageInput" type="number" min="1"
                   onchange="activityPaginator && activityPaginator.goTo(+this.value)"
                   class="w-12 text-center rounded-lg border border-slate-200 dark:border-slate-600
                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                          text-xs font-semibold py-1.5 focus:outline-none focus:ring-2 focus:ring-rose-500 transition"/>
            <span class="text-xs text-slate-400 whitespace-nowrap">of <span id="activityTotalPages">1</span></span>
        </div>
        {{-- Next --}}
        <button id="activityBtnNext" onclick="activityPaginator && activityPaginator.next()"
                title="Next page"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-rose-600 dark:text-rose-400
                       hover:bg-rose-50 dark:hover:bg-rose-900/20
                       disabled:opacity-30 disabled:pointer-events-none transition text-xs font-semibold">
            Next
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        {{-- Last --}}
        <button id="activityBtnLast" onclick="activityPaginator && activityPaginator.last()"
                title="Last page"
                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20
                       disabled:opacity-30 disabled:pointer-events-none transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

</div>{{-- /alpine root --}}

<script>
function activityFilter() {
    return {
        open: false,
        name: '',
        month: '',
        types: [],

        get activeCount() {
            return (this.name ? 1 : 0) + (this.month ? 1 : 0) + (this.types.length > 0 ? 1 : 0);
        },

        toggleType(val) {
            const idx = this.types.indexOf(val);
            if (idx > -1) {
                this.types.splice(idx, 1);
            } else {
                this.types.push(val);
            }
            this.applyFilters();
        },

        applyFilters() {
            const rows = document.querySelectorAll('#activityLogList > div[data-name]');
            if (!rows.length) return;

            const term = this.name.toLowerCase().trim();
            const month = this.month;
            const types = this.types;
            
            const matchedRows = [];
            rows.forEach(row => {
                const rowName = row.dataset.name || '';
                const rowMonth = row.dataset.month || '';
                const rowType = row.dataset.type || '';

                const nameOk = !term || rowName.includes(term);
                const monthOk = !month || rowMonth === month;
                const typeOk = types.length === 0 || types.includes(rowType);

                if (nameOk && monthOk && typeOk) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            if (window.activityPaginator) {
                window.activityPaginator.resetWithItems(matchedRows);
            }

            const visible = matchedRows.length;
            
            const badge = document.getElementById('activityCountBadge');
            if (badge) {
                badge.textContent = visible + (visible === 1 ? ' entry' : ' entries');
            }
            
            const emptyState = document.getElementById('activityEmptyState');
            if (emptyState) emptyState.style.display = visible === 0 ? 'flex' : 'none';
        },

        clearAll() {
            this.name = '';
            this.month = '';
            this.types = [];
            this.applyFilters();
        }
    };
}

let activityPaginator = null;

function makeActivityPaginator({ getItems, rangeLabel, totalPagesEl, pageInput, btnFirst, btnPrev, btnNext, btnLast, defaultPerPage }) {
    let currentPage = 1;
    let perPage = defaultPerPage;
    let items = [];

    function render() {
        const total = items.length;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        currentPage = Math.min(Math.max(1, currentPage), totalPages);

        const start = (currentPage - 1) * perPage;
        const end   = start + perPage;

        items.forEach((el, i) => {
            el.style.display = (i >= start && i < end) ? '' : 'none';
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
        init() { items = Array.from(getItems()); render(); },
        resetWithItems(newItems) { items = Array.from(newItems); currentPage = 1; render(); },
        setPerPage(n) { perPage = n; currentPage = 1; render(); },
        goTo(n)       { currentPage = n; render(); },
        first()       { currentPage = 1; render(); },
        prev()        { currentPage--; render(); },
        next()        { currentPage++; render(); },
        last()        { currentPage = Math.max(1, Math.ceil(items.length / perPage)); render(); },
    };
}

document.addEventListener('DOMContentLoaded', function () {
    activityPaginator = makeActivityPaginator({
        getItems:      () => document.querySelectorAll('#activityLogList > div[data-name]'),
        rangeLabel:    document.getElementById('activityRangeLabel'),
        totalPagesEl:  document.getElementById('activityTotalPages'),
        pageInput:     document.getElementById('activityPageInput'),
        btnFirst:      document.getElementById('activityBtnFirst'),
        btnPrev:       document.getElementById('activityBtnPrev'),
        btnNext:       document.getElementById('activityBtnNext'),
        btnLast:       document.getElementById('activityBtnLast'),
        defaultPerPage: 25,
    });
    window.activityPaginator = activityPaginator;
    activityPaginator.init();
});
</script>

@endsection
