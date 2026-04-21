<!DOCTYPE html>
<html lang="en" x-data="adminLayout()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin') — Leave Report System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Smooth sidebar & transitions */
        .sidebar-link { transition: all .18s ease; }
        .sidebar-link:hover .link-icon { transform: scale(1.15); }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148,163,184,.3); border-radius: 99px; }
        /* Toggle pill */
        .toggle-pill { width: 44px; height: 24px; border-radius: 999px; position: relative; transition: background .25s; }
        .toggle-pill::after { content:''; position:absolute; top:3px; left:3px; width:18px; height:18px; background:#fff; border-radius:50%; transition: transform .25s, box-shadow .25s; box-shadow: 0 1px 3px rgba(0,0,0,.3); }
        .toggle-on::after { transform: translateX(20px); }
    </style>
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">

{{-- ═══════════════════════════════════════════════════════════
     TOP NAVBAR
═══════════════════════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-50 h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm flex items-center px-4 gap-4">

    {{-- Sidebar toggle (mobile) --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Logo + Brand --}}
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 select-none">
        <img src="{{ asset('images/hrlogo.png') }}"
             alt="HR Logo"
             class="w-10 h-10 object-contain drop-shadow-sm"/>
        <div class="hidden sm:block">
            <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-widest leading-none">Province of Bukidnon</p>
            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight">Leave Report System</p>
        </div>
    </a>

    {{-- Spacer --}}
    <div class="flex-1"></div>

    {{-- User Dropdown --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" @click.outside="open = false"
                class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
            {{-- Avatar --}}
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <span class="hidden sm:block text-sm font-medium text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Dropdown Menu --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50"
             style="display:none; top: 100%;">

            {{-- User info header --}}
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                <p class="text-xs text-slate-500 dark:text-slate-400">Signed in as</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ Auth::user()->email }}</p>
            </div>

            {{-- Menu items --}}
            <div class="py-1">
                <a href="{{ route('admin.profile') }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
            </div>
        </div>
    </div>
</header>


{{-- ═══════════════════════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════════════════════ --}}
{{-- Overlay (mobile) --}}
<div x-show="sidebarOpen && window.innerWidth < 1024"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden"
     style="display:none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed top-16 left-0 bottom-0 z-40 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0">

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        @php
            $currentRoute = request()->routeIs('admin.dashboard') ? 'dashboard'
                : (request()->routeIs('admin.employees*') ? 'employees'
                : (request()->routeIs('admin.leaves*') ? 'leaves'
                : (request()->routeIs('admin.recorded-entries*') ? 'recorded-entries'
                : (request()->routeIs('admin.activity*') ? 'activity'
                : (request()->routeIs('admin.settings*') ? 'settings' : '')))));
        @endphp

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'dashboard'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </span>
            Dashboard
        </a>

        {{-- All Employees --}}
        <a href="{{ route('admin.employees') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'employees'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
            All Leave Processors
        </a>

        {{-- Leave Records --}}
        <a href="{{ route('admin.leaves') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'leaves'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            Leave Records
        </a>

        {{-- Recorded Entries --}}
        <a href="{{ route('admin.recorded-entries') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'recorded-entries'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            Recorded Entries
        </a>

        {{-- Activity Log (Admin only) --}}
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.activity') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'activity'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </span>
            Activity Log
        </a>
        @endif

        {{-- Settings --}}
        <a href="{{ route('admin.settings') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                  {{ $currentRoute === 'settings'
                      ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30'
                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
            <span class="link-icon w-5 h-5 flex-shrink-0 transition-transform duration-200">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
            Settings
        </a>
    </nav>

    {{-- ── Sidebar Logout ─────────────────────────────────────── --}}
    <div class="border-t border-slate-200 dark:border-slate-800 px-3 py-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all
                           text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                <span class="link-icon w-5 h-5 flex-shrink-0">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                Log Out
            </button>
        </form>
    </div>
</aside>


{{-- ═══════════════════════════════════════════════════════════
     MAIN CONTENT AREA
═══════════════════════════════════════════════════════════ --}}
<main class="lg:ml-64 pt-16 min-h-screen transition-all duration-300">
    <div class="p-6">
        @yield('content')
    </div>
</main>


<script>
function adminLayout() {
    return {
        sidebarOpen: false,
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        }
    }
}
</script>

@stack('scripts')
</body>
</html>
