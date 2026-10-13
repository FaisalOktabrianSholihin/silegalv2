@php
    $u = auth()->user();
    $jenisList = config('dokumen.jenis');
    $aktifJenis = request()->route('jenis');
    $menuMaster = request()->routeIs('master.*', 'users.*');
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · SILEGAL v2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } } }</script>
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('preview', {
                show: false, url: '', raw: '', title: '', loading: false,
                open(o) {
                    this.url = o.url; this.raw = o.raw; this.title = o.title; this.show = true; this.loading = true;
                    if (o.id) fetch(@js(url('dokumen-preview')) + '/' + o.id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }).catch(() => {});
                },
                close() { this.show = false; this.url = ''; }
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.8/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        body{background:#f4f6fb}
        .nav-link{display:flex;align-items:center;gap:.75rem;border-radius:.75rem;padding:.55rem .75rem;font-size:.875rem;font-weight:500;color:#a5b4fc;transition:.15s}
        .nav-link:hover{background:rgba(255,255,255,.08);color:#fff}
        .nav-link.active{background:linear-gradient(90deg,rgba(99,102,241,.95),rgba(139,92,246,.85));color:#fff;box-shadow:0 8px 20px -8px rgba(99,102,241,.8)}
        .nav-label{padding:.25rem .75rem;margin:1.1rem 0 .35rem;font-size:.65rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#6366f1}
        .field{width:100%;border-radius:.75rem;border:1px solid #e2e8f0;background:#fff;padding:.65rem .9rem;font-size:.875rem;outline:none;transition:.15s}
        .field:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.12)}
        .field.err{border-color:#fb7185}
        .lbl{display:block;margin-bottom:.35rem;font-size:.8rem;font-weight:600;color:#334155}
        .sidebar-scroll::-webkit-scrollbar{width:6px}.sidebar-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,.15);border-radius:9px}
    </style>
</head>
<body class="h-full font-sans text-slate-800 antialiased"
      x-data="{ sidebar:false, master:{{ $menuMaster ? 'true' : 'false' }}, dok:{{ request()->routeIs('dokumen.*') ? 'true' : 'false' }}, userMenu:false }">

<div x-show="sidebar" x-cloak x-transition.opacity @click="sidebar=false" class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

{{-- ===== SIDEBAR ===== --}}
<aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col text-white transition-transform duration-200 lg:translate-x-0"
       :class="sidebar && '!translate-x-0'"
       style="background:linear-gradient(180deg,#0b1020 0%,#14124a 100%)">
    <div class="flex items-center gap-3 px-6 py-5">
        <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 shadow-lg shadow-indigo-500/40"><i data-lucide="scale" class="h-5 w-5"></i></div>
        <div class="leading-tight">
            <div class="text-lg font-extrabold tracking-wide">SILEGAL</div>
            <div class="text-[10px] text-indigo-300">Sistem Informasi Legal</div>
        </div>
        <span class="ml-auto rounded-full bg-emerald-400/15 px-2 py-0.5 text-[10px] font-bold text-emerald-300 ring-1 ring-emerald-300/40">v2.0</span>
    </div>

    <nav class="sidebar-scroll flex-1 overflow-y-auto px-4 pb-6">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i data-lucide="layout-dashboard" class="h-[18px] w-[18px]"></i>Dashboard</a>

        @if ($u->bisa('master.kelola') || $u->bisa('user.kelola'))
            <div class="nav-label">Master Data</div>
            <button @click="master=!master" class="nav-link w-full {{ $menuMaster ? 'text-white' : '' }}">
                <i data-lucide="database" class="h-[18px] w-[18px]"></i>Master Data
                <i data-lucide="chevron-down" class="ml-auto h-4 w-4 transition" :class="master && 'rotate-180'"></i>
            </button>
            <div x-show="master" x-collapse x-cloak class="ml-5 mt-1 space-y-1 border-l border-white/10 pl-3">
                @if ($u->bisa('master.kelola'))
                    <a href="{{ route('master.divisi.index') }}" class="nav-link {{ request()->routeIs('master.divisi.*') ? 'active' : '' }}"><i data-lucide="network" class="h-4 w-4"></i>Divisi</a>
                    <a href="{{ route('master.karyawan.index') }}" class="nav-link {{ request()->routeIs('master.karyawan.*') ? 'active' : '' }}"><i data-lucide="id-card" class="h-4 w-4"></i>Karyawan</a>
                @endif
                @if ($u->bisa('user.kelola'))
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i data-lucide="user-cog" class="h-4 w-4"></i>User</a>
                @endif
            </div>
        @endif

        <div class="nav-label">Dokumen</div>
        <button @click="dok=!dok" class="nav-link w-full {{ request()->routeIs('dokumen.*') ? 'text-white' : '' }}">
            <i data-lucide="folders" class="h-[18px] w-[18px]"></i>Dokumen
            <i data-lucide="chevron-down" class="ml-auto h-4 w-4 transition" :class="dok && 'rotate-180'"></i>
        </button>
        <div x-show="dok" x-collapse x-cloak class="ml-5 mt-1 space-y-1 border-l border-white/10 pl-3">
            @foreach ($jenisList as $slug => $j)
                <a href="{{ route('dokumen.index', $slug) }}" class="nav-link {{ $aktifJenis === $slug ? 'active' : '' }}"><i data-lucide="{{ $j['icon'] }}" class="h-4 w-4"></i>{{ $j['label'] }}</a>
            @endforeach
        </div>

        @if ($u->bisa('role.kelola') || $u->bisa('log.lihat'))
            <div class="nav-label">Administrasi</div>
            @if ($u->bisa('role.kelola'))
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"><i data-lucide="shield-check" class="h-[18px] w-[18px]"></i>Roles</a>
            @endif
            @if ($u->bisa('log.lihat'))
                <a href="{{ route('log.index') }}" class="nav-link {{ request()->routeIs('log.*') ? 'active' : '' }}"><i data-lucide="history" class="h-[18px] w-[18px]"></i>Log Aktivitas</a>
            @endif
        @endif
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 text-sm font-bold">{{ $u->inisial }}</div>
            <div class="min-w-0 leading-tight">
                <div class="truncate text-sm font-semibold">{{ $u->name }}</div>
                <div class="truncate text-[11px] text-indigo-300">{{ $u->role?->nama ?? 'Tanpa role' }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ===== KONTEN ===== --}}
<div class="lg:pl-72">
    <header class="sticky top-0 z-20 flex items-center gap-4 border-b border-slate-200/70 bg-white/80 px-4 py-3 backdrop-blur-xl sm:px-8">
        <button @click="sidebar=true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"><i data-lucide="menu" class="h-5 w-5"></i></button>
        <div class="min-w-0">
            <h1 class="truncate text-lg font-bold text-slate-900">@yield('title', 'Dashboard')</h1>
            <p class="hidden text-xs text-slate-500 sm:block">@yield('subtitle')</p>
        </div>

        <div class="relative ml-auto" @click.outside="userMenu=false">
            <button @click="userMenu=!userMenu" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 shadow-sm hover:border-indigo-300">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-bold text-white">{{ $u->inisial }}</span>
                <span class="hidden text-left leading-tight sm:block">
                    <span class="block text-sm font-semibold">{{ $u->name }}</span>
                    <span class="block text-[11px] text-slate-500">{{ $u->divisi?->nama_divisi ?? 'Semua Divisi' }}</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
            </button>
            <div x-show="userMenu" x-cloak x-transition class="absolute right-0 mt-2 w-60 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-100 px-4 py-3">
                    <div class="text-sm font-semibold">{{ $u->name }}</div>
                    <div class="truncate text-xs text-slate-500">{{ $u->email }}</div>
                    <span class="mt-2 inline-block rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-600">{{ $u->role?->nama }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="flex w-full items-center gap-2 px-4 py-3 text-sm font-medium text-rose-600 hover:bg-rose-50"><i data-lucide="log-out" class="h-4 w-4"></i>Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="px-4 py-6 sm:px-8">
        @yield('content')
        <p class="mt-10 text-center text-xs text-slate-400">SILEGAL v2.0 · © {{ date('Y') }}</p>
    </main>
</div>

{{-- ===== TOAST ===== --}}
@foreach (['sukses' => ['emerald', 'circle-check'], 'gagal' => ['rose', 'circle-alert']] as $key => [$warna, $ikon])
    @if (session($key))
        <div x-data="{ s:true }" x-init="setTimeout(()=>s=false,4500)" x-show="s" x-transition.opacity.duration.300ms x-cloak
             class="fixed right-5 top-5 z-[70] flex max-w-sm items-start gap-3 rounded-xl border border-{{ $warna }}-200 bg-white p-4 shadow-xl">
            <i data-lucide="{{ $ikon }}" class="mt-0.5 h-5 w-5 text-{{ $warna }}-500"></i>
            <p class="text-sm font-medium text-slate-700">{{ session($key) }}</p>
            <button @click="s=false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif
@endforeach

{{-- ===== MODAL PREVIEW PDF ===== --}}
<div x-data x-show="$store.preview.show" x-cloak x-transition.opacity @keydown.escape.window="$store.preview.close()"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/70 p-3 backdrop-blur-sm sm:p-8">
    <div @click.outside="$store.preview.close()" class="flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-3">
            <div class="grid h-9 w-9 place-items-center rounded-lg bg-rose-50 text-rose-600"><i data-lucide="file-text" class="h-5 w-5"></i></div>
            <div class="min-w-0">
                <div class="truncate text-sm font-bold" x-text="$store.preview.title"></div>
                <div class="text-[11px] text-slate-500">Pratinjau dokumen</div>
            </div>
            <a :href="$store.preview.raw" target="_blank" rel="noopener" class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"><i data-lucide="external-link" class="h-3.5 w-3.5"></i>Buka di tab baru</a>
            <button @click="$store.preview.close()" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">✕</button>
        </div>
        <div class="relative flex-1 bg-slate-100">
            <div x-show="$store.preview.loading" class="absolute inset-0 grid place-items-center text-sm text-slate-500">
                <div class="flex items-center gap-3"><svg class="h-5 w-5 animate-spin text-indigo-600" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>Memuat dokumen…</div>
            </div>
            <template x-if="$store.preview.show"><iframe :src="$store.preview.url" @load="$store.preview.loading=false" class="relative h-full w-full"></iframe></template>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-2 text-[11px] text-slate-500">Jika dokumen tidak tampil, pastikan akses link sudah dibagikan, atau gunakan tombol “Buka di tab baru”.</div>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
@stack('scripts')
</body>
</html>
